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



public function salvarProcesso(Request $request, DatajudPersistService $persist)
{
    $request->validate([
        'tribunal' => 'required|string',
        'source' => 'required', // aceita array (AJAX) ou JSON string (form)
    ]);

    // garantir que temos um array em $source
    $source = $request->input('source');
    if (is_string($source)) {
        $decoded = json_decode($source, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $source = $decoded;
        }
    }

    if (!is_array($source)) {
        return response()->json(['error' => 'Campo source inválido'], 422);
    }

    // Garantir numeroProcesso (API pode retornar camelCase ou snake_case)
    if (empty($source['numeroProcesso']) && !empty($source['numero_processo'])) {
        $source['numeroProcesso'] = $source['numero_processo'];
    }

    if (empty($source['numeroProcesso'])) {
        return response()->json(['error' => 'Dados do processo incompletos (número não encontrado).'], 422);
    }

    try {
        // Salvar o processo
        $processo = $persist->salvarProcesso(
            $source,
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
                'numero_processo' => $source['numeroProcesso'] ?? null,
                'ultima_atualizacao_datajud' => $source['dataHoraUltimaAtualizacao'] ?? now(),
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
     * Atualizar processo salvo: reconsulta no DataJud e persiste alterações.
     */
    public function atualizarProcesso(Request $request, $id, DatajudPersistService $persist)
    {
        $processo = DatajudProcesso::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $tribunal = $processo->tribunal;
        $numero = $this->service->normalizeProcessNumber($processo->numero_processo);

        $resp = $tribunal === 'ALL'
            ? $this->service->searchAll('numero', $numero, 0, 5)
            : $this->service->searchByProcess($tribunal, $numero, 0, 1);

        if (empty($resp) || empty($resp['hits']['hits'])) {
            return response()->json(['error' => 'Processo não encontrado no DataJud no momento.'], 404);
        }

        $hits = $resp['hits']['hits'];
        $hit = null;
        foreach ($hits as $h) {
            $src = $h['_source'] ?? [];
            $num = $src['numeroProcesso'] ?? $src['numero_processo'] ?? null;
            if ($num && $this->service->normalizeProcessNumber($num) === $numero) {
                $hit = $h;
                break;
            }
        }
        if (! $hit) {
            $hit = $hits[0];
        }

        $source = $hit['_source'] ?? [];
        if (empty($source)) {
            return response()->json(['error' => 'Dados do processo indisponíveis.'], 502);
        }

        if (empty($source['numeroProcesso']) && ! empty($source['numero_processo'])) {
            $source['numeroProcesso'] = $source['numero_processo'];
        }

        try {
            $processoAtualizado = $persist->salvarProcesso($source, $tribunal, auth()->id());

            ProcessoMonitor::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'processo_id' => $processoAtualizado->id,
                ],
                [
                    'tribunal' => $tribunal,
                    'numero_processo' => $source['numeroProcesso'] ?? $source['numero_processo'] ?? null,
                    'ultima_atualizacao_datajud' => $source['dataHoraUltimaAtualizacao'] ?? now(),
                    'ativo' => true,
                ]
            );

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
