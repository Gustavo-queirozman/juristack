@extends('layouts.app')

@section('pageTitle', 'Customers')

@section('content')
<div class="w-full max-w-full">
    <p class="text-gray-600 text-sm mb-6">
        Cadastro completo de clientes (customers). Cadastre, edite e anexe arquivos a cada cliente.
    </p>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            {{ $customers->total() }} {{ $customers->total() === 1 ? 'cliente' : 'clientes' }}
        </h2>
        <a href="{{ route('customers.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Novo cliente
        </a>
    </div>

    <form method="GET" action="{{ route('customers.index') }}" class="mb-6 flex flex-wrap gap-2 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="busca" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nome, e-mail ou CPF/CNP</label>
            <input type="text" name="busca" id="busca" value="{{ old('busca', $busca ?? '') }}"
                   placeholder="Nome, e-mail ou documento"
                   class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Buscar
        </button>
    </form>

    @if(session('success'))
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($customers->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-600 mb-4">Nenhum cliente cadastrado.</p>
            <a href="{{ route('customers.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Cadastrar primeiro cliente
            </a>
        </div>
    @else
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nome</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">CPF/CNP</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">E-mail</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Telefone</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Arquivos</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('customers.show', $customer) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $customer->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 font-mono">{{ $customer->cnp ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $customer->email ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $customer->mobile_phone ?? $customer->phone ?? '—' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $customer->files_count ?? 0 }} arquivo(s)
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <span class="inline-flex items-center gap-1">
                                        <a href="{{ route('customers.show', $customer) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Ver">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Editar">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline form-delete-customer">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50 btn-delete-customer" title="Excluir">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    @endif
</div>

<div id="modal-delete-customer" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir cliente</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja excluir este cliente? Os arquivos anexados também serão removidos. Esta ação não pode ser desfeita.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modal-delete-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="modal-delete-confirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Excluir</button>
        </div>
    </div>
</div>
<script>
(function() {
    var formToSubmit = null;
    var modal = document.getElementById('modal-delete-customer');
    var cancelBtn = document.getElementById('modal-delete-cancel');
    var confirmBtn = document.getElementById('modal-delete-confirm');
    var backdrop = document.getElementById('modal-delete-backdrop');
    document.querySelectorAll('.btn-delete-customer').forEach(function(btn) {
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
