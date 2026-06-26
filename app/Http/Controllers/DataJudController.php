<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DatajudProcesso;
use App\Models\ProcessoMonitor;
use App\Models\User;
use App\Services\DataJudService;
use App\Services\DatajudPersistService;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class DataJudController extends Controller
{
    protected DataJudService $service;

    public function __construct(DataJudService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        return view('datajud.pesquisa', [
            'officeLawyers' => $this->availableResponsibleLawyersFor($request->user()),
        ]);
    }

    public function salvos(Request $request)
    {
        $query = $this->scopedProcessosQuery($request->user())
            ->with(['assuntos', 'movimentos', 'customer', 'responsibleLawyer']);

        $busca = $request->get('busca');
        if ($busca !== null && $busca !== '') {
            $buscaNormalizada = trim((string) $busca);
            $term = '%' . preg_replace('/\s+/', '%', $buscaNormalizada) . '%';
            $documento = $this->normalizeDocument($buscaNormalizada);

            $query->where(function (Builder $subQuery) use ($term, $documento) {
                $subQuery->where('numero_processo', 'like', $term)
                    ->orWhereHas('customer', function (Builder $customerQuery) use ($term, $documento) {
                        $customerQuery->where('name', 'like', $term);

                        if ($documento !== '') {
                            $customerQuery->orWhere('cnp', 'like', '%' . $documento . '%');
                        }
                    });
            });
        }

        $processos = $query->latest()->paginate(20)->withQueryString();

        return view('datajud.salvos', compact('processos', 'busca'));
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
            'resultados' => $resp['hits']['hits'] ?? [],
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
            'source' => 'required',
            'cpf_cliente' => 'required|string',
            'responsible_lawyer_user_id' => 'nullable|integer',
        ]);

        $source = $request->input('source');
        if (is_string($source)) {
            $decoded = json_decode($source, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $source = $decoded;
            }
        }

        if (! is_array($source)) {
            return response()->json(['error' => 'Campo source invalido.'], 422);
        }

        if (empty($source['numeroProcesso']) && ! empty($source['numero_processo'])) {
            $source['numeroProcesso'] = $source['numero_processo'];
        }

        if (empty($source['numeroProcesso'])) {
            return response()->json(['error' => 'Dados do processo incompletos: numero nao encontrado.'], 422);
        }

        $customerDocument = $this->normalizeDocument(
            $request->input('cpf_cliente')
                ?? $request->input('cpf')
                ?? $request->input('cnp')
                ?? data_get($source, 'cpf')
                ?? data_get($source, 'cnp')
        );

        if ($customerDocument === '') {
            return response()->json(['error' => 'Informe o CPF do cliente para vincular o processo.'], 422);
        }

        $customer = $this->scopedCustomersQuery($request->user())
            ->where('cnp', $customerDocument)
            ->first();

        if (! $customer) {
            return response()->json(['error' => 'Cliente nao encontrado para o CPF informado.'], 422);
        }

        try {
            $responsibleLawyer = $this->resolveResponsibleLawyerForCustomer(
                $request->user(),
                $customer,
                $request->integer('responsible_lawyer_user_id')
            );
        } catch (ValidationException $exception) {
            return response()->json([
                'error' => $exception->validator->errors()->first('responsible_lawyer_user_id'),
            ], 422);
        }

        $source['customer_id'] = $customer->id;

        try {
            $processo = $persist->salvarProcesso(
                $source,
                $request->tribunal,
                auth()->id()
            );

            $processoUpdates = [];
            if (Schema::hasColumn('datajud_processos', 'customer_id')) {
                $processoUpdates['customer_id'] = $customer->id;
            }
            if (Schema::hasColumn('datajud_processos', 'cliente_id')) {
                $processoUpdates['cliente_id'] = $customer->id;
            }
            if (Schema::hasColumn('datajud_processos', 'responsible_lawyer_user_id')) {
                $processoUpdates['responsible_lawyer_user_id'] = $responsibleLawyer?->id;
            }
            if ($processoUpdates !== []) {
                $processo->forceFill($processoUpdates)->save();
            }

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

            return response()->json([
                'ok' => true,
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'cnp' => $customer->cnp,
                ],
                'responsible_lawyer' => $responsibleLawyer ? [
                    'id' => $responsibleLawyer->id,
                    'name' => $responsibleLawyer->name,
                ] : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showSaved($id)
    {
        $processo = $this->scopedProcessosQuery(request()->user())
            ->with(['assuntos', 'movimentos.complementos', 'customer', 'responsibleLawyer'])
            ->where('id', $id)
            ->firstOrFail();

        return view('datajud.salvo', compact('processo'));
    }

    public function atualizarProcesso(Request $request, $id, DatajudPersistService $persist)
    {
        $processo = $this->scopedProcessosQuery($request->user())
            ->where('id', $id)
            ->firstOrFail();

        $tribunal = $processo->tribunal;
        $numero = $this->service->normalizeProcessNumber($processo->numero_processo);

        $resp = $tribunal === 'ALL'
            ? $this->service->searchAll('numero', $numero, 0, 5)
            : $this->service->searchByProcess($tribunal, $numero, 0, 1);

        if (empty($resp) || empty($resp['hits']['hits'])) {
            return response()->json(['error' => 'Processo nao encontrado no DataJud no momento.'], 404);
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
            return response()->json(['error' => 'Dados do processo indisponiveis.'], 502);
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

    public function deleteSaved(Request $request, $id)
    {
        $processo = $this->scopedProcessosQuery($request->user())
            ->with('movimentos.complementos', 'assuntos')
            ->where('id', $id)
            ->firstOrFail();

        ProcessoMonitor::where('processo_id', $processo->id)->delete();

        foreach ($processo->movimentos as $mov) {
            $mov->complementos()->delete();
            $mov->delete();
        }

        $processo->assuntos()->delete();
        $processo->delete();

        if ($request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('datajud.salvos')->with('status', 'Processo removido com sucesso.');
    }

    private function scopedCustomersQuery(User $user): Builder
    {
        $query = Customer::query();

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function scopedProcessosQuery(User $user): Builder
    {
        $query = DatajudProcesso::query();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->enterprise_id) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        if ($user->hasRole(User::ROLE_LAWYER)) {
            $query->where(function (Builder $builder) use ($user) {
                $builder->where('responsible_lawyer_user_id', $user->id)
                    ->orWhere('user_id', $user->id);
            });
        }

        return $query;
    }

    private function availableResponsibleLawyersFor(User $user, ?int $enterpriseId = null): Collection
    {
        $enterpriseId = $enterpriseId ?? $user->enterprise_id;

        if (! $enterpriseId) {
            return collect();
        }

        return User::query()
            ->where('enterprise_id', $enterpriseId)
            ->where('role', User::ROLE_LAWYER)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'oab_state', 'oab_number']);
    }

    private function resolveResponsibleLawyerForCustomer(User $actor, Customer $customer, ?int $lawyerId): ?User
    {
        $lawyers = $this->availableResponsibleLawyersFor($actor, $customer->enterprise_id);

        if ($lawyers->isEmpty()) {
            if ($lawyerId) {
                throw ValidationException::withMessages([
                    'responsible_lawyer_user_id' => 'Nao foi encontrado um advogado ativo neste escritorio.',
                ]);
            }

            return null;
        }

        if (! $lawyerId) {
            throw ValidationException::withMessages([
                'responsible_lawyer_user_id' => 'Selecione o advogado responsavel do escritorio para este processo.',
            ]);
        }

        $lawyer = $lawyers->firstWhere('id', $lawyerId);

        if (! $lawyer) {
            throw ValidationException::withMessages([
                'responsible_lawyer_user_id' => 'Selecione um advogado valido do mesmo escritorio do cliente.',
            ]);
        }

        return $lawyer;
    }

    private function normalizeDocument(?string $document): string
    {
        return preg_replace('/\D/', '', (string) $document);
    }
}
