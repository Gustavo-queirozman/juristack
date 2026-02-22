<?php $__env->startSection('pageTitle', 'Configurações'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <p class="text-gray-600 text-sm mb-6">
        Gerencie suas informações de perfil, senha e preferências da conta.
    </p>

    <?php if(session('status') === 'profile-updated'): ?>
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            Perfil atualizado com sucesso.
        </div>
    <?php endif; ?>
    <?php if(session('status') === 'password-updated'): ?>
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            Senha atualizada com sucesso.
        </div>
    <?php endif; ?>

    <div class="space-y-6 max-w-2xl">
        
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 m-0">Informações do perfil</h2>
                <p class="text-xs text-gray-500 mt-0.5 m-0">Atualize seu nome e endereço de e-mail.</p>
            </div>
            <div class="p-4">
                <form method="post" action="<?php echo e(route('profile.update')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('patch'); ?>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                        <input id="name" name="name" type="text" value="<?php echo e(old('name', $user->name)); ?>" required autocomplete="name"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input id="email" name="email" type="email" value="<?php echo e(old('email', $user->email)); ?>" required autocomplete="username"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <?php if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()): ?>
                            <p class="text-sm mt-2 text-gray-600">
                                Seu e-mail ainda não foi verificado.
                                <form id="send-verification" method="post" action="<?php echo e(route('verification.send')); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-800 font-medium underline">
                                        Reenviar e-mail de verificação
                                    </button>
                                </form>
                            </p>
                            <?php if(session('status') === 'verification-link-sent'): ?>
                                <p class="mt-2 text-sm text-emerald-600">Um novo link de verificação foi enviado para o seu e-mail.</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 m-0">Atualizar senha</h2>
                <p class="text-xs text-gray-500 mt-0.5 m-0">Use uma senha longa e aleatória para manter sua conta segura.</p>
            </div>
            <div class="p-4">
                <form method="post" action="<?php echo e(route('password.update')); ?>" class="space-y-4">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('put'); ?>

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">Senha atual</label>
                        <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php echo e(optional($errors->getBag('updatePassword'))->has('current_password') ? 'border-red-500' : ''); ?>">
                        <?php if(optional($errors->getBag('updatePassword'))->has('current_password')): ?><p class="mt-1 text-sm text-red-600"><?php echo e($errors->getBag('updatePassword')->first('current_password')); ?></p><?php endif; ?>
                    </div>

                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">Nova senha</label>
                        <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php echo e(optional($errors->getBag('updatePassword'))->has('password') ? 'border-red-500' : ''); ?>">
                        <?php if(optional($errors->getBag('updatePassword'))->has('password')): ?><p class="mt-1 text-sm text-red-600"><?php echo e($errors->getBag('updatePassword')->first('password')); ?></p><?php endif; ?>
                    </div>

                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php echo e(optional($errors->getBag('updatePassword'))->has('password_confirmation') ? 'border-red-500' : ''); ?>">
                        <?php if(optional($errors->getBag('updatePassword'))->has('password_confirmation')): ?><p class="mt-1 text-sm text-red-600"><?php echo e($errors->getBag('updatePassword')->first('password_confirmation')); ?></p><?php endif; ?>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Salvar senha
                        </button>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-900 m-0">Excluir conta</h2>
                <p class="text-xs text-gray-500 mt-0.5 m-0">Após a exclusão, todos os dados serão removidos permanentemente.</p>
            </div>
            <div class="p-4">
                <p class="text-sm text-gray-600 mb-4">
                    Uma vez que sua conta for excluída, todos os recursos e dados serão removidos permanentemente. Antes de excluir, faça backup de qualquer informação que deseje manter.
                </p>
                <button type="button" id="profile-delete-open"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                    Excluir conta
                </button>
            </div>
        </div>
    </div>
</div>


<div id="profile-delete-modal" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-labelledby="profile-delete-modal-title" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="profile-delete-backdrop"></div>
    <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h2 id="profile-delete-modal-title" class="text-lg font-semibold text-gray-900 mb-2">Excluir conta</h2>
        <p class="text-gray-600 text-sm mb-4">
            Tem certeza que deseja excluir sua conta? Após a exclusão, todos os dados serão removidos permanentemente. Digite sua senha para confirmar.
        </p>

        <form method="post" action="<?php echo e(route('profile.destroy')); ?>" id="profile-delete-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('delete'); ?>

            <div class="mb-4">
                <label for="profile-delete-password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <input id="profile-delete-password" name="password" type="password" placeholder="Sua senha" required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php echo e($errors->userDeletion->has('password') ? 'border-red-500' : ''); ?>">
                <?php if($errors->userDeletion->has('password')): ?><p class="mt-1 text-sm text-red-600"><?php echo e($errors->userDeletion->first('password')); ?></p><?php endif; ?>
            </div>

            <div class="flex gap-2 justify-end">
                <button type="button" id="profile-delete-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Sim, excluir conta</button>
            </div>
        </form>
    </div>
</div>

<style>
#profile-delete-modal.is-open { display: flex !important; }
</style>

<script>
(function() {
    var modal = document.getElementById('profile-delete-modal');
    var openBtn = document.getElementById('profile-delete-open');
    var cancelBtn = document.getElementById('profile-delete-cancel');
    var backdrop = document.getElementById('profile-delete-backdrop');

    if (openBtn) openBtn.addEventListener('click', function() { modal.classList.add('is-open'); });
    if (cancelBtn) cancelBtn.addEventListener('click', function() { modal.classList.remove('is-open'); });
    if (backdrop) backdrop.addEventListener('click', function() { modal.classList.remove('is-open'); });

    <?php if($errors->userDeletion->isNotEmpty()): ?>
    if (modal) modal.classList.add('is-open');
    <?php endif; ?>
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/profile/edit.blade.php ENDPATH**/ ?>