<?php $__env->startSection('pageTitle', 'Processos salvos'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl">

    <p class="text-gray-600 text-sm mb-6">
        Processos que você salvou para consulta e acompanhamento. Eles estão vinculados à sua conta.
    </p>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            <?php echo e($processos->total()); ?> <?php echo e($processos->total() === 1 ? 'processo' : 'processos'); ?> salvo(s)
        </h2>
        <a href="<?php echo e(route('datajud.index')); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Nova pesquisa
        </a>
    </div>

    <?php if($processos->isEmpty()): ?>
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-600 mb-4">Nenhum processo salvo até o momento.</p>
            <a href="<?php echo e(route('datajud.index')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Pesquisar e salvar um processo
            </a>
        </div>
    <?php else: ?>
        <ul class="space-y-4 list-none p-0 m-0">
            <?php $__currentLoopData = $processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $ultimoMov = $processo->movimentos->first();
                    $ultimoMovNome = $ultimoMov ? $ultimoMov->nome : null;
                ?>
                <li class="salvos-processo-card rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow relative"
                    data-processo-id="<?php echo e($processo->id); ?>">
                    <div class="salvos-card-inner p-4 sm:p-5 relative">
                        <div class="salvos-card-loading hidden absolute inset-0 bg-white/80 z-10 rounded-lg flex items-center justify-center">
                            <div class="flex flex-col items-center gap-2 text-emerald-700">
                                <svg class="animate-spin h-8 w-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span class="text-sm font-medium">Buscando atualizações no DataJud...</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate">
                                    <?php echo e($processo->numero_processo); ?>

                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <?php echo e($processo->tribunal); ?>

                                    </span>
                                    <?php if($processo->classe_nome): ?>
                                        <span class="text-sm text-gray-500"><?php echo e($processo->classe_nome); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1 text-sm text-gray-600 mb-4">
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
                            <p class="text-sm text-gray-500 mb-4 m-0">
                                <span class="font-medium text-gray-700">Assuntos:</span>
                                <?php echo e($processo->assuntos->pluck('nome')->take(3)->implode(', ')); ?><?php echo e($processo->assuntos->count() > 3 ? '…' : ''); ?>

                            </p>
                        <?php endif; ?>

                        <div class="flex flex-wrap items-center gap-2 pt-3 border-t border-gray-100">
                            <a href="<?php echo e(route('datajud.salvo.show', $processo->id)); ?>"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Ver detalhes
                            </a>
                            <button type="button"
                                    class="salvos-atualizar-btn inline-flex items-center gap-1.5 px-3 py-1.5 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-md hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-60 disabled:pointer-events-none"
                                    data-processo-id="<?php echo e($processo->id); ?>"
                                    data-atualizar-url="<?php echo e(route('datajud.salvo.atualizar', $processo->id)); ?>">
                                <span class="salvos-atualizar-label">Atualizar</span>
                                <span class="salvos-atualizar-spinner hidden inline-flex items-center">
                                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
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
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>

        <?php if($processos->hasPages()): ?>
            <div class="mt-6">
                <?php echo e($processos->links()); ?>

            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div id="salvos-toast" class="fixed bottom-4 right-4 z-[10001] max-w-sm pointer-events-none hidden" role="status" aria-live="polite"></div>
</div>


<div id="salvosRemoveModal" class="salvos-confirm-modal" role="dialog" aria-labelledby="salvosRemoveModalTitle" aria-modal="true">
    <div class="salvos-confirm-backdrop">
        <div class="salvos-confirm-inner">
            <div class="salvos-confirm-header">
                <h2 id="salvosRemoveModalTitle" class="salvos-confirm-title">Remover processo</h2>
            </div>
            <div class="salvos-confirm-body">
                <p class="salvos-confirm-text">Tem certeza que deseja remover este processo da sua lista? Você poderá pesquisar e salvá-lo novamente quando quiser.</p>
            </div>
            <div class="salvos-confirm-footer">
                <button type="button" id="salvosRemoveModalCancel" class="salvos-confirm-btn salvos-confirm-btn-cancel">Cancelar</button>
                <button type="button" id="salvosRemoveModalConfirm" class="salvos-confirm-btn salvos-confirm-btn-danger">Sim, remover</button>
            </div>
        </div>
    </div>
</div>


<div id="salvosJsonModal" class="salvos-json-modal" role="dialog" aria-labelledby="salvosJsonModalTitle" aria-modal="true">
    <div class="salvos-json-modal-backdrop">
        <div class="salvos-json-modal-inner">
            <div class="salvos-json-modal-header">
                <h2 id="salvosJsonModalTitle" class="salvos-json-modal-title">JSON do processo</h2>
                <button type="button" class="salvos-json-modal-close" aria-label="Fechar">&times;</button>
            </div>
            <div class="salvos-json-modal-body">
                <pre id="salvosJsonModalContent"></pre>
            </div>
            <div class="salvos-json-modal-footer">
                <button type="button" id="salvosJsonModalBtnFechar" class="salvos-json-modal-btn">Fechar</button>
            </div>
        </div>
    </div>
</div>

<style>
    .salvos-json-modal { display: none; position: fixed; inset: 0; z-index: 9999; }
    .salvos-json-modal.is-open { display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .salvos-json-modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .salvos-json-modal-inner { position: relative; background: #fff; border-radius: 8px; max-width: 90vw; max-height: 85vh; width: 42rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); display: flex; flex-direction: column; }
    .salvos-json-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid #e5e7eb; }
    .salvos-json-modal-title { margin: 0; font-size: 1.125rem; font-weight: 600; }
    .salvos-json-modal-close { background: none; border: none; padding: 0.25rem; cursor: pointer; color: #6b7280; font-size: 1.5rem; line-height: 1; }
    .salvos-json-modal-close:hover { color: #111; }
    .salvos-json-modal-body { overflow: auto; padding: 1rem; flex: 1; }
    .salvos-json-modal-body pre { margin: 0; background: #0b1220; color: #dbeafe; padding: 1rem; border-radius: 6px; white-space: pre-wrap; word-break: break-word; font-size: 0.8125rem; }
    .salvos-json-modal-footer { padding: 0.75rem 1.25rem; border-top: 1px solid #e5e7eb; }
    .salvos-json-modal-btn { padding: 0.5rem 1rem; border: 1px solid #d1d5db; border-radius: 6px; background: #fff; color: #374151; font-size: 0.875rem; cursor: pointer; }
    .salvos-json-modal-btn:hover { background: #f9fafb; }

    .salvos-confirm-modal { display: none; position: fixed; inset: 0; z-index: 10000; }
    .salvos-confirm-modal.is-open { display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .salvos-confirm-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; padding: 1rem; }
    .salvos-confirm-inner { position: relative; background: #fff; border-radius: 8px; width: 100%; max-width: 24rem; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
    .salvos-confirm-header { padding: 1.25rem 1.25rem 0; }
    .salvos-confirm-title { margin: 0; font-size: 1.125rem; font-weight: 600; color: #1e293b; }
    .salvos-confirm-body { padding: 1rem 1.25rem; }
    .salvos-confirm-text { margin: 0; font-size: 0.9375rem; color: #475569; line-height: 1.5; }
    .salvos-confirm-footer { display: flex; gap: 0.75rem; justify-content: flex-end; padding: 1rem 1.25rem 1.25rem; border-top: 1px solid #e2e8f0; }
    .salvos-confirm-btn { padding: 0.5rem 1rem; border-radius: 6px; font-size: 0.875rem; font-weight: 500; cursor: pointer; border: none; }
    .salvos-confirm-btn-cancel { background: #f1f5f9; color: #475569; }
    .salvos-confirm-btn-cancel:hover { background: #e2e8f0; }
    .salvos-confirm-btn-danger { background: #dc2626; color: #fff; }
    .salvos-confirm-btn-danger:hover { background: #b91c1c; }

    #salvos-toast .salvos-toast-msg { padding: 0.75rem 1rem; border-radius: 8px; font-size: 0.875rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
    #salvos-toast .salvos-toast-success { background: #d1fae8; color: #065f46; border: 1px solid #a7f3d0; }
    #salvos-toast .salvos-toast-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<script>
(function() {
    var modal = document.getElementById('salvosJsonModal');
    var content = document.getElementById('salvosJsonModalContent');
    var btnFechar = document.getElementById('salvosJsonModalBtnFechar');
    var btnClose = document.querySelector('.salvos-json-modal-close');

    function openModal(payload) {
        try {
            var data = typeof payload === 'string' ? JSON.parse(payload) : payload;
            content.textContent = JSON.stringify(data, null, 2);
        } catch (e) {
            content.textContent = payload;
        }
        modal.classList.add('is-open');
    }

    function closeModal() {
        modal.classList.remove('is-open');
    }

    document.querySelectorAll('.salvos-json-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var b64 = this.getAttribute('data-payload-base64');
            if (b64) {
                try { var payload = atob(b64); openModal(payload); } catch (e) { openModal('{}'); }
            }
        });
    });
    if (btnFechar) btnFechar.addEventListener('click', closeModal);
    if (btnClose) btnClose.addEventListener('click', closeModal);
    if (modal) modal.addEventListener('click', function(e) {
        if (e.target.classList.contains('salvos-json-modal-backdrop')) closeModal();
    });

    var removeModal = document.getElementById('salvosRemoveModal');
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
    if (removeConfirm) removeConfirm.addEventListener('click', function() {
        if (formToSubmit) formToSubmit.submit();
        closeRemoveModal();
    });
    if (removeModal) removeModal.addEventListener('click', function(e) {
        if (e.target.classList.contains('salvos-confirm-backdrop')) closeRemoveModal();
    });

    var toastEl = document.getElementById('salvos-toast');
    function showToast(message, isError) {
        if (!toastEl) return;
        toastEl.innerHTML = '<div class="salvos-toast-msg ' + (isError ? 'salvos-toast-error' : 'salvos-toast-success') + '">' + (message || '') + '</div>';
        toastEl.classList.remove('hidden');
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

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/datajud/salvos.blade.php ENDPATH**/ ?>