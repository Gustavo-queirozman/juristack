<div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
        <h3 class="text-sm font-semibold text-gray-900">Dados do lancamento</h3>
    </div>
    <div class="p-4 space-y-4">
        <?php if($enterprises->isNotEmpty()): ?>
            <div>
                <label for="enterprise_id" class="block text-sm font-medium text-gray-700 mb-1">Escritorio <span class="text-red-500">*</span></label>
                <select name="enterprise_id" id="enterprise_id" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['enterprise_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Selecione</option>
                    <?php $__currentLoopData = $enterprises; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $enterprise): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($enterprise->id); ?>" <?php echo e((int) old('enterprise_id', $selectedEnterpriseId ?? (isset($financialEntry) ? $financialEntry->enterprise_id : null)) === (int) $enterprise->id ? 'selected' : ''); ?>>
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
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titulo <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="<?php echo e(old('title', isset($financialEntry) ? $financialEntry->title : '')); ?>" required maxlength="255"
                   class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
            <?php $__errorArgs = ['title'];
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
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Valor <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" value="<?php echo e(old('amount', isset($financialEntry) ? number_format((float) $financialEntry->amount, 2, '.', '') : '')); ?>" required min="0.01" step="0.01"
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-1">Data <span class="text-red-500">*</span></label>
                <input type="date" name="entry_date" id="entry_date" value="<?php echo e(old('entry_date', isset($financialEntry) && $financialEntry->entry_date ? $financialEntry->entry_date->format('Y-m-d') : '')); ?>" required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['entry_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <?php $__errorArgs = ['entry_date'];
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
                <label for="entry_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select name="entry_type" id="entry_type" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['entry_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Selecione</option>
                    <?php $__currentLoopData = $entryTypeOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(old('entry_type', isset($financialEntry) ? $financialEntry->entry_type : '') === $value ? 'selected' : ''); ?>>
                            <?php echo e($label); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['entry_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Forma de pagamento <span class="text-red-500">*</span></label>
                <select name="payment_method" id="payment_method" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Selecione</option>
                    <?php $__currentLoopData = $paymentMethodOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php echo e(old('payment_method', isset($financialEntry) ? $financialEntry->payment_method : '') === $value ? 'selected' : ''); ?>>
                            <?php echo e($label); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views\financial-entries\_form.blade.php ENDPATH**/ ?>