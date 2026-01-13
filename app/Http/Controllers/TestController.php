<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(){
	$request->validate([
    'tribunal' => 'required',
    'tipo_pesquisa' => 'required|in:processo,advogado',
    'numero_processo' => 'required_if:tipo_pesquisa,processo',
    'nome_advogado' => 'required_if:tipo_pesquisa,advogado',
]);

	
$response = Http::withHeaders([
    'Authorization' => 'cDZHYzlZa0JadVREZDJCendQbXY6SkJlTzNjLV9TRENyQk1RdnFKZGRQdw==',
    'Content-Type'  => 'application/json',
])->post(
    'https://api-publica.datajud.cnj.jus.br/api_publica_trf1/_search',
    [
        'query' => [
            'match' => [
                'numeroProcesso' => '00008323520184013202',
            ],
        ],
    ]
);

// Corpo da resposta
$data = $response->json();

// Debug
dd($data);    
}
}
