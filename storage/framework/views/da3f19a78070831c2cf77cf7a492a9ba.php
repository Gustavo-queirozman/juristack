<?php $__env->startSection('pageTitle', 'Novo documento a partir do modelo'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-2xl">
    <div class="mb-4">
        <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Voltar para Documentos
        </a>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-4">
        <div class="px-4 py-2 border-b border-gray-200 bg-indigo-50">
            <p class="text-sm font-medium text-indigo-900">Modelo: <?php echo e($template->title); ?></p>
            <p class="text-xs text-indigo-700"><?php echo e(\App\Models\DocumentTemplate::TYPES[$template->type] ?? $template->type); ?></p>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-900">Criar documento</h2>
        </div>
        <form action="<?php echo e(route('documents.store')); ?>" method="POST" class="p-4 space-y-4" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="document_template_id" value="<?php echo e($template->id); ?>">
            <input type="hidden" name="type" value="<?php echo e($template->type); ?>">

            <?php if($errors->any()): ?>
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título do documento *</label>
                <input type="text" name="title" id="title" value="<?php echo e(old('title', $template->title . ' - ' . now()->format('d/m/Y'))); ?>" required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                       placeholder="Ex: Procuração João Silva">
            </div>

            <div>
                <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Anexar arquivo (opcional)</label>
                <input type="file" name="document_file" id="document_file" accept=".pdf,.doc,.docx"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-500">PDF ou Word. Deixe em branco para gerar depois a partir do modelo.</p>
            </div>

            <div>
                <label for="form_link" class="block text-sm font-medium text-gray-700 mb-1">Link do formulário (opcional)</label>
                <input type="url" name="form_link" id="form_link" value="<?php echo e(old('form_link')); ?>"
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                       placeholder="https://...">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Criar documento
                </button>
                <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/documents/create-from-template.blade.php ENDPATH**/ ?>