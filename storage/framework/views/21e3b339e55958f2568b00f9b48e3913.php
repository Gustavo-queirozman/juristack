<?php
    $isHome = request()->path() === '';
    $isDashboard = request()->routeIs('dashboard');
    $isPesquisa = request()->routeIs('datajud.index');
    $isSalvos = request()->routeIs('datajud.salvos') || request()->routeIs('datajud.salvo.show');
?>
<aside class="sidebar" aria-label="Navegação principal">
    <div class="sidebar-header">
        <a href="<?php echo e(url('/')); ?>" class="sidebar-brand"><?php echo e(config('app.name', 'JuriStack')); ?></a>
    </div>
    <nav class="sidebar-nav">
        <a href="<?php echo e(url('/')); ?>" class="sidebar-link <?php echo e($isHome ? 'sidebar-link-active' : ''); ?>">
            <span class="sidebar-link-icon" aria-hidden="true">⌂</span>
            <span>Início</span>
        </a>
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e($isDashboard ? 'sidebar-link-active' : ''); ?>">
            <span class="sidebar-link-icon" aria-hidden="true">▣</span>
            <span>Dashboard</span>
        </a>
        <div class="sidebar-group">
            <span class="sidebar-group-title">DataJud</span>
            <a href="<?php echo e(route('datajud.index')); ?>" class="sidebar-link <?php echo e($isPesquisa ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">🔍</span>
                <span>Pesquisa de processos</span>
            </a>
            <a href="<?php echo e(route('datajud.salvos')); ?>" class="sidebar-link <?php echo e($isSalvos ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">📁</span>
                <span>Processos salvos</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
    <?php if(auth()->guard()->check()): ?>
    <div class="sidebar-footer">
        <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link sidebar-link-muted">
            <span class="sidebar-link-icon" aria-hidden="true">⚙</span>
            <span>Perfil</span>
        </a>
    </div>
    <?php endif; ?>
</aside>
<?php /**PATH /home/snews/projects/juristack/resources/views/layouts/nav.blade.php ENDPATH**/ ?>