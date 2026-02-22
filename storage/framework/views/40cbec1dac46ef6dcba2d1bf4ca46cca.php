<?php $__env->startSection('pageTitle', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <?php if(!empty($userName)): ?>
    <p class="text-gray-600 text-sm mb-2">Olá, <span class="font-medium text-gray-900"><?php echo e($userName); ?></span>.</p>
    <?php endif; ?>
    <p class="text-gray-600 text-sm mb-6">
        Visão geral da sua conta. Acompanhe métricas e acesse rapidamente as principais áreas.
    </p>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <a href="<?php echo e(route('users.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-500 truncate">Usuários</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalUsuarios)); ?></p>
                    <p class="mt-0.5 text-xs text-gray-400">vinculados à sua conta</p>
                </div>
                <div class="ml-3 flex-shrink-0 rounded-lg bg-indigo-50 p-2.5 group-hover:bg-indigo-100 transition-colors">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('customers.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-500 truncate">Clientes</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalClientes)); ?></p>
                    <p class="mt-0.5 text-xs text-gray-400">cadastro completo</p>
                </div>
                <div class="ml-3 flex-shrink-0 rounded-lg bg-violet-50 p-2.5 group-hover:bg-violet-100 transition-colors">
                    <svg class="h-6 w-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </a>

        <a href="<?php echo e(route('datajud.salvos')); ?>" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-200">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-500 truncate">Processos salvos</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalProcessos)); ?></p>
                    <p class="mt-0.5 text-xs text-gray-400">DataJud</p>
                </div>
                <div class="ml-3 flex-shrink-0 rounded-lg bg-emerald-50 p-2.5 group-hover:bg-emerald-100 transition-colors">
                    <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </a>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-500 truncate">Arquivos anexados</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalArquivos)); ?></p>
                    <p class="mt-0.5 text-xs text-gray-400">total no sistema</p>
                </div>
                <div class="ml-3 flex-shrink-0 rounded-lg bg-amber-50 p-2.5">
                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-8">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Acesso rápido</h3>
        </div>
        <div class="p-4 flex flex-wrap gap-3">
            <a href="<?php echo e(route('users.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Novo usuário
            </a>
            <a href="<?php echo e(route('customers.create')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5H4a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Novo cliente
            </a>
            <a href="<?php echo e(route('datajud.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                Pesquisar processos
            </a>
        </div>
    </div>

    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-semibold text-gray-900 m-0">Processos salvos recentes</h3>
                <a href="<?php echo e(route('datajud.salvos')); ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $processosRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('datajud.salvo.show', $pm->id)); ?>" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                            <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate font-mono"><?php echo e($pm->numero_processo ?? '—'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($pm->tribunal ?? 'DataJud'); ?> · <?php echo e($pm->updated_at?->diffForHumans()); ?></p>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-8 text-center text-sm text-gray-500">
                        Nenhum processo salvo ainda.
                        <a href="<?php echo e(route('datajud.index')); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium ml-1">Pesquisar processos</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-semibold text-gray-900 m-0">Últimos clientes</h3>
                <a href="<?php echo e(route('customers.index')); ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $ultimosClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('customers.show', $c)); ?>" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-sm">
                            <?php echo e(strtoupper(mb_substr($c->name ?? '?', 0, 1))); ?>

                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo e($c->name ?? 'Sem nome'); ?></p>
                            <p class="text-xs text-gray-500 truncate"><?php echo e($c->email ?? '—'); ?></p>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-8 text-center text-sm text-gray-500">
                        Nenhum cliente cadastrado.
                        <a href="<?php echo e(route('customers.create')); ?>" class="text-indigo-600 hover:text-indigo-800 font-medium ml-1">Cadastrar cliente</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/dashboard.blade.php ENDPATH**/ ?>