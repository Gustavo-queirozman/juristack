<?php $__env->startSection('content'); ?>
<div class="container">

    <style>
        .datajud-card {
            box-shadow: 0 6px 18px rgba(15,23,42,0.06);
            border-radius: 8px;
            overflow: hidden;
        }
        .datajud-card .card-header {
            background: linear-gradient(90deg,#f8fafc,#ffffff);
            font-weight: 600;
        }
        .badge-tribunal {
            background: #eef2ff;
            color: #2a2a72;
            font-weight: 600;
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>📁 Processos Salvos</h3>
        <a href="<?php echo e(route('datajud.index')); ?>" class="btn btn-outline-primary">
            🔍 Nova pesquisa
        </a>
    </div>

    <?php if($processos->isEmpty()): ?>
        <div class="alert alert-info">
            Nenhum processo salvo até o momento.
        </div>
    <?php endif; ?>

    <?php $__currentLoopData = $processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="card datajud-card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong><?php echo e($processo->numero_processo); ?></strong>
                    <?php if($processo->classe_nome): ?>
                        — <?php echo e($processo->classe_nome); ?>

                    <?php endif; ?>
                </div>
                <span class="badge badge-tribunal">
                    <?php echo e($processo->tribunal); ?>

                </span>
            </div>

            <div class="card-body">
                <p class="mb-2">
                    <strong>Status:</strong>
                    <?php echo e($processo->status ?? '—'); ?>

                </p>

                <p class="mb-2">
                    <strong>Data de ajuizamento:</strong>
                    <?php echo e(optional($processo->data_ajuizamento)->format('d/m/Y') ?? '—'); ?>

                </p>

                <p class="mb-2">
                    <strong>Última atualização:</strong>
                    <?php echo e(optional($processo->datahora_ultima_atualizacao)->format('d/m/Y H:i') ?? '—'); ?>

                </p>

                <?php if($processo->assuntos->count()): ?>
                    <p class="mb-2">
                        <strong>Assuntos:</strong><br>
                        <small class="text-muted">
                            <?php echo e($processo->assuntos->pluck('nome')->implode(', ')); ?>

                        </small>
                    </p>
                <?php endif; ?>

                <div class="d-flex gap-2 mt-3">
                    
                    <a href="<?php echo e(route('datajud.salvo.show', $processo->id)); ?>"
                       class="btn btn-sm btn-outline-primary">
                        📄 Detalhes
                    </a>

                    
                    <button
                        class="btn btn-sm btn-outline-secondary"
                        data-bs-toggle="collapse"
                        data-bs-target="#json-<?php echo e($processo->id); ?>">
                        🧾 JSON
                    </button>

                    
                    <form method="POST"
                          action="<?php echo e(route('datajud.salvo.delete', $processo->id)); ?>"
                          onsubmit="return confirm('Remover este processo salvo?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger">
                            🗑 Remover
                        </button>
                    </form>
                </div>

                
                <div class="collapse mt-3" id="json-<?php echo e($processo->id); ?>">
                    <pre style="background:#0b1220;color:#dbeafe;padding:1rem;border-radius:6px;white-space:pre-wrap">
<?php echo e(json_encode($processo->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?>

                    </pre>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    
    <div class="mt-4">
        <?php echo e($processos->links()); ?>

    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/datajud/salvos.blade.php ENDPATH**/ ?>