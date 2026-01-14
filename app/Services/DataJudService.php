<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataJudService
{
    protected $baseUrl;
    protected $token;

    public function __construct()
    {
        $this->baseUrl = config('services.datajud.base_url') ?: 'https://api-publica.datajud.cnj.jus.br';
        $this->token = config('services.datajud.token');
    }

    protected function endpointForTribunal(string $tribunal): string
    {
        $tribunal = strtolower($tribunal);
        return rtrim($this->baseUrl, '/') . "/api_publica_{$tribunal}/_search";
    }

    protected function headers(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($this->token) {
            $token = trim($this->token);
            // If token already contains a scheme (APIKey, Bearer, etc), use as-is.
            if (preg_match('/^(Bearer|APIKey)\s+/i', $token)) {
                $headers['Authorization'] = $token;
            } else {
                // Default to APIKey scheme used by DataJud public API
                $headers['Authorization'] = 'APIKey ' . $token;
            }
        }

        return $headers;
    }

    public function normalizeProcessNumber(?string $numero): ?string
    {
        if (! $numero) return null;
        // remove any non-digit or non-letter characters except dot and hyphen
        return preg_replace('/[^0-9A-Za-z\.\-]/', '', $numero);
    }

    /**
     * Return a list of known tribunals to iterate when searching all.
     * Keep this list in-sync with the view options.
     */
    public function tribunals(): array
    {
        return [
            'STF','STJ','TST',
            'TRF1','TRF2','TRF3','TRF4','TRF5','TRF6',
            'TJAC','TJAL','TJAP','TJAM','TJBA','TJCE','TJDFT','TJES',
            'TJGO','TJMA','TJMT','TJMS','TJMG','TJPB','TJPA','TJPR','TJPE',
            'TJPI','TJRJ','TJRN','TJRS','TJRO','TJRR','TJSC','TJSP','TJSE','TJTO'
        ];
    }

    /**
     * Search across all known tribunals and merge results.
     * This performs sequential requests and aggregates hits.
     */
    public function searchAll(string $tipo, string $valor, int $from = 0, int $size = 20)
    {
        $allHits = [];
        $total = 0;

        // Distribute size per tribunal to avoid huge responses per request.
        $tribunals = $this->tribunals();
        $perTribunal = max(1, (int) ceil($size / max(1, count($tribunals))));

        foreach ($tribunals as $tribunal) {
            if ($tipo === 'numero') {
                $resp = $this->searchByProcess($tribunal, $valor, 0, $perTribunal);
            } else {
                $resp = $this->searchByLawyer($tribunal, $valor, 0, $perTribunal);
            }

            if (empty($resp)) continue;

            $hits = $resp['hits']['hits'] ?? [];
            $count = $resp['hits']['total'] ?? (is_array($resp['hits'] ?? null) ? count($hits) : ($resp['hits']['total']['value'] ?? count($hits)));

            $total += is_numeric($count) ? (int)$count : count($hits);

            // annotate hit with tribunal for traceability
            foreach ($hits as $h) {
                if (is_array($h) && !isset($h['_tribunal'])) {
                    $h['_tribunal'] = $tribunal;
                }
                $allHits[] = $h;
            }

            // stop early if we already have enough
            if (count($allHits) >= $size) break;
        }

        // Trim to requested size
        $allHits = array_slice($allHits, 0, $size);

        return [
            'hits' => [
                'total' => $total,
                'hits' => $allHits,
            ]
        ];
    }

    public function searchByProcess(string $tribunal, string $numero, int $from = 0, int $size = 10)
    {
        $endpoint = $this->endpointForTribunal($tribunal);

        $body = [
            'from' => $from,
            'size' => $size,
            'query' => [
                'term' => [
                    'numeroProcesso' => $this->normalizeProcessNumber($numero)
                ]
            ]
        ];

        try {
            $resp = Http::withHeaders($this->headers())->post($endpoint, $body);
        } catch (\Exception $e) {
            Log::error('DataJud request failed: ' . $e->getMessage());
            return null;
        }

        if ($resp->failed()) {
            Log::warning('DataJud returned failed status', ['status' => $resp->status(), 'body' => $resp->body()]);
            return null;
        }

        return $resp->json();
    }

    public function searchByLawyer(string $tribunal, string $nome, int $from = 0, int $size = 10)
    {
        $endpoint = $this->endpointForTribunal($tribunal);

        $body = [
            'from' => $from,
            'size' => $size,
            'query' => [
                'match' => [
                    'partes.advogados.nome' => [
                        'query' => $nome,
                        'operator' => 'and'
                    ]
                ]
            ]
        ];

        try {
            $resp = Http::withHeaders($this->headers())->post($endpoint, $body);
        } catch (\Exception $e) {
            Log::error('DataJud request failed: ' . $e->getMessage());
            return null;
        }

        if ($resp->failed()) {
            Log::warning('DataJud returned failed status', ['status' => $resp->status(), 'body' => $resp->body()]);
            return null;
        }

        return $resp->json();
    }
}
