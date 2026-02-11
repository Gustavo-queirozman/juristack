@extends('layouts.app')

@section('pageTitle', $cliente->nome)

@section('content')
<div class="max-w-2xl">
    @if(session('status'))
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">{{ $cliente->nome }}</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('clientes.edit', $cliente) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Editar
            </a>
            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="inline form-delete-cliente">
                @csrf
                @method('DELETE')
                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 btn-delete-cliente">
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Dados do cliente</h3>
        </div>
        <div class="p-4 space-y-3">
            <div class="flex flex-wrap gap-2 items-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cliente->type === 'PF' ? 'bg-blue-100 text-blue-800' : 'bg-violet-100 text-violet-800' }}">
                    {{ $cliente->type === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica' }}
                </span>
                @if($cliente->documento_formatado)
                    <span class="text-sm text-gray-700">{{ $cliente->documento_formatado }}</span>
                @endif
            </div>
            @if($cliente->email)
                <p class="text-sm text-gray-700"><span class="font-medium text-gray-900">E-mail:</span> {{ $cliente->email }}</p>
            @endif
            @if($cliente->telefone)
                <p class="text-sm text-gray-700"><span class="font-medium text-gray-900">Telefone:</span> {{ $cliente->telefone }}</p>
            @endif
        </div>
    </div>

    @if($cliente->enderecos->isNotEmpty())
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereço</h3>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($cliente->enderecos as $endereco)
                    <li class="p-4 text-sm text-gray-700">
                        {{ $endereco->linha_completa }}
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-500 text-sm">
            Nenhum endereço cadastrado.
            <a href="{{ route('clientes.edit', $cliente) }}" class="text-indigo-600 hover:underline ml-1">Editar cliente para adicionar</a>.
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('clientes.index') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
            ← Voltar para lista de clientes
        </a>
    </div>
</div>

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
