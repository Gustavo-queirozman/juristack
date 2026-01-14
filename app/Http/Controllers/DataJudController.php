<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataJudService;

class DataJudController extends Controller
{
    protected $service;

    public function __construct(DataJudService $service)
    {
        $this->service = $service;
    }

    public function pesquisar(Request $request)
    {
        $request->validate([
            'tribunal' => 'required|string',
            'numero_processo' => 'nullable|required_without:nome_advogado|string',
            'nome_advogado' => 'nullable|required_without:numero_processo|string',
        ]);

        $tribunal = $request->tribunal;

        if ($request->filled('numero_processo')) {
            $numero = $this->service->normalizeProcessNumber($request->numero_processo);
            $resp = $this->service->searchByProcess($tribunal, $numero, 0, 20);
        } else {
            $resp = $this->service->searchByLawyer($tribunal, $request->nome_advogado, 0, 20);
        }

        if (empty($resp)) {
            return back()->withErrors(['erro' => 'Erro ao consultar o DataJud ou retorno vazio']);
        }

        return view('datajud.resultado', [
            'resultados' => $resp['hits']['hits'] ?? []
        ]);
    }

    public function apiSearch(Request $request)
    {
        $request->validate([
            'tribunal' => 'required|string',
            'numero_processo' => 'nullable|required_without:nome_advogado|string',
            'nome_advogado' => 'nullable|required_without:numero_processo|string',
            'from' => 'nullable|integer|min:0',
            'size' => 'nullable|integer|min:1|max:100',
        ]);

        $tribunal = $request->tribunal;
        $from = $request->get('from', 0);
        $size = $request->get('size', 10);

        if ($request->filled('numero_processo')) {
            $numero = $this->service->normalizeProcessNumber($request->numero_processo);
            $resp = $this->service->searchByProcess($tribunal, $numero, $from, $size);
        } else {
            $resp = $this->service->searchByLawyer($tribunal, $request->nome_advogado, $from, $size);
        }

        if (empty($resp)) {
            return response()->json(['error' => 'Erro ao consultar o DataJud'], 502);
        }

        return response()->json($resp);
    }
}
