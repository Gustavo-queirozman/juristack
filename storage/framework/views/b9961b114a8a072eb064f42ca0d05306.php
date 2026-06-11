<?php $__env->startSection('pageTitle', 'Novo lancamento financeiro'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl">
    <p class="text-gray-600 text-sm mb-6">
        Cadastre uma conta a pagar ou a receber com data, valor e forma de pagamento.
    </p>

    <form method="POST" action="<?php echo e(route('financial-entries.store')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <?php echo $__env->make('financial-entries._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Salvar lancamento
            </button>
            <a href="<?php echo e(route('financial-entries.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views\financial-entries\create.blade.php ENDPATH**/ ?>