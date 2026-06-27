<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\FinancialEntry;
use App\Models\FinancialEntryPayment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BankStatementImportService
{
    public function import(string $path, string $originalName, Collection $entries): array
    {
        $transactions = $this->parseTransactions($path, $originalName);
        $matched = 0;
        $unmatched = 0;
        $ambiguous = 0;

        foreach ($transactions as $transaction) {
            if ($transaction['amount'] <= 0) {
                continue;
            }

            $match = $this->matchEntry($transaction, $entries);

            if ($match['status'] === 'matched') {
                /** @var FinancialEntry $entry */
                $entry = $match['entry'];
                $paymentAmount = min($transaction['amount'], $entry->remainingAmount());

                $entry->payments()->create([
                    'amount' => $paymentAmount,
                    'payment_date' => $transaction['date']->toDateString(),
                    'source' => FinancialEntryPayment::SOURCE_BANK_IMPORT,
                    'reference' => $transaction['reference'] ?: $originalName,
                    'notes' => 'Pagamento conciliado por importacao bancaria.',
                    'imported_payload' => $transaction['raw'],
                ]);

                $entry->payments_sum_amount = $entry->paidAmount() + $paymentAmount;
                $matched++;
                continue;
            }

            if ($match['status'] === 'ambiguous') {
                $ambiguous++;
                continue;
            }

            $unmatched++;
        }

        return [
            'parsed' => count($transactions),
            'matched' => $matched,
            'unmatched' => $unmatched,
            'ambiguous' => $ambiguous,
        ];
    }

    private function parseTransactions(string $path, string $originalName): array
    {
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if ($extension === 'ofx') {
            return $this->parseOfx($path);
        }

        return $this->parseCsv($path);
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'rb');

        if (! $handle) {
            return [];
        }

        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);

            return [];
        }

        $delimiter = $this->detectDelimiter($firstLine);
        rewind($handle);

        $rows = [];
        $header = fgetcsv($handle, 0, $delimiter) ?: [];
        $headerMap = $this->normalizeHeaderMap($header);
        $hasNamedColumns = $this->hasKnownColumns($headerMap);

        if (! $hasNamedColumns) {
            rewind($handle);
        }

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($row === [null] || $row === []) {
                continue;
            }

            $dateValue = $hasNamedColumns
                ? $this->columnValue($row, $headerMap, ['data', 'date', 'data_pagamento', 'posted_at'])
                : ($row[0] ?? null);
            $description = $hasNamedColumns
                ? $this->columnValue($row, $headerMap, ['descricao', 'description', 'historico', 'memo'])
                : ($row[1] ?? null);
            $amountValue = $hasNamedColumns
                ? $this->columnValue($row, $headerMap, ['valor', 'amount', 'credito', 'valor_credito', 'valor_pago'])
                : ($row[2] ?? null);
            $reference = $hasNamedColumns
                ? $this->columnValue($row, $headerMap, ['documento', 'reference', 'identificador', 'fitid'])
                : ($row[3] ?? null);

            $date = $this->parseDate($dateValue);
            $amount = $this->parseAmount($amountValue);

            if (! $date || $amount === null) {
                continue;
            }

            $rows[] = [
                'date' => $date,
                'amount' => $amount,
                'description' => trim((string) $description),
                'reference' => trim((string) $reference),
                'raw' => [
                    'date' => $dateValue,
                    'amount' => $amountValue,
                    'description' => $description,
                    'reference' => $reference,
                ],
            ];
        }

        fclose($handle);

        return $rows;
    }

    private function parseOfx(string $path): array
    {
        $contents = file_get_contents($path);

        if ($contents === false) {
            return [];
        }

        preg_match_all('/<STMTTRN>(.*?)<\/STMTTRN>/si', $contents, $matches);

        $transactions = [];

        foreach ($matches[1] ?? [] as $chunk) {
            $dateRaw = $this->extractOfxValue($chunk, 'DTPOSTED');
            $amountRaw = $this->extractOfxValue($chunk, 'TRNAMT');
            $description = $this->extractOfxValue($chunk, 'MEMO') ?: $this->extractOfxValue($chunk, 'NAME');
            $reference = $this->extractOfxValue($chunk, 'FITID') ?: $this->extractOfxValue($chunk, 'CHECKNUM');

            $date = $this->parseDate($dateRaw);
            $amount = $this->parseAmount($amountRaw);

            if (! $date || $amount === null) {
                continue;
            }

            $transactions[] = [
                'date' => $date,
                'amount' => $amount,
                'description' => trim((string) $description),
                'reference' => trim((string) $reference),
                'raw' => [
                    'date' => $dateRaw,
                    'amount' => $amountRaw,
                    'description' => $description,
                    'reference' => $reference,
                ],
            ];
        }

        return $transactions;
    }

    private function matchEntry(array $transaction, Collection $entries): array
    {
        $openEntries = $entries
            ->filter(fn (FinancialEntry $entry) => $entry->remainingAmount() > 0.0)
            ->values();

        if ($openEntries->isEmpty()) {
            return ['status' => 'unmatched'];
        }

        $scored = $openEntries
            ->map(function (FinancialEntry $entry) use ($transaction) {
                return [
                    'entry' => $entry,
                    'score' => $this->scoreEntryMatch($entry, $transaction),
                ];
            })
            ->filter(fn (array $item) => $item['score'] > 0)
            ->sortByDesc('score')
            ->values();

        if ($scored->isEmpty()) {
            return ['status' => 'unmatched'];
        }

        $topScore = $scored[0]['score'];

        if ($topScore < 25) {
            return ['status' => 'unmatched'];
        }

        $topMatches = $scored->filter(fn (array $item) => $item['score'] === $topScore)->values();

        if ($topMatches->count() > 1) {
            return ['status' => 'ambiguous'];
        }

        return [
            'status' => 'matched',
            'entry' => $topMatches[0]['entry'],
        ];
    }

    private function scoreEntryMatch(FinancialEntry $entry, array $transaction): int
    {
        $remaining = $entry->remainingAmount();
        $amount = (float) $transaction['amount'];

        if ($amount > $remaining + 0.01) {
            return 0;
        }

        $score = abs($remaining - $amount) < 0.01 ? 30 : 10;
        $haystack = $this->normalizeText(($transaction['description'] ?? '') . ' ' . ($transaction['reference'] ?? ''));

        if ($haystack === '') {
            return $score;
        }

        $customer = $entry->customer;

        if ($customer instanceof Customer) {
            $document = preg_replace('/\D/', '', (string) $customer->cnp);

            if ($document !== '' && str_contains(preg_replace('/\D/', '', $haystack), $document)) {
                $score += 40;
            }

            if ($this->textContainsTokens($haystack, $customer->name)) {
                $score += 30;
            }
        }

        if ($this->textContainsTokens($haystack, $entry->title)) {
            $score += 20;
        }

        $days = abs($entry->entry_date?->diffInDays($transaction['date']) ?? 999);

        if ($days <= 3) {
            $score += 10;
        } elseif ($days <= 10) {
            $score += 5;
        }

        return $score;
    }

    private function textContainsTokens(string $haystack, ?string $value): bool
    {
        $tokens = collect(explode(' ', $this->normalizeText((string) $value)))
            ->filter(fn (string $token) => mb_strlen($token) >= 4)
            ->values();

        if ($tokens->isEmpty()) {
            return false;
        }

        return $tokens->contains(fn (string $token) => str_contains($haystack, $token));
    }

    private function normalizeText(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^\pL\pN\s]/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/', ' ', $value) ?? '';

        return trim($value);
    }

    private function detectDelimiter(string $line): string
    {
        $delimiters = [';', ',', "\t"];
        $bestDelimiter = ';';
        $bestCount = -1;

        foreach ($delimiters as $delimiter) {
            $count = substr_count($line, $delimiter);

            if ($count > $bestCount) {
                $bestDelimiter = $delimiter;
                $bestCount = $count;
            }
        }

        return $bestDelimiter;
    }

    private function normalizeHeaderMap(array $header): array
    {
        $map = [];

        foreach ($header as $index => $column) {
            $normalized = str_replace(' ', '_', $this->normalizeText((string) $column));
            $map[$normalized] = $index;
        }

        return $map;
    }

    private function hasKnownColumns(array $headerMap): bool
    {
        return collect(['data', 'date', 'valor', 'amount'])
            ->contains(fn (string $column) => array_key_exists($column, $headerMap));
    }

    private function columnValue(array $row, array $headerMap, array $aliases): ?string
    {
        foreach ($aliases as $alias) {
            if (array_key_exists($alias, $headerMap)) {
                return isset($row[$headerMap[$alias]]) ? (string) $row[$headerMap[$alias]] : null;
            }
        }

        return null;
    }

    private function parseDate(?string $value): ?Carbon
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $formats = [
            'd/m/Y' => 10,
            'Y-m-d' => 10,
            'd-m-Y' => 10,
            'Ymd' => 8,
        ];

        foreach ($formats as $format => $length) {
            try {
                return Carbon::createFromFormat($format, substr($value, 0, $length))->startOfDay();
            } catch (\Throwable) {
            }
        }

        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseAmount(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $normalized = str_replace(['R$', ' '], '', $value);

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            $normalized = str_replace('.', '', $normalized);
            $normalized = str_replace(',', '.', $normalized);
        } elseif (str_contains($normalized, ',')) {
            $normalized = str_replace(',', '.', $normalized);
        }

        if (! is_numeric($normalized)) {
            return null;
        }

        return round((float) $normalized, 2);
    }

    private function extractOfxValue(string $contents, string $tag): ?string
    {
        if (! preg_match('/<' . preg_quote($tag, '/') . '>([^<\r\n]+)/i', $contents, $matches)) {
            return null;
        }

        return trim($matches[1]);
    }
}
