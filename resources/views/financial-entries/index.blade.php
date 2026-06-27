@extends('layouts.app')

@section('pageTitle', 'Financeiro')

@section('content')
<div class="w-full max-w-full space-y-6">
    <p class="text-gray-600 text-sm">
        Controle contas a pagar e a receber, acompanhe quanto cada cliente pagou, o saldo em aberto e concilie recebimentos por arquivo bancario.
    </p>

    @if(session('success'))
        <div class="rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            @foreach($errors->all() as $message)
                <p>{{ $message }}</p>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-emerald-700">Total a receber</p>
            <p class="mt-1 text-2xl font-semibold text-emerald-900">R$ {{ number_format((float) $summary['receivable'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-sky-200 bg-sky-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-sky-700">Total recebido</p>
            <p class="mt-1 text-2xl font-semibold text-sky-900">R$ {{ number_format((float) $summary['received'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-amber-700">Saldo em aberto</p>
            <p class="mt-1 text-2xl font-semibold text-amber-900">R$ {{ number_format((float) $summary['outstanding'], 2, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm">
            <p class="text-sm font-medium text-rose-700">Total a pagar</p>
            <p class="mt-1 text-2xl font-semibold text-rose-900">R$ {{ number_format((float) $summary['payable'], 2, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 rounded-lg border border-gray-200 bg-white shadow-sm p-4">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-900 m-0">
                    {{ $entries->total() }} {{ $entries->total() === 1 ? 'lancamento' : 'lancamentos' }}
                </h2>
                <a href="{{ route('financial-entries.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Novo lancamento
                </a>
            </div>

            <form method="GET" action="{{ route('financial-entries.index') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-3 items-end">
                @if($enterprises->isNotEmpty())
                    <div>
                        <label for="enterprise_id" class="block text-sm font-medium text-gray-700 mb-1">Escritorio</label>
                        <select name="enterprise_id" id="enterprise_id"
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                            <option value="">Todos</option>
                            @foreach($enterprises as $enterprise)
                                <option value="{{ $enterprise->id }}" {{ (int) $selectedEnterpriseId === (int) $enterprise->id ? 'selected' : '' }}>
                                    {{ $enterprise->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar por titulo ou cliente</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] }}"
                           placeholder="Ex.: Honorarios de junho"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                </div>
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                    <select name="customer_id" id="customer_id"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                        <option value="">Todos</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (int) $filters['customer_id'] === (int) $customer->id ? 'selected' : '' }}>
                                {{ $customer->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="entry_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                    <select name="entry_type" id="entry_type"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                        <option value="">Todos</option>
                        @foreach($entryTypeOptions as $value => $label)
                            <option value="{{ $value }}" {{ $filters['entry_type'] === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Pagamento</label>
                    <select name="payment_method" id="payment_method"
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                        <option value="">Todos</option>
                        @foreach($paymentMethodOptions as $value => $label)
                            <option value="{{ $value }}" {{ $filters['payment_method'] === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2 lg:col-span-2">
                    <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Filtrar
                    </button>
                    <a href="{{ route('financial-entries.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                        Limpar
                    </a>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Importar arquivo bancario</h3>
            </div>
            <form method="POST" action="{{ route('financial-entries.import-bank-file') }}" enctype="multipart/form-data" class="p-4 space-y-4">
                @csrf
                @if($selectedEnterpriseId)
                    <input type="hidden" name="enterprise_id" value="{{ $selectedEnterpriseId }}">
                @endif
                <div>
                    <label for="statement_file" class="block text-sm font-medium text-gray-700 mb-1">Arquivo CSV, TXT ou OFX</label>
                    <input type="file" name="statement_file" id="statement_file" accept=".csv,.txt,.ofx"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                </div>
                <p class="text-xs text-gray-500">
                    O sistema tenta localizar pagamentos por valor, nome do cliente, documento e descricao do lancamento.
                </p>
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 text-white text-sm font-medium rounded-md hover:bg-slate-800">
                    Importar e conciliar
                </button>
            </form>
        </div>
    </div>

    @if($entries->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-600 mb-4">Nenhum lancamento financeiro cadastrado.</p>
            <a href="{{ route('financial-entries.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Cadastrar primeiro lancamento
            </a>
        </div>
    @else
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Titulo</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Cliente</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipo</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pago</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Falta</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Vencimento</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pagamento</th>
                            @if($enterprises->isNotEmpty())
                                <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Escritorio</th>
                            @endif
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Acoes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($entries as $entry)
                            @php
                                $status = $entry->paymentStatus();
                                $statusLabel = $paymentStatusOptions[$status] ?? $status;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $entry->title }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $entry->customer?->name ?? '-' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $entry->entry_type === 'receivable' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $entryTypeOptions[$entry->entry_type] ?? $entry->entry_type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $status === 'paid' ? 'bg-sky-50 text-sky-700' : '' }}
                                        {{ $status === 'partial' ? 'bg-amber-50 text-amber-700' : '' }}
                                        {{ $status === 'pending' ? 'bg-slate-100 text-slate-700' : '' }}
                                        {{ $status === 'overdue' ? 'bg-rose-50 text-rose-700' : '' }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">R$ {{ number_format((float) $entry->amount, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-emerald-700">R$ {{ number_format($entry->paidAmount(), 2, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-amber-700">R$ {{ number_format($entry->remainingAmount(), 2, ',', '.') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $entry->entry_date?->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $paymentMethodOptions[$entry->payment_method] ?? $entry->payment_method }}</td>
                                @if($enterprises->isNotEmpty())
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $entry->enterprise?->name ?? '-' }}</td>
                                @endif
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="inline-flex items-center gap-1">
                                        @if($entry->entry_type === 'receivable' && $entry->remainingAmount() > 0 && $entry->whatsappReminderUrl())
                                            <form method="POST" action="{{ route('financial-entries.whatsapp-reminder', $entry->id) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="px-2 py-1.5 text-emerald-700 hover:text-emerald-800 rounded hover:bg-emerald-50 text-xs font-medium" title="Cobrar no WhatsApp">
                                                    WhatsApp
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('financial-entries.edit', $entry->id) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Editar">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('financial-entries.destroy', $entry->id) }}" class="inline form-delete-entry">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50 btn-delete-entry" title="Excluir">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $entries->links() }}
        </div>
    @endif
</div>

<div id="modal-delete-entry" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-entry-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir lancamento</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja excluir este lancamento financeiro? Esta acao nao pode ser desfeita.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modal-delete-entry-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="modal-delete-entry-confirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Excluir</button>
        </div>
    </div>
</div>
<script>
(function() {
    var formToSubmit = null;
    var modal = document.getElementById('modal-delete-entry');
    var cancelBtn = document.getElementById('modal-delete-entry-cancel');
    var confirmBtn = document.getElementById('modal-delete-entry-confirm');
    var backdrop = document.getElementById('modal-delete-entry-backdrop');
    document.querySelectorAll('.btn-delete-entry').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formToSubmit = this.closest('form');
            if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
        });
    });
    function closeModal() {
        formToSubmit = null;
        if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    }
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (confirmBtn) confirmBtn.addEventListener('click', function() {
        if (formToSubmit) formToSubmit.submit();
        closeModal();
    });
})();
</script>
@endsection
