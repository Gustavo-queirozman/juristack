@extends('layouts.app')

@section('pageTitle', 'Clientes')

@section('content')
<div class="w-full max-w-full">
    <p class="text-gray-600 text-sm mb-6">
        Clientes vinculados à sua conta. Cadastre pessoas físicas (CPF) ou jurídicas (CNPJ).
    </p>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            {{ $clientes->total() }} {{ $clientes->total() === 1 ? 'cliente' : 'clientes' }}
        </h2>
        <a href="{{ route('clientes.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Novo cliente
        </a>
    </div>

    <form method="GET" action="{{ route('clientes.index') }}" class="mb-6 flex flex-wrap gap-2 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="busca" class="block text-sm font-medium text-gray-700 mb-1">Buscar por nome ou CPF/CNPJ</label>
            <input type="text" name="busca" id="busca" value="{{ old('busca', $busca ?? '') }}"
                   placeholder="Nome, CPF ou CNPJ"
                   class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Buscar
        </button>
    </form>

    @if(session('status'))
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    @if($clientes->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-600 mb-4">Nenhum cliente cadastrado.</p>
            <a href="{{ route('clientes.create') }}"
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
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipo</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">CPF/CNPJ</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">E-mail</th>
                            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($clientes as $cliente)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $cliente->nome }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cliente->type === 'PF' ? 'bg-blue-100 text-blue-800' : 'bg-violet-100 text-violet-800' }}">
                                        {{ $cliente->type === 'PF' ? 'PF' : 'PJ' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 font-mono">
                                    {{ $cliente->documento_formatado ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                    {{ $cliente->email ?? '—' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="text-indigo-600 hover:text-indigo-800 font-medium mr-2">Ver</a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-gray-600 hover:text-gray-800 font-medium mr-2">Editar</a>
                                    <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline form-delete-cliente">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-600 hover:text-red-800 font-medium btn-delete-cliente">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $clientes->links() }}
        </div>
    @endif
</div>

{{-- Modal confirmar exclusão --}}
<div id="modal-delete-cliente" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir cliente</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modal-delete-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="modal-delete-confirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Excluir</button>
        </div>
    </div>
</div>
<script>
(function() {
    var formToSubmit = null;
    var modal = document.getElementById('modal-delete-cliente');
    var cancelBtn = document.getElementById('modal-delete-cancel');
    var confirmBtn = document.getElementById('modal-delete-confirm');
    var backdrop = document.getElementById('modal-delete-backdrop');

    document.querySelectorAll('.btn-delete-cliente').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formToSubmit = this.closest('form');
            if (modal) modal.classList.remove('hidden');
            if (modal) modal.classList.add('flex');
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
