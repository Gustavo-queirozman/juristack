<?php $__env->startSection('pageTitle', 'Acessos do escritório'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <p class="text-gray-600 text-sm mb-6">
        Controle os usuários internos do escritório, definindo quem pode acessar o sistema como administrador do escritório ou advogado.
    </p>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">
            <?php echo e($users->total()); ?> <?php echo e($users->total() === 1 ? 'acesso interno' : 'acessos internos'); ?>

        </h2>
        <a href="<?php echo e(route('office-access.create')); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            Novo acesso
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            <?php echo e($errors->first()); ?>

        </div>
    <?php endif; ?>

    <?php if($inviteEnterprise): ?>
        <div class="mb-6 rounded-lg border border-indigo-200 bg-indigo-50 p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-indigo-900">Link de cadastro do cliente</p>
                    <p class="mt-1 text-sm text-indigo-800">
                        Envie este link para o cliente. O cadastro ja abre vinculado ao escritorio
                        <span class="font-medium"><?php echo e($inviteEnterprise->name); ?></span>.
                    </p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input
                        id="client-register-link"
                        type="text"
                        readonly
                        value="<?php echo e(route('register.client', $inviteEnterprise->slug)); ?>"
                        class="min-w-[320px] rounded-md border border-indigo-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm"
                    >
                    <button
                        type="button"
                        id="copy-client-register-link"
                        class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                    >
                        Copiar link
                    </button>
                </div>
            </div>
            <p id="copy-client-register-feedback" class="mt-2 hidden text-sm text-emerald-700">Link copiado.</p>
        </div>
    <?php endif; ?>

    <?php if($enterprises->isNotEmpty()): ?>
    <form method="GET" action="<?php echo e(route('office-access.index')); ?>" class="mb-6 flex flex-wrap gap-2 items-end">
        <div class="min-w-[240px]">
            <label for="enterprise_id" class="block text-sm font-medium text-gray-700 mb-1">Escritório</label>
            <select name="enterprise_id" id="enterprise_id"
                    class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">Todos</option>
                <?php $__currentLoopData = $enterprises; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enterprise): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($enterprise->id); ?>" <?php echo e((int) $selectedEnterpriseId === (int) $enterprise->id ? 'selected' : ''); ?>>
                        <?php echo e($enterprise->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200">
            Filtrar
        </button>
    </form>
    <?php endif; ?>

    <?php if($users->isEmpty()): ?>
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-600 mb-4">Nenhum acesso interno cadastrado.</p>
            <a href="<?php echo e(route('office-access.create')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Criar primeiro acesso
            </a>
        </div>
    <?php else: ?>
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">E-mail</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Perfil</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Escritório</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $accessUser): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($accessUser->name); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo e($accessUser->email); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo e($roleLabels[$accessUser->role] ?? $accessUser->role); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600"><?php echo e($accessUser->enterprise?->name ?? '—'); ?></td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <?php if($accessUser->is_active): ?>
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 border border-emerald-200">Ativo</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 border border-gray-200">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right">
                                <span class="inline-flex items-center gap-1">
                                    <a href="<?php echo e(route('office-access.edit', $accessUser)); ?>" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Editar">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('office-access.destroy', $accessUser)); ?>" class="inline form-delete-access">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="button" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50 btn-delete-access" title="Excluir">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <?php echo e($users->links()); ?>

        </div>
    <?php endif; ?>
</div>

<div id="modal-delete-access" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-access-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Remover acesso</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja remover este acesso do escritório? Esta ação não pode ser desfeita.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modal-delete-access-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="modal-delete-access-confirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Remover</button>
        </div>
    </div>
</div>

<script>
(function() {
    var formToSubmit = null;
    var modal = document.getElementById('modal-delete-access');
    var cancelBtn = document.getElementById('modal-delete-access-cancel');
    var confirmBtn = document.getElementById('modal-delete-access-confirm');
    var backdrop = document.getElementById('modal-delete-access-backdrop');
    document.querySelectorAll('.btn-delete-access').forEach(function(btn) {
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

    var copyButton = document.getElementById('copy-client-register-link');
    var copyInput = document.getElementById('client-register-link');
    var copyFeedback = document.getElementById('copy-client-register-feedback');

    if (copyButton && copyInput) {
        copyButton.addEventListener('click', async function() {
            try {
                await navigator.clipboard.writeText(copyInput.value);
                if (copyFeedback) {
                    copyFeedback.classList.remove('hidden');
                    setTimeout(function() {
                        copyFeedback.classList.add('hidden');
                    }, 2500);
                }
            } catch (error) {
                copyInput.select();
                document.execCommand('copy');
                if (copyFeedback) {
                    copyFeedback.classList.remove('hidden');
                    setTimeout(function() {
                        copyFeedback.classList.add('hidden');
                    }, 2500);
                }
            }
        });
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views/office-access/index.blade.php ENDPATH**/ ?>