<?php $__env->startSection('pageTitle', 'Detalhes do processo'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl">

    
    <div class="mb-6">
        <a href="<?php echo e(route('datajud.salvos')); ?>"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded">
            <span aria-hidden="true">←</span>
            <span>Voltar para Processos salvos</span>
        </a>
    </div>

    <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
        <div class="min-w-0">
            <h1 class="text-xl font-semibold text-gray-900 mt-0 mb-1">
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
        <form method="POST"
              action="<?php echo e(route('datajud.salvo.delete', $processo->id)); ?>"
              class="inline"
              onsubmit="return confirm('Remover este processo da sua lista? Você poderá salvá-lo novamente depois.');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Remover da lista
            </button>
        </form>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700 mt-0 mb-3">Informações do processo</h2>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                <div><dt class="text-gray-500 font-medium">Data de ajuizamento</dt><dd class="text-gray-900"><?php echo e($processo->data_ajuizamento ? $processo->data_ajuizamento->format('d/m/Y') : '—'); ?></dd></div>
                <div><dt class="text-gray-500 font-medium">Última atualização</dt><dd class="text-gray-900"><?php echo e($processo->datahora_ultima_atualizacao ? $processo->datahora_ultima_atualizacao->format('d/m/Y H:i') : '—'); ?></dd></div>
                <?php if($processo->orgao_julgador_nome): ?>
                    <div class="sm:col-span-2"><dt class="text-gray-500 font-medium">Juízo / Órgão</dt><dd class="text-gray-900"><?php echo e($processo->orgao_julgador_nome); ?></dd></div>
                <?php endif; ?>
            </dl>
            <?php if($processo->assuntos->count()): ?>
                <p class="text-sm mt-3 mb-0"><span class="font-medium text-gray-500">Assuntos:</span> <span class="text-gray-900"><?php echo e($processo->assuntos->pluck('nome')->implode(', ')); ?></span></p>
            <?php endif; ?>
        </div>

        <div class="p-4 sm:p-5">
            <h2 class="text-sm font-semibold text-gray-700 mt-0 mb-3">Movimentações</h2>
            <?php if($processo->movimentos->count()): ?>
                <ul class="list-none p-0 m-0 space-y-3">
                    <?php $__currentLoopData = $processo->movimentos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mov): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex flex-col sm:flex-row sm:items-baseline gap-1 py-3 border-b border-gray-100 last:border-0">
                            <span class="text-xs text-gray-500 font-medium shrink-0 sm:w-32"><?php echo e($mov->data_hora ? $mov->data_hora->format('d/m/Y H:i') : '—'); ?></span>
                            <div>
                                <span class="text-gray-900 font-medium"><?php echo e($mov->nome); ?></span>
                                <?php if($mov->complementos->count()): ?>
                                    <p class="text-sm text-gray-500 mt-1 mb-0"><?php echo e($mov->complementos->pluck('descricao')->filter()->implode(' · ')); ?></p>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500 text-sm m-0">Nenhum movimento registrado.</p>
            <?php endif; ?>
        </div>

        <div class="p-4 sm:p-5 border-t border-gray-100 bg-gray-50">
            <details class="group">
                <summary class="cursor-pointer text-sm font-medium text-gray-700 list-none flex items-center gap-2">
                    <span class="group-open:rotate-90 transition-transform inline-block">▶</span>
                    Ver JSON do processo
                </summary>
                <pre class="mt-3 mb-0 p-4 rounded-lg bg-gray-900 text-blue-100 text-xs overflow-auto" style="white-space:pre-wrap;word-break:break-word"><?php echo e(json_encode($processo->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
            </details>
        </div>
    </div>

    
    <div class="pt-2">
        <a href="<?php echo e(route('datajud.salvos')); ?>"
           class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 rounded">
            <span aria-hidden="true">←</span>
            <span>Voltar para Processos salvos</span>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/datajud/salvo.blade.php ENDPATH**/ ?>