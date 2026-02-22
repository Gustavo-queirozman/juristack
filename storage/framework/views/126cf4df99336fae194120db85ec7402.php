<?php $__env->startSection('pageTitle', 'Detalhes do processo'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="min-w-0">
            <a href="<?php echo e(route('datajud.salvos')); ?>" class="inline-flex items-center gap-1.5 text-sm text-gray-600 hover:text-indigo-600 font-medium mb-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Processos salvos
            </a>
            <h1 class="text-xl font-semibold text-gray-900 mt-0 mb-1 font-mono">
                <?php echo e($processo->numero_processo); ?>

            </h1>
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    <?php echo e($processo->tribunal); ?>

                </span>
                <?php if($processo->classe_nome): ?>
                    <span class="text-sm text-gray-500"><?php echo e($processo->classe_nome); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('datajud.salvos')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Voltar
            </a>
            <button type="button" id="salvo-atualizar-btn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-emerald-200 text-emerald-700 text-sm font-medium rounded-md hover:bg-emerald-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-60 disabled:pointer-events-none"
                    data-url="<?php echo e(route('datajud.salvo.atualizar', $processo->id)); ?>">
                <span id="salvo-atualizar-label">Atualizar dados</span>
                <span id="salvo-atualizar-spinner" class="hidden inline-flex"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>
            </button>
            <button type="button" id="salvo-remove-btn"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Remover da lista
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-sm font-semibold text-gray-900 m-0">Informações do processo</h2>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500 font-medium m-0 mb-0.5">Data de ajuizamento</p>
                        <p class="text-gray-900 m-0"><?php echo e($processo->data_ajuizamento ? $processo->data_ajuizamento->format('d/m/Y') : '—'); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium m-0 mb-0.5">Última atualização</p>
                        <p class="text-gray-900 m-0"><?php echo e($processo->datahora_ultima_atualizacao ? $processo->datahora_ultima_atualizacao->format('d/m/Y H:i') : '—'); ?></p>
                    </div>
                    <?php if($processo->orgao_julgador_nome): ?>
                        <div>
                            <p class="text-gray-500 font-medium m-0 mb-0.5">Juízo / Órgão</p>
                            <p class="text-gray-900 m-0"><?php echo e($processo->orgao_julgador_nome); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($processo->assuntos->count()): ?>
                        <div>
                            <p class="text-gray-500 font-medium m-0 mb-0.5">Assuntos</p>
                            <p class="text-gray-900 m-0"><?php echo e($processo->assuntos->pluck('nome')->implode(', ')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden mt-6">
                <details class="group">
                    <summary class="px-4 py-3 border-b border-gray-200 bg-gray-50 cursor-pointer list-none flex items-center justify-between gap-2 text-sm font-medium text-gray-700 hover:bg-gray-100">
                        <span>Ver JSON do processo</span>
                        <svg class="h-4 w-4 text-gray-500 group-open:rotate-90 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </summary>
                    <div class="p-4">
                        <div class="max-h-[70vh] overflow-y-auto pr-1 -mr-1 rounded-lg">
                            <pre class="m-0 p-4 rounded-lg bg-slate-900 text-sky-100 text-xs whitespace-pre-wrap break-words"><?php echo e(json_encode($processo->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                        </div>
                    </div>
                </details>
            </div>
        </div>

        
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900 m-0">Movimentações</h2>
                    <?php if($processo->movimentos->count()): ?>
                        <span class="text-xs text-gray-500"><?php echo e($processo->movimentos->count()); ?> registro(s)</span>
                    <?php endif; ?>
                </div>
                <div class="p-4">
                    <?php if($processo->movimentos->count()): ?>
                        <div class="max-h-[70vh] overflow-y-auto pr-1 -mr-1">
                        <ul class="relative space-y-0 list-none p-0 m-0">
                            <?php $__currentLoopData = $processo->movimentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="flex gap-4 pb-4 last:pb-0 <?php echo e(!$loop->last ? 'border-b border-gray-100' : ''); ?> <?php echo e(!$loop->first ? 'pt-4' : ''); ?>">
                                    <div class="shrink-0 w-24 text-xs text-gray-500 font-medium pt-0.5">
                                        <?php echo e($mov->data_hora ? $mov->data_hora->format('d/m/Y') : '—'); ?><br>
                                        <span class="text-gray-400"><?php echo e($mov->data_hora ? $mov->data_hora->format('H:i') : ''); ?></span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-medium text-gray-900 m-0"><?php echo e($mov->nome); ?></p>
                                        <?php if($mov->complementos->count()): ?>
                                            <?php $complementos = $mov->complementos->pluck('descricao')->filter(); ?>
                                            <?php if($complementos->isNotEmpty()): ?>
                                                <p class="text-sm text-gray-500 mt-1 mb-0"><?php echo e($complementos->implode(' · ')); ?></p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-sm m-0 py-4">Nenhum movimento registrado.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div id="salvoRemoveModal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-labelledby="salvoRemoveModalTitle" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="salvoRemoveBackdrop"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h2 id="salvoRemoveModalTitle" class="text-lg font-semibold text-gray-900 mb-2">Remover processo</h2>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja remover este processo da sua lista? Você poderá pesquisar e salvá-lo novamente quando quiser.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="salvoRemoveModalCancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="salvoRemoveModalConfirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Sim, remover</button>
        </div>
    </div>
</div>

<form method="POST" action="<?php echo e(route('datajud.salvo.delete', $processo->id)); ?>" id="salvo-remove-form" class="hidden">
    <?php echo csrf_field(); ?>
    <?php echo method_field('DELETE'); ?>
</form>

<style>
#salvoRemoveModal.is-open { display: flex !important; }
</style>

<script>
(function() {
    var modal = document.getElementById('salvoRemoveModal');
    var form = document.getElementById('salvo-remove-form');
    var btn = document.getElementById('salvo-remove-btn');
    var cancelBtn = document.getElementById('salvoRemoveModalCancel');
    var confirmBtn = document.getElementById('salvoRemoveModalConfirm');
    var backdrop = document.getElementById('salvoRemoveBackdrop');

    if (btn) btn.addEventListener('click', function() { modal.classList.add('is-open'); });
    if (cancelBtn) cancelBtn.addEventListener('click', function() { modal.classList.remove('is-open'); });
    if (backdrop) backdrop.addEventListener('click', function() { modal.classList.remove('is-open'); });
    if (confirmBtn) confirmBtn.addEventListener('click', function() {
        if (form) form.submit();
        modal.classList.remove('is-open');
    });

    var atualizarBtn = document.getElementById('salvo-atualizar-btn');
    var atualizarLabel = document.getElementById('salvo-atualizar-label');
    var atualizarSpinner = document.getElementById('salvo-atualizar-spinner');
    if (atualizarBtn) {
        atualizarBtn.addEventListener('click', function() {
            var url = this.getAttribute('data-url');
            if (!url) return;
            var csrf = document.querySelector('meta[name="csrf-token"]');
            var token = csrf ? csrf.getAttribute('content') : '';

            this.disabled = true;
            if (atualizarLabel) atualizarLabel.classList.add('hidden');
            if (atualizarSpinner) atualizarSpinner.classList.remove('hidden');

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
                window.location.reload();
            })
            .catch(function(err) {
                atualizarBtn.disabled = false;
                if (atualizarLabel) atualizarLabel.classList.remove('hidden');
                if (atualizarSpinner) atualizarSpinner.classList.add('hidden');
                alert(err.message || 'Não foi possível atualizar. Tente novamente.');
            });
        });
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/datajud/salvo.blade.php ENDPATH**/ ?>