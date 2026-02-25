<?php $__env->startSection('pageTitle', $cliente->nome); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <?php if(session('status')): ?>
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            <?php echo e(session('status')); ?>

        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0"><?php echo e($cliente->nome); ?></h2>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('users.index')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Voltar
            </a>
            <a href="<?php echo e(route('users.edit', $cliente)); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Editar
            </a>
            <form method="POST" action="<?php echo e(route('users.destroy', $cliente)); ?>" class="inline form-delete-cliente">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 btn-delete-cliente">
                    Excluir usuário
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados pessoais</h3>
            </div>
            <div class="p-4 space-y-2 text-sm">
                <p class="m-0"><span class="font-medium text-gray-700">Tipo:</span> <?php echo e($cliente->type === 'PF' ? 'Pessoa Física' : 'Pessoa Jurídica'); ?></p>
                <?php if($cliente->documento_formatado): ?><p class="m-0"><span class="font-medium text-gray-700">CPF/CNPJ:</span> <?php echo e($cliente->documento_formatado); ?></p><?php endif; ?>
                <?php if($cliente->email): ?><p class="m-0"><span class="font-medium text-gray-700">E-mail:</span> <?php echo e($cliente->email); ?></p><?php endif; ?>
                <?php if($cliente->telefone): ?><p class="m-0"><span class="font-medium text-gray-700">Telefone:</span> <?php echo e($cliente->telefone); ?></p><?php endif; ?>
                <?php if(!$cliente->documento_formatado && !$cliente->email && !$cliente->telefone): ?>
                    <p class="m-0 text-gray-500">Nenhum dado adicional informado.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereço</h3>
            </div>
            <div class="p-4 space-y-2 text-sm">
                <?php if($cliente->enderecos->isNotEmpty()): ?>
                    <?php $__currentLoopData = $cliente->enderecos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $endereco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <p class="m-0"><?php echo e($endereco->linha_completa); ?></p>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <p class="m-0 text-gray-500">Endereço não informado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<div id="modal-delete-cliente" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir usuário</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja excluir este usuário? Esta ação não pode ser desfeita.</p>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/clientes/show.blade.php ENDPATH**/ ?>