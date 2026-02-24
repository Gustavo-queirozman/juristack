<?php $__env->startSection('pageTitle', $document->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-4xl">
    <?php if(session('success')): ?>
    <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800" role="alert">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <div class="mb-4">
        <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Voltar para Documentos
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900"><?php echo e($document->title); ?></h2>
            <div class="flex items-center gap-2">
                <?php if($document->document_link): ?>
                <a href="<?php echo e(route('documents.download', $document->id)); ?>" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700" title="Baixar PDF">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Baixar PDF
                </a>
                <?php endif; ?>
                <form action="<?php echo e(route('documents.destroy', $document->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Excluir este documento?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md" title="Excluir">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        <div class="p-4 space-y-4">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div>
                    <dt class="text-gray-500">Tipo</dt>
                    <dd class="font-medium text-gray-900"><?php echo e(\App\Models\Document::TYPES[$document->type] ?? $document->type); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-500">Modelo base</dt>
                    <dd class="font-medium text-gray-900"><?php echo e($document->template?->title ?? '—'); ?></dd>
                </div>
                <div>
                    <dt class="text-gray-500">Cliente / Relacionado</dt>
                    <dd class="font-medium text-gray-900">
                        <?php if($document->customer): ?>
                        <a href="<?php echo e(route('customers.show', $document->customer)); ?>" class="text-indigo-600 hover:text-indigo-800"><?php echo e($document->customer->name); ?></a>
                        <?php else: ?>
                        —
                        <?php endif; ?>
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Criado em</dt>
                    <dd class="font-medium text-gray-900"><?php echo e($document->created_at->format('d/m/Y H:i')); ?></dd>
                </div>
                <?php if($document->document_link): ?>
                <div class="sm:col-span-2">
                    <dt class="text-gray-500">Arquivo gerado</dt>
                    <dd><span class="text-gray-900">PDF disponível.</span> Use o botão “Baixar PDF” acima.</dd>
                </div>
                <?php endif; ?>
                <?php if($document->form_link): ?>
                <div class="sm:col-span-2">
                    <dt class="text-gray-500">Formulário</dt>
                    <dd><a href="<?php echo e($document->form_link); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:underline break-all"><?php echo e($document->form_link); ?></a></dd>
                </div>
                <?php endif; ?>
            </dl>
            <?php if(!$document->document_link && $document->template): ?>
            <p class="text-sm text-gray-500">Este documento ainda não possui arquivo gerado. Use o modelo "<?php echo e($document->template->title); ?>" em Documentos → Preencher e gerar para gerar o PDF.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/documents/show.blade.php ENDPATH**/ ?>