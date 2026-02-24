<?php $__env->startSection('pageTitle', 'Documentos'); ?>

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

    <p class="text-gray-600 text-sm mb-6">
        Use os modelos para gerar procurações, contratos, petições e declarações. Preencha os dados e baixe o PDF. Você pode vincular um cliente para preencher automaticamente.
    </p>

    
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            Modelos e documentos
        </h2>
        <span class="inline-flex items-center gap-2">
            <button type="button" id="btn-novo-documento" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Novo documento
            </button>
            <a href="<?php echo e(route('document-templates.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Novo modelo
            </a>
        </span>
    </div>

    
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden mb-8">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between gap-2">
            <h3 class="text-sm font-semibold text-gray-900">Modelos</h3>
            <a href="<?php echo e(route('document-templates.index')); ?>" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todos</a>
        </div>
        <div class="p-4">
            <?php if($templates->isEmpty()): ?>
            <p class="text-gray-500 text-sm py-6 text-center">Nenhum modelo cadastrado. Crie um modelo acima.</p>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="rounded-lg border border-gray-200 p-4 hover:border-indigo-200 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-gray-900 truncate"><?php echo e($template->title); ?></p>
                            <p class="text-xs text-indigo-600 mt-0.5"><?php echo e(\App\Models\DocumentTemplate::TYPES[$template->type] ?? $template->type); ?></p>
                            <?php if($template->description): ?>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-2"><?php echo e($template->description); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="flex-shrink-0 rounded bg-gray-100 p-2">
                            <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <a href="<?php echo e(route('document-templates.fill', $template->id)); ?>" class="inline-flex items-center justify-center w-9 h-9 text-indigo-600 hover:bg-indigo-50 rounded-md" title="Preencher e gerar">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </a>
                        <a href="<?php echo e(route('document-templates.edit', $template->id)); ?>" class="inline-flex items-center justify-center w-9 h-9 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-md" title="Editar modelo">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="<?php echo e(route('document-templates.destroy', $template->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Excluir este modelo?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="inline-flex items-center justify-center w-9 h-9 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-md" title="Excluir modelo">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Meus documentos</h3>
        </div>
        <div class="overflow-x-auto">
            <?php if($documents->isEmpty()): ?>
            <p class="text-gray-500 text-sm py-8 text-center">Nenhum documento gerado. Use um modelo ou o botão Novo documento acima.</p>
            <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Título</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Cliente / Relacionado</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipo</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Modelo</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Criado em</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <a href="<?php echo e(route('documents.show', $doc->id)); ?>" class="font-medium text-indigo-600 hover:text-indigo-800"><?php echo e($doc->title); ?></a>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <?php if($doc->customer): ?>
                            <a href="<?php echo e(route('customers.show', $doc->customer)); ?>" class="text-indigo-600 hover:text-indigo-800"><?php echo e($doc->customer->name); ?></a>
                            <?php else: ?>
                            <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo e(\App\Models\Document::TYPES[$doc->type] ?? $doc->type); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo e($doc->template?->title ?? '—'); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo e($doc->created_at->format('d/m/Y')); ?></td>
                        <td class="px-4 py-3 whitespace-nowrap text-right">
                            <span class="inline-flex items-center gap-1">
                                <?php if($doc->document_link): ?>
                                <a href="<?php echo e(route('documents.download', $doc->id)); ?>" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Baixar PDF">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('documents.show', $doc->id)); ?>" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Ver">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form action="<?php echo e(route('documents.destroy', $doc->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Excluir este documento?');">
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
        <?php if($documents->hasPages()): ?>
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            <?php echo e($documents->withQueryString()->links()); ?>

        </div>
        <?php endif; ?>
    </div>
</div>


<div id="modal-novo-documento" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="fixed inset-0 bg-black/50" id="modal-novo-documento-backdrop"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md" role="dialog" aria-labelledby="modal-novo-documento-title">
            <div class="px-4 py-3 border-b border-gray-200">
                <h2 id="modal-novo-documento-title" class="text-base font-semibold text-gray-900">Novo documento</h2>
                <p class="text-sm text-gray-500 mt-0.5">Escolha o modelo e, se quiser, um cliente para preencher os dados automaticamente.</p>
            </div>
            <form action="#" method="GET" id="form-novo-documento" data-fill-base="<?php echo e(url('document-templates')); ?>">
                <div class="p-4 space-y-4">
                    <div>
                        <label for="modal-template-id" class="block text-sm font-medium text-gray-700 mb-1">Modelo *</label>
                        <select name="template_id" id="modal-template-id" required class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">Selecione o modelo</option>
                            <?php $__currentLoopData = $allTemplates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($t->id); ?>"><?php echo e($t->title); ?> (<?php echo e(\App\Models\DocumentTemplate::TYPES[$t->type] ?? $t->type); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label for="modal-customer-id" class="block text-sm font-medium text-gray-700 mb-1">Cliente (opcional)</label>
                        <select name="customer_id" id="modal-customer-id" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">Nenhum — preencher manualmente</option>
                            <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?> <?php if($c->cnp): ?>(<?php echo e($c->cnp); ?>)<?php endif; ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                    <button type="button" id="modal-novo-documento-fechar" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Preencher</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    var btn = document.getElementById('btn-novo-documento');
    var modal = document.getElementById('modal-novo-documento');
    var backdrop = document.getElementById('modal-novo-documento-backdrop');
    var fechar = document.getElementById('modal-novo-documento-fechar');
    var form = document.getElementById('form-novo-documento');
    var fillBase = form ? form.getAttribute('data-fill-base') : '';

    if (btn && modal) btn.addEventListener('click', function() { modal.classList.remove('hidden'); });
    if (fechar && modal) fechar.addEventListener('click', function() { modal.classList.add('hidden'); });
    if (backdrop && modal) backdrop.addEventListener('click', function() { modal.classList.add('hidden'); });

    if (form && fillBase) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var templateId = document.getElementById('modal-template-id').value;
            if (!templateId) return;
            var customerId = document.getElementById('modal-customer-id').value;
            var url = fillBase + '/' + templateId + '/preencher';
            if (customerId) url += '?customer_id=' + encodeURIComponent(customerId);
            window.location.href = url;
        });
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/documents/index.blade.php ENDPATH**/ ?>