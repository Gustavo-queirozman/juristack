<?php
    $isHome = request()->path() === '';
    $isDashboard = request()->routeIs('dashboard');
    $isPesquisa = request()->routeIs('datajud.index');
    $isSalvos = request()->routeIs('datajud.salvos') || request()->routeIs('datajud.salvo.show');
    $isDocuments = request()->routeIs('documents.*') || request()->routeIs('document-templates.*');
    $isTasks = request()->routeIs('tasks.*');
    $isUsers = request()->routeIs('users.*');
    $isCustomers = request()->routeIs('customers.*');
    $isProfile = request()->routeIs('profile.edit');
?>
<aside class="sidebar" aria-label="Navegação principal">
    <div class="sidebar-header">
        <a href="<?php echo e(url('/')); ?>" class="sidebar-brand">
            <svg class="sidebar-brand-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            <?php echo e(config('app.name', 'JuriStack')); ?>

        </a>
    </div>
    <nav class="sidebar-nav">
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="sidebar-link <?php echo e($isDashboard ? 'sidebar-link-active' : ''); ?>">
            <span class="sidebar-link-icon" aria-hidden="true">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM16 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM16 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            </span>
            <span>Dashboard</span>
        </a>
        <div class="sidebar-group">
            <span class="sidebar-group-title">DataJud</span>
            <a href="<?php echo e(route('datajud.index')); ?>" class="sidebar-link <?php echo e($isPesquisa ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <span>Pesquisa de processos</span>
            </a>
            <a href="<?php echo e(route('datajud.salvos')); ?>" class="sidebar-link <?php echo e($isSalvos ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                </span>
                <span>Processos salvos</span>
            </a>
            <a href="<?php echo e(route('documents.index')); ?>" class="sidebar-link <?php echo e($isDocuments ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <span>Documentos</span>
            </a>
            <a href="<?php echo e(route('tasks.index')); ?>" class="sidebar-link <?php echo e($isTasks ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6M7 7h10M5 5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5z" />
                    </svg>
                </span>
                <span>Kanban</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Cadastros</span>
            <a href="<?php echo e(route('users.index')); ?>" class="sidebar-link <?php echo e($isUsers ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </span>
                <span>Usuários</span>
            </a>
            <a href="<?php echo e(route('customers.index')); ?>" class="sidebar-link <?php echo e($isCustomers ? 'sidebar-link-active' : ''); ?>">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <span>Clientes</span>
            </a>
        </div>
        <div class="sidebar-group">
            <span class="sidebar-group-title">Conta</span>
            <a href="<?php echo e(route('profile.edit')); ?>" class="sidebar-link <?php echo e($isProfile ? 'sidebar-link-active' : ''); ?> sidebar-link-muted">
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
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
                <span class="sidebar-link-icon" aria-hidden="true">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </span>
                <span>Sair</span>
            </button>
        </form>
    </div>
    <?php endif; ?>
</aside>
<?php /**PATH /var/www/juristack/resources/views/layouts/nav.blade.php ENDPATH**/ ?>