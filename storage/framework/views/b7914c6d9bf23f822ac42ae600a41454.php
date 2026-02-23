<?php $__env->startSection('pageTitle', 'Processos salvos'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <p class="text-gray-600 text-sm mb-6">
        Processos que você salvou para consulta e acompanhamento. Eles estão vinculados à sua conta.
    </p>

    <form method="GET" action="<?php echo e(route('datajud.salvos')); ?>" class="mb-6 flex flex-wrap gap-2 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="busca" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por número do processo</label>
            <input type="text" name="busca" id="busca" value="<?php echo e(old('busca', $busca ?? '')); ?>"
                   placeholder="Ex: 0001234-56.2023.8.26.0000 ou parte do número"
                   class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Buscar
        </button>
        <?php if(!empty($busca)): ?>
            <a href="<?php echo e(route('datajud.salvos')); ?>" class="px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-900">Limpar</a>
        <?php endif; ?>
    </form>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            <?php echo e($processos->total()); ?> <?php echo e($processos->total() === 1 ? 'processo' : 'processos'); ?> salvo(s)
        </h2>
        <a href="<?php echo e(route('datajud.index')); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Nova pesquisa
        </a>
    </div>

    <?php if($processos->isEmpty()): ?>
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm p-8 text-center">
            <div class="flex justify-center mb-4">
                <div class="rounded-full bg-gray-100 p-4">
                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
            <?php if(!empty($busca)): ?>
                <p class="text-gray-600 mb-4">Nenhum processo encontrado com o filtro informado.</p>
                <a href="<?php echo e(route('datajud.salvos')); ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium mr-2">Limpar filtro</a>
                <a href="<?php echo e(route('datajud.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Nova pesquisa
                </a>
            <?php else: ?>
                <p class="text-gray-600 mb-4">Nenhum processo salvo até o momento.</p>
                <a href="<?php echo e(route('datajud.index')); ?>"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Pesquisar e salvar um processo
                </a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="max-h-[70vh] overflow-y-auto pr-1 -mr-1">
            <div class="space-y-4">
            <?php $__currentLoopData = $processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $ultimoMov = $processo->movimentos->first();
                    $ultimoMovNome = $ultimoMov ? $ultimoMov->nome : null;
                ?>
                <article class="salvos-processo-card rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow relative"
                         data-processo-id="<?php echo e($processo->id); ?>">
                    <div class="salvos-card-inner relative">
                        <div class="salvos-card-loading hidden absolute inset-0 bg-white/90 z-10 rounded-xl flex items-center justify-center">
                            <div class="flex flex-col items-center gap-2 text-emerald-700">
                                <svg class="animate-spin h-8 w-8" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm font-medium">Buscando atualizações no DataJud...</span>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate font-mono text-sm">
                                    <?php echo e($processo->numero_processo); ?>

                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <?php echo e($processo->tribunal); ?>

                                    </span>
                                    <?php if($processo->classe_nome): ?>
                                        <span class="text-xs text-gray-500"><?php echo e($processo->classe_nome); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-gray-600 mb-4">
                                <?php if($processo->orgao_julgador_nome): ?>
                                    <p class="m-0"><span class="font-medium text-gray-700">Juízo:</span> <?php echo e($processo->orgao_julgador_nome); ?></p>
                                <?php endif; ?>
                                <p class="m-0"><span class="font-medium text-gray-700">Ajuizamento:</span> <?php echo e($processo->data_ajuizamento ? $processo->data_ajuizamento->format('d/m/Y') : '—'); ?></p>
                                <p class="m-0"><span class="font-medium text-gray-700">Última atualização:</span> <?php echo e($processo->datahora_ultima_atualizacao ? $processo->datahora_ultima_atualizacao->format('d/m/Y H:i') : '—'); ?></p>
                                <?php if($ultimoMovNome): ?>
                                    <p class="m-0 sm:col-span-2"><span class="font-medium text-gray-700">Último movimento:</span> <?php echo e($ultimoMovNome); ?></p>
                                <?php endif; ?>
                            </div>

                            <?php if($processo->assuntos->count()): ?>
                                <p class="text-sm text-gray-500 mb-0">
                                    <span class="font-medium text-gray-700">Assuntos:</span>
                                    <?php echo e($processo->assuntos->pluck('nome')->take(3)->implode(', ')); ?><?php echo e($processo->assuntos->count() > 3 ? '…' : ''); ?>

                                </p>
                            <?php endif; ?>

                            <div class="flex flex-wrap items-center gap-2 pt-4 mt-4 border-t border-gray-100">
                                <a href="<?php echo e(route('datajud.salvo.show', $processo->id)); ?>"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Ver detalhes
                                </a>
                                <button type="button"
                                        class="salvos-atualizar-btn inline-flex items-center gap-1.5 px-3 py-1.5 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-md hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-60 disabled:pointer-events-none"
                                        data-processo-id="<?php echo e($processo->id); ?>"
                                        data-atualizar-url="<?php echo e(route('datajud.salvo.atualizar', $processo->id)); ?>">
                                    <span class="salvos-atualizar-label">Atualizar</span>
                                    <span class="salvos-atualizar-spinner hidden inline-flex">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                </button>
                                <button type="button"
                                        class="salvos-json-btn inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                        data-payload-base64="<?php echo e(base64_encode(json_encode($processo->payload))); ?>">
                                    Ver JSON
                                </button>
                                <form method="POST"
                                      action="<?php echo e(route('datajud.salvo.delete', $processo->id)); ?>"
                                      class="inline removo-form-salvos">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button"
                                            class="removo-btn-salvos inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                        Remover
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        <?php if($processos->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($processos->links()); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>


<div id="salvos-toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[11001] flex flex-col gap-2 pointer-events-none hidden" role="status" aria-live="polite"></div>


<div id="salvosRemoveModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-labelledby="salvosRemoveModalTitle" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="salvosRemoveBackdrop"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h2 id="salvosRemoveModalTitle" class="text-lg font-semibold text-gray-900 mb-2">Remover processo</h2>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja remover este processo da sua lista? Você poderá pesquisar e salvá-lo novamente quando quiser.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="salvosRemoveModalCancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="salvosRemoveModalConfirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Sim, remover</button>
        </div>
    </div>
</div>


<div id="salvosJsonModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4 bg-black/50" role="dialog" aria-labelledby="salvosJsonModalTitle" aria-modal="true">
    <div class="bg-white rounded-xl shadow-xl max-w-3xl w-full max-h-[85vh] flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200">
            <h2 id="salvosJsonModalTitle" class="text-lg font-semibold text-gray-900 m-0">JSON do processo</h2>
            <button type="button" class="salvos-json-modal-close p-1 text-gray-500 hover:text-gray-700 rounded" aria-label="Fechar">&times;</button>
        </div>
        <div class="p-4 overflow-auto flex-1">
            <pre id="salvosJsonModalContent" class="bg-slate-900 text-sky-100 p-4 rounded-lg text-xs overflow-auto m-0 whitespace-pre-wrap break-word"></pre>
        </div>
        <div class="px-4 py-3 border-t border-gray-200">
            <button type="button" id="salvosJsonModalBtnFechar" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Fechar</button>
        </div>
    </div>
</div>

<style>
#salvosRemoveModal.is-open,
#salvosJsonModal.is-open { display: flex !important; }
#salvos-toast .salvos-toast-msg { padding: 0.75rem 1.25rem; border-radius: 8px; font-size: 0.875rem; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.15); pointer-events: auto; }
#salvos-toast .salvos-toast-success { background: #f0fdf4; border: 1px solid #86efac; color: #166534; }
#salvos-toast .salvos-toast-error { background: #fef2f2; border: 1px solid #fecaca; color: #b91c1c; }
</style>

<script>
(function() {
    var modal = document.getElementById('salvosJsonModal');
    var content = document.getElementById('salvosJsonModalContent');
    var btnFechar = document.getElementById('salvosJsonModalBtnFechar');
    var btnClose = document.querySelector('.salvos-json-modal-close');

    function openJsonModal(payload) {
        try {
            var data = typeof payload === 'string' ? JSON.parse(payload) : payload;
            content.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            content.textContent = payload;
        }
        modal.classList.add('is-open');
    }

    function closeJsonModal() {
        modal.classList.remove('is-open');
    }

    document.querySelectorAll('.salvos-json-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var b64 = this.getAttribute('data-payload-base64');
            if (b64) {
                try { var payload = atob(b64); openJsonModal(payload); } catch (e) { openJsonModal('{}'); }
            }
        });
    });
    if (btnFechar) btnFechar.addEventListener('click', closeJsonModal);
    if (btnClose) btnClose.addEventListener('click', closeJsonModal);
    if (modal) modal.addEventListener('click', function(e) {
        if (e.target === modal) closeJsonModal();
    });

    var removeModal = document.getElementById('salvosRemoveModal');
    var removeBackdrop = document.getElementById('salvosRemoveBackdrop');
    var removeCancel = document.getElementById('salvosRemoveModalCancel');
    var removeConfirm = document.getElementById('salvosRemoveModalConfirm');
    var formToSubmit = null;

    function openRemoveModal(form) {
        formToSubmit = form;
        removeModal.classList.add('is-open');
    }
    function closeRemoveModal() {
        removeModal.classList.remove('is-open');
        formToSubmit = null;
    }
    document.querySelectorAll('.removo-btn-salvos').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var form = this.closest('form');
            if (form) openRemoveModal(form);
        });
    });
    if (removeCancel) removeCancel.addEventListener('click', closeRemoveModal);
    if (removeBackdrop) removeBackdrop.addEventListener('click', closeRemoveModal);
    if (removeConfirm) removeConfirm.addEventListener('click', function() {
        if (formToSubmit) formToSubmit.submit();
        closeRemoveModal();
    });

    var toastEl = document.getElementById('salvos-toast');
    function showToast(message, isError) {
        if (!toastEl) return;
        toastEl.innerHTML = '<div class="salvos-toast-msg ' + (isError ? 'salvos-toast-error' : 'salvos-toast-success') + '">' + (message || '') + '</div>';
        toastEl.classList.remove('hidden');
        toastEl.style.display = 'flex';
        toastEl.style.flexDirection = 'column';
        setTimeout(function() {
            toastEl.classList.add('hidden');
            toastEl.innerHTML = '';
        }, 5000);
    }

    document.querySelectorAll('.salvos-atualizar-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = this.getAttribute('data-atualizar-url');
            var card = this.closest('.salvos-processo-card');
            var loadingEl = card ? card.querySelector('.salvos-card-loading') : null;
            var label = this.querySelector('.salvos-atualizar-label');
            var spinner = this.querySelector('.salvos-atualizar-spinner');
            var csrf = document.querySelector('meta[name="csrf-token"]');
            var token = (csrf && csrf.getAttribute('content')) ? csrf.getAttribute('content') : '<?php echo e(csrf_token()); ?>';

            this.disabled = true;
            if (label) label.classList.add('hidden');
            if (spinner) spinner.classList.remove('hidden');
            if (loadingEl) loadingEl.classList.remove('hidden');

            var self = this;
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({})
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    if (!r.ok) throw new Error(data.error || 'Erro ao atualizar');
                    return data;
                });
            })
            .then(function() {
                showToast('Processo atualizado com sucesso. Atualizando lista…');
                window.location.reload();
            })
            .catch(function(err) {
                self.disabled = false;
                if (label) label.classList.remove('hidden');
                if (spinner) spinner.classList.add('hidden');
                if (loadingEl) loadingEl.classList.add('hidden');
                showToast(err.message || 'Não foi possível atualizar este processo.', true);
            });
        });
    });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/gustavo/Desktop/juristack/resources/views/datajud/salvos.blade.php ENDPATH**/ ?>