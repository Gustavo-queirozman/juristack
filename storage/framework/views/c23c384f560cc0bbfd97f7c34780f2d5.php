<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', config('app.name', 'Laravel')); ?></title>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        .app-layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; flex-shrink: 0; background: #1e293b; color: #e2e8f0; display: flex; flex-direction: column; }
        .sidebar-header { padding: 1.25rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); }
        .sidebar-brand { font-size: 1.125rem; font-weight: 700; color: #fff; text-decoration: none; letter-spacing: -0.02em; }
        .sidebar-brand:hover { color: #cbd5e1; }
        .sidebar-nav { flex: 1; padding: 0.75rem 0; overflow-y: auto; }
        .sidebar-group { padding: 0 0.75rem; margin-top: 1rem; }
        .sidebar-group-title { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; padding: 0 0.5rem 0.375rem; display: block; }
        .sidebar-link { display: flex; align-items: center; gap: 0.625rem; padding: 0.5rem 1rem; margin: 0 0.5rem; color: #cbd5e1; text-decoration: none; font-size: 0.9375rem; border-radius: 6px; transition: background 0.15s, color 0.15s; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: #f1f5f9; }
        .sidebar-link-active { background: rgba(99, 102, 241, 0.25); color: #c7d2fe; font-weight: 500; border-left: 3px solid #6366f1; margin-left: 0.5rem; padding-left: calc(1rem - 3px); }
        .sidebar-link-icon { font-size: 1.125rem; opacity: 0.9; width: 1.5rem; text-align: center; }
        .sidebar-footer { padding: 0.75rem; border-top: 1px solid rgba(255,255,255,0.08); }
        .sidebar-link-muted { color: #94a3b8; font-size: 0.875rem; }
        .page-title { font-size: 1.25rem; font-weight: 600; color: #1e293b; margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 1px solid #e2e8f0; }
        .app-main { flex: 1; display: flex; flex-direction: column; background: #f8fafc; min-width: 0; }
        .app-main-inner { flex: 1; padding: 1.5rem; }
        @media (max-width: 767px) {
            .app-layout { flex-direction: column; }
            .sidebar { width: 100%; flex-direction: row; flex-wrap: wrap; padding: 0.5rem; gap: 0.25rem; }
            .sidebar-header { width: 100%; padding: 0.5rem 0.75rem; border-bottom: none; }
            .sidebar-nav { flex: none; width: 100%; padding: 0.25rem 0; display: flex; flex-wrap: wrap; gap: 0.25rem; }
            .sidebar-group { margin-top: 0; width: 100%; }
            .sidebar-group-title { padding: 0.25rem 0.5rem 0 0; }
            .sidebar-link { margin: 0; padding: 0.5rem 0.75rem; }
            .sidebar-link-active { margin-left: 0; padding-left: 0.75rem; border-left: none; border-bottom: 2px solid #6366f1; }
            .sidebar-footer { width: 100%; padding: 0.5rem; border-top: 1px solid rgba(255,255,255,0.08); }
        }
    </style>
</head>
<body class="antialiased">
    <div class="app-layout">
        <?php echo $__env->make('layouts.nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <main class="app-main" role="main">
            <div class="app-main-inner">
                <?php if (! empty(trim($__env->yieldContent('pageTitle')))): ?>
                    <h1 class="page-title"><?php echo $__env->yieldContent('pageTitle'); ?></h1>
                <?php endif; ?>
                <?php if(isset($slot)): ?> <?php echo e($slot); ?> <?php else: ?> <?php echo $__env->yieldContent('content'); ?> <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>

<?php /**PATH /home/snews/projects/juristack/resources/views/layouts/app.blade.php ENDPATH**/ ?>