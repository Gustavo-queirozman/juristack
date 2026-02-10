<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Resultados</h3>
        <a href="<?php echo e(route('datajud.index')); ?>" class="btn btn-outline-secondary"> ← Voltar</a>
    </div>

    <?php if(empty($resultados) || count($resultados) === 0): ?>
        <div class="alert alert-warning">Nenhum resultado encontrado.</div>
    <?php endif; ?>

    <?php $__currentLoopData = $resultados; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $src = $hit['_source'] ?? []; $numero = $src['numeroProcesso'] ?? ($hit['_id'] ?? ''); ?>
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><strong><?php echo e($numero); ?></strong> <?php if(!empty($src['classe']['nome'])): ?> — <?php echo e($src['classe']['nome']); ?> <?php endif; ?></div>
                <div class="d-flex gap-2">
                    <form method="POST" action="<?php echo e(route('datajud.salvar')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="tribunal" value="<?php echo e($hit['_tribunal'] ?? ($src['tribunal'] ?? '')); ?>">
                        <input type="hidden" name="source" value='<?php echo e(json_encode($src, JSON_UNESCAPED_UNICODE)); ?>'>
                        <button class="btn btn-sm btn-outline-success">Salvar</button>
                    </form>

                    <a href="#" class="btn btn-sm btn-outline-primary" onclick="event.preventDefault(); document.getElementById('json-<?php echo e($numero); ?>').classList.toggle('d-none')">Ver JSON</a>
                </div>
            </div>
            <div class="card-body">
                <p><strong>Tribunal:</strong> <?php echo e($hit['_tribunal'] ?? ($src['tribunal'] ?? '—')); ?></p>
                <p><strong>Assuntos:</strong> <?php echo e(implode(', ', array_map(function($a){return $a['nome'] ?? '';}, $src['assuntos'] ?? []))); ?></p>
                <div id="json-<?php echo e($numero); ?>" class="d-none">
                    <pre style="background:#0b1220;color:#dbeafe;padding:1rem;border-radius:6px;white-space:pre-wrap"><?php echo e(json_encode($src, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="mt-4">
        <a href="<?php echo e(route('datajud.index')); ?>" class="btn btn-secondary">Nova pesquisa</a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/gustavo/Desktop/juristack/resources/views/datajud/resultado.blade.php ENDPATH**/ ?>