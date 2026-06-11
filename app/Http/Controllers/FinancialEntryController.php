<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Models\FinancialEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FinancialEntryController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        $query = $this->scopedEntriesQuery($actor)
            ->with('enterprise')
            ->when($actor->isAdmin() && $request->filled('enterprise_id'), function (Builder $builder) use ($request) {
                $builder->where('enterprise_id', $request->integer('enterprise_id'));
            })
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $builder->where('title', 'like', '%' . trim((string) $request->input('search')) . '%');
            })
            ->when($request->filled('entry_type'), function (Builder $builder) use ($request) {
                $builder->where('entry_type', $request->input('entry_type'));
            })
            ->when($request->filled('payment_method'), function (Builder $builder) use ($request) {
                $builder->where('payment_method', $request->input('payment_method'));
            });

        $receivableTotal = (clone $query)
            ->where('entry_type', FinancialEntry::TYPE_RECEIVABLE)
            ->sum('amount');

        $payableTotal = (clone $query)
            ->where('entry_type', FinancialEntry::TYPE_PAYABLE)
            ->sum('amount');

        $entries = $query
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('financial-entries.index', [
            'entries' => $entries,
            'entryTypeOptions' => FinancialEntry::entryTypeLabels(),
            'paymentMethodOptions' => FinancialEntry::paymentMethodLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
            'selectedEnterpriseId' => $request->integer('enterprise_id') ?: null,
            'filters' => [
                'search' => $request->input('search'),
                'entry_type' => $request->input('entry_type'),
                'payment_method' => $request->input('payment_method'),
            ],
            'summary' => [
                'receivable' => $receivableTotal,
                'payable' => $payableTotal,
                'balance' => $receivableTotal - $payableTotal,
            ],
        ]);
    }

    public function create(Request $request)
    {
        $actor = $request->user();

        return view('financial-entries.create', [
            'entryTypeOptions' => FinancialEntry::entryTypeLabels(),
            'paymentMethodOptions' => FinancialEntry::paymentMethodLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
            'selectedEnterpriseId' => $actor->isAdmin() ? old('enterprise_id') : $actor->enterprise_id,
        ]);
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        $validated = $request->validate($this->rules($actor));

        FinancialEntry::create([
            'enterprise_id' => $this->resolveEnterpriseId($actor, $validated['enterprise_id'] ?? null),
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'entry_type' => $validated['entry_type'],
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('financial-entries.index')
            ->with('success', 'Lancamento financeiro criado com sucesso.');
    }

    public function edit(Request $request, int $financialEntry)
    {
        $actor = $request->user();
        $entry = $this->scopedEntriesQuery($actor)->findOrFail($financialEntry);

        return view('financial-entries.edit', [
            'financialEntry' => $entry,
            'entryTypeOptions' => FinancialEntry::entryTypeLabels(),
            'paymentMethodOptions' => FinancialEntry::paymentMethodLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }

    public function update(Request $request, int $financialEntry)
    {
        $actor = $request->user();
        $entry = $this->scopedEntriesQuery($actor)->findOrFail($financialEntry);
        $validated = $request->validate($this->rules($actor));

        $entry->update([
            'enterprise_id' => $this->resolveEnterpriseId($actor, $validated['enterprise_id'] ?? $entry->enterprise_id),
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'entry_type' => $validated['entry_type'],
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('financial-entries.index')
            ->with('success', 'Lancamento financeiro atualizado com sucesso.');
    }

    public function destroy(Request $request, int $financialEntry)
    {
        $entry = $this->scopedEntriesQuery($request->user())->findOrFail($financialEntry);
        $entry->delete();

        return redirect()->route('financial-entries.index')
            ->with('success', 'Lancamento financeiro removido com sucesso.');
    }

    private function scopedEntriesQuery(User $actor): Builder
    {
        $query = FinancialEntry::query();

        if (! $actor->isAdmin()) {
            $query->where('enterprise_id', $actor->enterprise_id);
        }

        return $query;
    }

    private function rules(User $actor): array
    {
        return [
            'enterprise_id' => $actor->isAdmin()
                ? ['required', 'integer', 'exists:enterprises,id']
                : ['nullable'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'entry_type' => ['required', Rule::in(array_keys(FinancialEntry::entryTypeLabels()))],
            'payment_method' => ['required', Rule::in(array_keys(FinancialEntry::paymentMethodLabels()))],
        ];
    }

    private function resolveEnterpriseId(User $actor, ?int $enterpriseId): ?int
    {
        if ($actor->isAdmin()) {
            return $enterpriseId;
        }

        return $actor->enterprise_id;
    }
}
