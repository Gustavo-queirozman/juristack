<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataJudService
{
    protected $baseUrl;
    protected $token;
    protected $debug = false;

    public function __construct()
    {
        $this->baseUrl = config('services.datajud.base_url') ?: 'https://api-publica.datajud.cnj.jus.br';
        $this->token = config('services.datajud.token');
        $this->debug = config('services.datajud.debug', env('DATAJUD_DEBUG', false));
    }

    protected function endpointForTribunal(string $tribunal): string
    {
        $endpoint = rtrim($this->baseUrl, '/') . "/api_publica_" . strtolower($tribunal) . "/_search";

        Log::debug('DataJud endpoint resolvido', [
            'tribunal' => $tribunal,
            'endpoint' => $endpoint,
        ]);

        return $endpoint;
    }

    protected function headers(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
        ];

        if ($this->token) {
            $token = trim($this->token);
            $headers['Authorization'] = preg_match('/^(Bearer|APIKey)\s+/i', $token)
                ? $token
                : 'APIKey ' . $token;
        }

        return $headers;
    }

    public function normalizeProcessNumber(?string $numero): ?string
    {
        if (! $numero) return null;

        $normalized = preg_replace('/[^0-9A-Za-z\.\-]/', '', $numero);

        Log::debug('DataJud normalizeProcessNumber', [
            'original' => $numero,
            'normalized' => $normalized,
        ]);

        return $normalized;
    }

    public function searchAll(string $tipo, string $valor, int $from = 0, int $size = 20)
    {
        Log::info('DataJud searchAll iniciado', compact('tipo', 'valor', 'from', 'size'));

        $t0 = microtime(true);
        $allHits = [];
        $total = 0;

        $tribunals = $this->tribunals();
        $perTribunal = max(1, (int) ceil($size / max(1, count($tribunals))));

        foreach ($tribunals as $tribunal) {
            Log::debug('DataJud searchAll consultando tribunal', [
                'tribunal' => $tribunal,
                'tipo' => $tipo,
            ]);

            $resp = $tipo === 'numero'
                ? $this->searchByProcess($tribunal, $valor, 0, $perTribunal)
                : $this->searchByLawyer($tribunal, $valor, 0, $perTribunal);

            if (empty($resp)) {
                Log::warning('DataJud resposta vazia', ['tribunal' => $tribunal]);
                continue;
            }

            $hits = $resp['hits']['hits'] ?? [];
            $count = $resp['hits']['total']['value'] ?? count($hits);

            Log::debug('DataJud hits por tribunal', [
                'tribunal' => $tribunal,
                'hits_count' => count($hits),
                'total_reportado' => $count,
            ]);

            $total += (int) $count;

            foreach ($hits as $h) {
                $h['_tribunal'] = $tribunal;
                $allHits[] = $h;
            }

            if (count($allHits) >= $size) break;
        }

        $durationMs = (int) round((microtime(true) - $t0) * 1000);

        Log::info('DataJud searchAll finalizado', [
            'hits_retornados' => count($allHits),
            'total' => $total,
            'duration_ms' => $durationMs,
        ]);

        return [
            'hits' => [
                'total' => $total,
                'hits' => array_slice($allHits, 0, $size),
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

        Log::info('DataJud searchByProcess', [
            'tribunal' => $tribunal,
            'numero' => $numero,
            'from' => $from,
            'size' => $size,
        ]);

        if ($this->debug) {
            Log::debug('DataJud request body (process)', $body);
        }

        try {
            $resp = Http::withHeaders($this->headers())->post($endpoint, $body);
        } catch (\Throwable $e) {
            Log::error('DataJud HTTP exception (process)', [
                'tribunal' => $tribunal,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        if ($resp->failed()) {
            Log::warning('DataJud HTTP failed (process)', [
                'tribunal' => $tribunal,
                'status' => $resp->status(),
                'body' => $resp->body(),
            ]);
            return null;
        }

        if ($this->debug) {
            Log::debug('DataJud response (process)', [
                'status' => $resp->status(),
                'hits' => $resp->json('hits.total'),
            ]);
        }

        return $resp->json();
    }

    public function searchByLawyer(string $tribunal, string $nome, int $from = 0, int $size = 10)
    {
        $endpoint = $this->endpointForTribunal($tribunal);

        Log::info('DataJud searchByLawyer', compact('tribunal', 'nome', 'from', 'size'));

        $fields = [
            'partes.advogados.nome^3',
            'partes.advogados.nomeCompleto',
            'partes.advogados.nomeAdvogado',
            'partes.advogados.*'
        ];

        $body = [
            'from' => $from,
            'size' => $size,
            'query' => [
                'bool' => [
                    'should' => [
                        ['multi_match' => [
                            'query' => $nome,
                            'type' => 'phrase_prefix',
                            'operator' => 'and',
                            'fields' => $fields,
                        ]],
                        ['multi_match' => [
                            'query' => $nome,
                            'type' => 'best_fields',
                            'fuzziness' => 'AUTO',
                            'operator' => 'and',
                            'fields' => $fields,
                        ]]
                    ]
                ]
            ]
        ];

        if ($this->debug) {
            Log::debug('DataJud request body (lawyer)', $body);
        }

        try {
            $resp = Http::withHeaders($this->headers())->post($endpoint, $body);
        } catch (\Throwable $e) {
            Log::error('DataJud HTTP exception (lawyer)', [
                'tribunal' => $tribunal,
                'error' => $e->getMessage(),
            ]);
            return null;
        }

        if ($resp->failed()) {
            Log::warning('DataJud HTTP failed (lawyer)', [
                'tribunal' => $tribunal,
                'status' => $resp->status(),
            ]);
            return null;
        }

        if ($this->debug) {
            Log::debug('DataJud response (lawyer)', [
                'status' => $resp->status(),
                'hits' => $resp->json('hits.total'),
            ]);
        }

        return $resp->json();
    }
}
