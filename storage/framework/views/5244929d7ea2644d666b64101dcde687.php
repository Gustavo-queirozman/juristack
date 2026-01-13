<?php $__env->startSection('content'); ?>
<div class="container">
    <h4>Resultados – <?php echo e($tribunal); ?></h4>

    <?php if(empty($resultados)): ?>
        <div class="alert alert-warning">
            Nenhum processo encontrado.
        </div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Número do Processo</th>
                    <th>Classe</th>
                    <th>Data de Ajuizamento</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $resultados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item['_source']['numeroProcesso'] ?? '-'); ?></td>
                        <td><?php echo e($item['_source']['classe'] ?? '-'); ?></td>
                        <td><?php echo e($item['_source']['dataAjuizamento'] ?? '-'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/resultado.blade.php ENDPATH**/ ?>