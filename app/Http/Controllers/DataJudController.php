<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataJudService;
use App\Services\DatajudPersistService;
use App\Models\DatajudProcesso;
use App\Models\ProcessoMonitor;
class DataJudController extends Controller
{
    protected $service;

    public function __construct(DataJudService $service)
    {
        $this->service = $service;
    }

public function salvos()
{
    $processos = DatajudProcesso::with('assuntos')
        ->where('user_id', auth()->id())
        ->latest()
        ->paginate(20);

    return view('datajud.salvos', compact('processos'));
}

public function monitorados()
{
    return view('datajud.monitorados');
}

    public function pesquisar(Request $request)
    {
        $request->validate([
            'tribunal' => 'required|string',
            'numero_processo' => 'nullable|required_without:nome_advogado|string',
            'nome_advogado' => 'nullable|required_without:numero_processo|string',
        ]);

        $tribunal = $request->tribunal;

        if ($tribunal === 'ALL') {
            if ($request->filled('numero_processo')) {
                $numero = $this->service->normalizeProcessNumber($request->numero_processo);
                $resp = $this->service->searchAll('numero', $numero, 0, 20);
            } else {
                $resp = $this->service->searchAll('advogado', $request->nome_advogado, 0, 20);
            }
        } else {
            if ($request->filled('numero_processo')) {
                $numero = $this->service->normalizeProcessNumber($request->numero_processo);
                $resp = $this->service->searchByProcess($tribunal, $numero, 0, 20);
            } else {
                $resp = $this->service->searchByLawyer($tribunal, $request->nome_advogado, 0, 20);
            }
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

        if ($tribunal === 'ALL') {
            if ($request->filled('numero_processo')) {
                $numero = $this->service->normalizeProcessNumber($request->numero_processo);
                $resp = $this->service->searchAll('numero', $numero, $from, $size);
            } else {
                $resp = $this->service->searchAll('advogado', $request->nome_advogado, $from, $size);
            }
        } else {
            if ($request->filled('numero_processo')) {
                $numero = $this->service->normalizeProcessNumber($request->numero_processo);
                $resp = $this->service->searchByProcess($tribunal, $numero, $from, $size);
            } else {
                $resp = $this->service->searchByLawyer($tribunal, $request->nome_advogado, $from, $size);
            }
        }

        if (empty($resp)) {
            return response()->json(['error' => 'Erro ao consultar o DataJud'], 502);
        }

        return response()->json($resp);
    }

    /**
     * Debug endpoint for quick manual tests. Returns raw response and logs.
     * Accessible via GET /api/datajud/debug?tribunal=ALL&tipo=advogado&valor=Nelson%20Mannrich
     */
    public function debug(Request $request)
    {
        $tribunal = $request->get('tribunal', 'ALL');
        $tipo = $request->get('tipo', 'advogado'); // 'numero' or 'advogado'
        $valor = $request->get('valor', 'Nelson Mannrich');
        $from = (int) $request->get('from', 0);
        $size = (int) $request->get('size', 10);

        $resp = $this->service->debugSearch($tribunal, $tipo, $valor, $from, $size);

        if (empty($resp)) {
            return response()->json(['error' => 'No response or request failed'], 502);
        }

        return response()->json($resp);
    }

public function salvarProcesso(Request $request, DatajudPersistService $persist)
{
    $request->validate([
        'tribunal' => 'required|string',
        'source' => 'required|array',
    ]);

    try {
        // Salvar o processo
        $processo = $persist->salvarProcesso(
            $request->source,
            $request->tribunal,
            auth()->id()
        );

        // Criar monitor para este processo
        ProcessoMonitor::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'processo_id' => $processo->id,
            ],
            [
                'tribunal' => $request->tribunal,
                'numero_processo' => $request->source['numeroProcesso'] ?? null,
                'ultima_atualizacao_datajud' => $request->source['dataHoraUltimaAtualizacao'] ?? now(),
                'ativo' => true,
            ]
        );

        return response()->json(['ok' => true]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Mostrar detalhe de um processo salvo
     */
    public function showSaved($id)
    {
        $processo = DatajudProcesso::with(['assuntos', 'movimentos.complementos'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('datajud.salvo', compact('processo'));
    }

    /**
     * Remover processo salvo (apenas proprietário)
     */
    public function deleteSaved(Request $request, $id)
    {
        $processo = DatajudProcesso::with('movimentos.complementos', 'assuntos')
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // remover monitor associado
        ProcessoMonitor::where('processo_id', $processo->id)->delete();

        // remover movimentos e complementos
        foreach ($processo->movimentos as $mov) {
            $mov->complementos()->delete();
            $mov->delete();
        }

        // remover assuntos
        $processo->assuntos()->delete();

        // remover o processo
        $processo->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('datajud.salvos')->with('status', 'Processo removido com sucesso.');
    }

}
