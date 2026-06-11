<?php $__env->startSection('pageTitle', 'Novo acesso do escritório'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-3xl">
    <p class="text-gray-600 text-sm mb-6">
        Cadastre um novo usuário interno para o escritório, definindo o perfil de acesso e a senha inicial.
    </p>

    <?php if($errors->any()): ?>
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            <p class="font-medium m-0 mb-1">Corrija os erros abaixo:</p>
            <ul class="list-disc list-inside m-0 space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($message); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('office-access.store')); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados do acesso</h3>
            </div>
            <div class="p-4 space-y-4">
                <?php if($enterprises->isNotEmpty()): ?>
                <div>
                    <label for="enterprise_id" class="block text-sm font-medium text-gray-700 mb-1">Escritório <span class="text-red-500">*</span></label>
                    <select name="enterprise_id" id="enterprise_id" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['enterprise_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">— Selecione o escritório —</option>
                        <?php $__currentLoopData = $enterprises; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enterprise): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($enterprise->id); ?>" <?php echo e((int) old('enterprise_id', $selectedEnterpriseId) === (int) $enterprise->id ? 'selected' : ''); ?>>
                                <?php echo e($enterprise->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['enterprise_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <?php endif; ?>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['name'];
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required maxlength="255"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['email'];
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
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Perfil <span class="text-red-500">*</span></label>
                        <select name="role" id="role" required
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">— Selecione —</option>
                            <?php $__currentLoopData = $roleOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role); ?>" <?php echo e(old('role') === $role ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha inicial <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo e(old('is_active', '1') ? 'checked' : ''); ?>

                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-gray-700">Criar acesso já ativo</label>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Criar acesso
            </button>
            <a href="<?php echo e(route('office-access.index')); ?>" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views\office-access\create.blade.php ENDPATH**/ ?>