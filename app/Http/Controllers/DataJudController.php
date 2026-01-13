<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DataJudController extends Controller
{
    public function pesquisar(Request $request)
    {
        $request->validate([
            'tribunal' => 'required|string',
            'numero_processo' => 'nullable|required_without:nome_advogado|string',
            'nome_advogado' => 'nullable|required_without:numero_processo|string',
        ]);

        $tribunal = strtolower($request->tribunal);

        $endpoint = "https://api-publica.datajud.cnj.jus.br/api_publica_{$tribunal}/_search";

        /**
         * Montagem da query Elasticsearch
         */
        if ($request->filled('numero_processo')) {
            $query = [
                'query' => [
                    'term' => [
                        'numeroProcesso' => $request->numero_processo
                    ]
                ]
            ];
        } else {
            $query = [
                'query' => [
                    'match' => [
                        'partes.advogados.nome' => [
                            'query' => $request->nome_advogado,
                            'operator' => 'and'
                        ]
                    ]
                ]
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.datajud.token'),
            'Content-Type'  => 'application/json',
        ])->post($endpoint, $query);

        if ($response->failed()) {
            return back()->withErrors([
                'erro' => 'Erro ao consultar o DataJud'
            ]);
        }

        return view('datajud.resultado', [
            'resultados' => $response->json()['hits']['hits'] ?? []
        ]);
    }
}
