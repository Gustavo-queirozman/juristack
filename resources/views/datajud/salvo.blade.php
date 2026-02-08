@extends('layouts.app')

@section('pageTitle', 'Detalhes do processo')

@section('content')
<div class="max-w-4xl">

    {{-- Navegação: voltar para a lista --}}
    <div class="mb-6">
        <a href="{{ route('datajud.salvos') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded">
            <span aria-hidden="true">←</span>
            <span>Voltar para Processos salvos</span>
        </a>
    </div>

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
            <h1 class="text-xl font-semibold text-gray-900 mt-0 mb-1">
                {{ $processo->numero_processo }}
            </h1>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ $processo->tribunal }}
                </span>
                @if($processo->classe_nome)
                    <span class="text-sm text-gray-500">{{ $processo->classe_nome }}</span>
                @endif
            </div>
        </div>
        <form method="POST"
              action="{{ route('datajud.salvo.delete', $processo->id) }}"
              class="inline"
              id="salvo-remove-form">
            @csrf
            @method('DELETE')
            <button type="button"
                    id="salvo-remove-btn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Remover da lista
            </button>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 mt-0 mb-3">Informações do processo</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                <div><dt class="text-gray-500 font-medium">Data de ajuizamento</dt><dd class="text-gray-900">{{ $processo->data_ajuizamento ? $processo->data_ajuizamento->format('d/m/Y') : '—' }}</dd></div>
                <div><dt class="text-gray-500 font-medium">Última atualização</dt><dd class="text-gray-900">{{ $processo->datahora_ultima_atualizacao ? $processo->datahora_ultima_atualizacao->format('d/m/Y H:i') : '—' }}</dd></div>
                @if($processo->orgao_julgador_nome)
                    <div class="sm:col-span-2"><dt class="text-gray-500 font-medium">Juízo / Órgão</dt><dd class="text-gray-900">{{ $processo->orgao_julgador_nome }}</dd></div>
                @endif
            </dl>
            @if($processo->assuntos->count())
                <p class="text-sm mt-3 mb-0"><span class="font-medium text-gray-500">Assuntos:</span> <span class="text-gray-900">{{ $processo->assuntos->pluck('nome')->implode(', ') }}</span></p>
            @endif
        </div>

        <div class="p-4 sm:p-5">
            <h2 class="text-sm font-semibold text-gray-700 mt-0 mb-3">Movimentações</h2>
            @if($processo->movimentos->count())
                <ul class="list-none p-0 m-0 space-y-3">
                    @foreach($processo->movimentos as $mov)
                        <li class="flex flex-col sm:flex-row sm:items-baseline gap-1 py-3 border-b border-gray-100 last:border-0">
                            <span class="text-xs text-gray-500 font-medium shrink-0 sm:w-32">{{ $mov->data_hora ? $mov->data_hora->format('d/m/Y H:i') : '—' }}</span>
                            <div>
                                <span class="text-gray-900 font-medium">{{ $mov->nome }}</span>
                                @if($mov->complementos->count())
                                    <p class="text-sm text-gray-500 mt-1 mb-0">{{ $mov->complementos->pluck('descricao')->filter()->implode(' · ') }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-sm m-0">Nenhum movimento registrado.</p>
            @endif
        </div>

        <div class="p-4 sm:p-5 border-t border-gray-100 bg-gray-50">
            <details class="group">
                <summary class="cursor-pointer text-sm font-medium text-gray-700 list-none flex items-center gap-2">
                    <span class="group-open:rotate-90 transition-transform inline-block">▶</span>
                    Ver JSON do processo
                </summary>
                <pre class="mt-3 mb-0 p-4 rounded-lg bg-gray-900 text-blue-100 text-xs overflow-auto" style="white-space:pre-wrap;word-break:break-word">{{ json_encode($processo->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        </div>
    </div>

    {{-- Segundo link de voltar no final da página --}}
    <div class="pt-2">
        <a href="{{ route('datajud.salvos') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded">
            <span aria-hidden="true">←</span>
            <span>Voltar para Processos salvos</span>
        </a>
    </div>
</div>

{{-- Modal de confirmação de remoção --}}
<div id="salvoRemoveModal" class="salvo-confirm-modal" role="dialog" aria-labelledby="salvoRemoveModalTitle" aria-modal="true">
    <div class="salvo-confirm-backdrop">
        <div class="salvo-confirm-inner">
            <div class="salvo-confirm-header">
                <h2 id="salvoRemoveModalTitle" class="salvo-confirm-title">Remover processo</h2>
            </div>
            <div class="salvo-confirm-body">
                <p class="salvo-confirm-text">Tem certeza que deseja remover este processo da sua lista? Você poderá pesquisar e salvá-lo novamente quando quiser.</p>
            </div>
            <div class="salvo-confirm-footer">
                <button type="button" id="salvoRemoveModalCancel" class="salvo-confirm-btn salvo-confirm-btn-cancel">Cancelar</button>
                <button type="button" id="salvoRemoveModalConfirm" class="salvo-confirm-btn salvo-confirm-btn-danger">Sim, remover</button>
            </div>
        </div>
    </div>
</div>

<style>
    .salvo-confirm-modal { display: none; position: fixed; inset: 0; z-index: 10000; }
    .salvo-confirm-modal.is-open { display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .salvo-confirm-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .salvo-confirm-inner { position: relative; background: #fff; border-radius: 8px; width: 100%; max-width: 24rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .salvo-confirm-header { padding: 1.25rem 1.25rem 0; }
    .salvo-confirm-title { margin: 0; font-size: 1.125rem; font-weight: 600; color: #1e293b; }
    .salvo-confirm-body { padding: 1rem 1.25rem; }
    .salvo-confirm-text { margin: 0; font-size: 0.9375rem; color: #475569; line-height: 1.5; }
    .salvo-confirm-footer { display: flex; gap: 0.75rem; justify-content: flex-end; padding: 1rem 1.25rem 1.25rem; border-top: 1px solid #e2e8f0; }
    .salvo-confirm-btn { padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; cursor: pointer; border: none; }
    .salvo-confirm-btn-cancel { background: #f1f5f9; color: #475569; }
    .salvo-confirm-btn-cancel:hover { background: #e2e8f0; }
    .salvo-confirm-btn-danger { background: #dc2626; color: #fff; }
    .salvo-confirm-btn-danger:hover { background: #b91c1c; }
</style>

<script>
(function() {
    var modal = document.getElementById('salvoRemoveModal');
    var form = document.getElementById('salvo-remove-form');
    var btn = document.getElementById('salvo-remove-btn');
    var cancelBtn = document.getElementById('salvoRemoveModalCancel');
    var confirmBtn = document.getElementById('salvoRemoveModalConfirm');
    if (btn) btn.addEventListener('click', function() { modal.classList.add('is-open'); });
    if (cancelBtn) cancelBtn.addEventListener('click', function() { modal.classList.remove('is-open'); });
    if (confirmBtn) confirmBtn.addEventListener('click', function() { if (form) form.submit(); modal.classList.remove('is-open'); });
    if (modal) modal.addEventListener('click', function(e) { if (e.target.classList.contains('salvo-confirm-backdrop')) modal.classList.remove('is-open'); });
})();
</script>
@endsection
