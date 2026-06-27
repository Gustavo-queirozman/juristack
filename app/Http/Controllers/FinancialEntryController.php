<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Enterprise;
use App\Models\FinancialEntry;
use App\Models\FinancialEntryPayment;
use App\Models\User;
use App\Services\BankStatementImportService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FinancialEntryController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        $filteredQuery = $this->applyFilters($request, $this->scopedEntriesQuery($actor), $actor);

        $receivableTotal = (clone $filteredQuery)
            ->where('entry_type', FinancialEntry::TYPE_RECEIVABLE)
            ->sum('amount');

        $payableTotal = (clone $filteredQuery)
            ->where('entry_type', FinancialEntry::TYPE_PAYABLE)
            ->sum('amount');

        $summaryEntryIds = (clone $filteredQuery)->pluck('financial_entries.id');

        $receivedTotal = FinancialEntryPayment::query()
            ->whereIn('financial_entry_id', $summaryEntryIds)
            ->sum('amount');

        $entries = $this->decorateEntriesQuery(clone $filteredQuery)
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('financial-entries.index', [
            'entries' => $entries,
            'entryTypeOptions' => FinancialEntry::entryTypeLabels(),
            'paymentMethodOptions' => FinancialEntry::paymentMethodLabels(),
            'paymentStatusOptions' => FinancialEntry::paymentStatusLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
            'customers' => $this->availableCustomersFor($actor, $request->integer('enterprise_id') ?: null),
            'selectedEnterpriseId' => $request->integer('enterprise_id') ?: null,
            'filters' => [
                'search' => $request->input('search'),
                'entry_type' => $request->input('entry_type'),
                'payment_method' => $request->input('payment_method'),
                'customer_id' => $request->integer('customer_id') ?: null,
            ],
            'summary' => [
                'receivable' => $receivableTotal,
                'received' => $receivedTotal,
                'outstanding' => max(0, $receivableTotal - $receivedTotal),
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
            'customers' => $this->availableCustomersFor($actor, old('enterprise_id') ? (int) old('enterprise_id') : null),
            'selectedEnterpriseId' => $actor->isAdmin() ? old('enterprise_id') : $actor->enterprise_id,
        ]);
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        FinancialEntry::create($this->validatedEntryData($request, $actor));

        return redirect()->route('financial-entries.index')
            ->with('success', 'Lancamento financeiro criado com sucesso.');
    }

    public function edit(Request $request, int $financialEntry)
    {
        $actor = $request->user();
        $entry = $this->decorateEntriesQuery($this->scopedEntriesQuery($actor))
            ->with('payments')
            ->findOrFail($financialEntry);

        return view('financial-entries.edit', [
            'financialEntry' => $entry,
            'entryTypeOptions' => FinancialEntry::entryTypeLabels(),
            'paymentMethodOptions' => FinancialEntry::paymentMethodLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
            'customers' => $this->availableCustomersFor($actor, $entry->enterprise_id),
            'paymentSourceOptions' => FinancialEntryPayment::sourceLabels(),
        ]);
    }

    public function update(Request $request, int $financialEntry)
    {
        $actor = $request->user();
        $entry = $this->scopedEntriesQuery($actor)->findOrFail($financialEntry);
        $entry->update($this->validatedEntryData($request, $actor, $entry));

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

    public function storePayment(Request $request, int $financialEntry)
    {
        $entry = $this->decorateEntriesQuery($this->scopedEntriesQuery($request->user()))
            ->findOrFail($financialEntry);

        $validated = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:0.01'],
            'payment_date' => ['required', 'date'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($entry->remainingAmount() <= 0.0) {
            throw ValidationException::withMessages([
                'payment_amount' => 'Este lancamento ja foi totalmente pago.',
            ]);
        }

        if ((float) $validated['payment_amount'] > $entry->remainingAmount()) {
            throw ValidationException::withMessages([
                'payment_amount' => 'O valor informado excede o saldo pendente deste lancamento.',
            ]);
        }

        $entry->payments()->create([
            'amount' => $validated['payment_amount'],
            'payment_date' => $validated['payment_date'],
            'source' => FinancialEntryPayment::SOURCE_MANUAL,
            'reference' => $validated['payment_reference'] ?? null,
            'notes' => $validated['payment_notes'] ?? null,
        ]);

        return redirect()
            ->route('financial-entries.edit', $entry->id)
            ->with('success', 'Pagamento registrado com sucesso.');
    }

    public function sendWhatsAppReminder(Request $request, int $financialEntry)
    {
        $entry = $this->decorateEntriesQuery($this->scopedEntriesQuery($request->user()))
            ->findOrFail($financialEntry);

        if ($entry->entry_type !== FinancialEntry::TYPE_RECEIVABLE) {
            abort(422);
        }

        if ($entry->remainingAmount() <= 0.0) {
            throw ValidationException::withMessages([
                'whatsapp' => 'Nao ha saldo pendente para este cliente.',
            ]);
        }

        $url = $entry->whatsappReminderUrl();

        if ($url === null) {
            throw ValidationException::withMessages([
                'whatsapp' => 'O cliente nao possui telefone para cobranca via WhatsApp.',
            ]);
        }

        $entry->forceFill([
            'last_whatsapp_reminder_at' => now(),
        ])->save();

        return redirect()->away($url);
    }

    public function importBankFile(Request $request, BankStatementImportService $importService)
    {
        $actor = $request->user();
        $validated = $request->validate([
            'statement_file' => ['required', 'file', 'mimes:csv,txt,ofx', 'max:5120'],
            'enterprise_id' => $actor->isAdmin()
                ? ['nullable', 'integer', 'exists:enterprises,id']
                : ['nullable'],
        ]);

        $entries = $this->decorateEntriesQuery(
            $this->scopedEntriesQuery($actor)
                ->where('entry_type', FinancialEntry::TYPE_RECEIVABLE)
                ->when($actor->isAdmin() && ! empty($validated['enterprise_id']), function (Builder $builder) use ($validated) {
                    $builder->where('enterprise_id', (int) $validated['enterprise_id']);
                })
        )->get();

        $result = $importService->import(
            $validated['statement_file']->getRealPath(),
            $validated['statement_file']->getClientOriginalName(),
            $entries
        );

        return redirect()
            ->route('financial-entries.index', array_filter([
                'enterprise_id' => $validated['enterprise_id'] ?? null,
            ]))
            ->with('success', sprintf(
                'Importacao concluida. %d transacoes lidas, %d pagamentos conciliados, %d transacoes sem correspondencia e %d ambiguas.',
                $result['parsed'],
                $result['matched'],
                $result['unmatched'],
                $result['ambiguous']
            ));
    }

    private function scopedEntriesQuery(User $actor): Builder
    {
        $query = FinancialEntry::query();

        if (! $actor->isAdmin()) {
            $query->where('enterprise_id', $actor->enterprise_id);
        }

        return $query;
    }

    private function decorateEntriesQuery(Builder $query): Builder
    {
        return $query
            ->with(['enterprise', 'customer'])
            ->withSum('payments', 'amount');
    }

    private function applyFilters(Request $request, Builder $query, User $actor): Builder
    {
        return $query
            ->when($actor->isAdmin() && $request->filled('enterprise_id'), function (Builder $builder) use ($request) {
                $builder->where('enterprise_id', $request->integer('enterprise_id'));
            })
            ->when($request->filled('search'), function (Builder $builder) use ($request) {
                $search = '%' . trim((string) $request->input('search')) . '%';

                $builder->where(function (Builder $nested) use ($search) {
                    $nested->where('title', 'like', $search)
                        ->orWhereHas('customer', function (Builder $customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', $search)
                                ->orWhere('cnp', 'like', preg_replace('/\D/', '', $search));
                        });
                });
            })
            ->when($request->filled('entry_type'), function (Builder $builder) use ($request) {
                $builder->where('entry_type', $request->input('entry_type'));
            })
            ->when($request->filled('payment_method'), function (Builder $builder) use ($request) {
                $builder->where('payment_method', $request->input('payment_method'));
            })
            ->when($request->filled('customer_id'), function (Builder $builder) use ($request) {
                $builder->where('customer_id', $request->integer('customer_id'));
            });
    }

    private function rules(User $actor): array
    {
        return [
            'enterprise_id' => $actor->isAdmin()
                ? ['required', 'integer', 'exists:enterprises,id']
                : ['nullable'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'entry_type' => ['required', Rule::in(array_keys(FinancialEntry::entryTypeLabels()))],
            'payment_method' => ['required', Rule::in(array_keys(FinancialEntry::paymentMethodLabels()))],
            'notes' => ['nullable', 'string', 'max:2000'],
            'whatsapp_reminder_enabled' => ['nullable', 'boolean'],
        ];
    }

    private function resolveEnterpriseId(User $actor, ?int $enterpriseId): ?int
    {
        if ($actor->isAdmin()) {
            return $enterpriseId;
        }

        return $actor->enterprise_id;
    }

    private function validatedEntryData(Request $request, User $actor, ?FinancialEntry $entry = null): array
    {
        $validated = $request->validate($this->rules($actor));
        $enterpriseId = $this->resolveEnterpriseId($actor, $validated['enterprise_id'] ?? $entry?->enterprise_id);
        $customer = $this->resolveCustomer($actor, $validated['customer_id'] ?? null, $enterpriseId);

        if ($validated['entry_type'] === FinancialEntry::TYPE_RECEIVABLE && ! $customer) {
            throw ValidationException::withMessages([
                'customer_id' => 'Selecione o cliente da conta a receber.',
            ]);
        }

        return [
            'enterprise_id' => $enterpriseId,
            'customer_id' => $customer?->id,
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'entry_date' => $validated['entry_date'],
            'entry_type' => $validated['entry_type'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
            'whatsapp_reminder_enabled' => (bool) ($validated['whatsapp_reminder_enabled'] ?? false),
        ];
    }

    private function resolveCustomer(User $actor, ?int $customerId, ?int $enterpriseId): ?Customer
    {
        if (! $customerId) {
            return null;
        }

        $customer = $this->accessibleCustomersQuery($actor, $enterpriseId)->find($customerId);

        if (! $customer) {
            throw ValidationException::withMessages([
                'customer_id' => 'O cliente selecionado nao pertence ao escritorio informado.',
            ]);
        }

        return $customer;
    }

    private function accessibleCustomersQuery(User $actor, ?int $enterpriseId = null): Builder
    {
        return Customer::query()
            ->when(! $actor->isAdmin(), function (Builder $builder) use ($actor) {
                $builder->where('enterprise_id', $actor->enterprise_id);
            })
            ->when($actor->isAdmin() && $enterpriseId, function (Builder $builder) use ($enterpriseId) {
                $builder->where('enterprise_id', $enterpriseId);
            });
    }

    private function availableCustomersFor(User $actor, ?int $enterpriseId = null)
    {
        return $this->accessibleCustomersQuery($actor, $enterpriseId)
            ->with('enterprise:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'cnp', 'mobile_phone', 'phone', 'enterprise_id'])
            ->each(function (Customer $customer) use ($actor): void {
                $customer->display_name = $actor->isAdmin() && $customer->enterprise?->name
                    ? $customer->name . ' - ' . $customer->enterprise->name
                    : $customer->name;
            });
    }
}
