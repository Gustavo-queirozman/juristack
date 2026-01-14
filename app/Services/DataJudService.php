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
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        return $headers;
    }

    public function normalizeProcessNumber(?string $numero): ?string
    {
        if (! $numero) return null;
        // remove any non-digit or non-letter characters except dot and hyphen
        return preg_replace('/[^0-9A-Za-z\.\-]/', '', $numero);
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
