<?php
    $isHome = request()->path() === '';
    $isDashboard = request()->routeIs('dashboard');
    $isPesquisa = request()->routeIs('datajud.index');
    $isSalvos = request()->routeIs('datajud.salvos') || request()->routeIs('datajud.salvo.show');
    $isClientes = request()->routeIs('clientes.*');
    $isProfile = request()->routeIs('profile.edit');
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
        <div class="sidebar-group">
            <span class="sidebar-group-title">Cadastros</span>
            <a href="<?php echo e(route('clientes.index')); ?>" class="sidebar-link <?php echo e($isClientes ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">👤</span>
                <span>Clientes</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Conta</span>
            <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link <?php echo e($isProfile ? 'sidebar-link-active' : ''); ?> sidebar-link-muted">
                <span class="sidebar-link-icon" aria-hidden="true">⚙</span>
                <span>Configurações</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
    <?php if(auth()->guard()->check()): ?>
    <div class="sidebar-footer">
        <form method="POST" action="<?php echo e(route('logout')); ?>" class="sidebar-logout-form" id="logout-form">
            <?php echo csrf_field(); ?>
            <button type="button" class="sidebar-link sidebar-link-logout" id="logout-btn" aria-controls="logout-confirm-modal">
                <span class="sidebar-link-icon" aria-hidden="true">↪</span>
                <span>Sair</span>
            </button>
        </form>
    </div>
    <?php endif; ?>
</aside>
<?php /**PATH /home/snews/projects/juristack/resources/views/layouts/nav.blade.php ENDPATH**/ ?>