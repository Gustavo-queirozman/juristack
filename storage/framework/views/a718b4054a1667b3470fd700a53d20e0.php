<?php $__env->startSection('pageTitle', 'Modelos de documento'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <?php if(session('success')): ?>
    <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800" role="alert">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800" role="alert">
        <?php echo e(session('error')); ?>

    </div>
    <?php endif; ?>

    <div class="mb-4">
        <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Voltar para Documentos
        </a>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            <?php echo e($templates->total()); ?> <?php echo e($templates->total() === 1 ? 'modelo' : 'modelos'); ?>

        </h2>
        <a href="<?php echo e(route('document-templates.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Novo modelo
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <?php if($templates->isEmpty()): ?>
            <div class="p-8 text-center">
                <p class="text-gray-500 mb-4">Nenhum modelo cadastrado.</p>
                <a href="<?php echo e(route('document-templates.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                    Criar primeiro modelo
                </a>
            </div>
            <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Título</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Descrição</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-medium text-gray-900"><?php echo e($template->title); ?></span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                <?php echo e(\App\Models\DocumentTemplate::TYPES[$template->type] ?? $template->type); ?>

                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">
                            <?php echo e($template->description ?? '—'); ?>

                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <span class="inline-flex items-center gap-1">
                                <a href="<?php echo e(route('document-templates.fill', $template->id)); ?>" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Preencher e gerar">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </a>
                                <a href="<?php echo e(route('document-templates.edit', $template->id)); ?>" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Editar">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="<?php echo e(route('document-templates.destroy', $template->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Excluir este modelo? Documentos gerados a partir dele não serão removidos.');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50" title="Excluir">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php if($templates->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <?php echo e($templates->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/document-templates/index.blade.php ENDPATH**/ ?>