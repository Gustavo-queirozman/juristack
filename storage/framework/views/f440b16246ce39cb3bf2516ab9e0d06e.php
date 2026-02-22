<?php $__env->startSection('pageTitle', 'Editar cliente'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <p class="text-gray-600 text-sm mb-6">
        Edite os dados do cliente.
    </p>

    <form method="POST" action="<?php echo e(route('users.update', $cliente)); ?>" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados do cliente</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="PF" <?php echo e(old('type', $cliente->type) === 'PF' ? 'selected' : ''); ?>>Pessoa Física (PF)</option>
                        <option value="PJ" <?php echo e(old('type', $cliente->type) === 'PJ' ? 'selected' : ''); ?>>Pessoa Jurídica (PJ)</option>
                    </select>
                    <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome / Razão social <span class="text-red-500">*</span></label>
                    <input type="text" name="nome" id="nome" value="<?php echo e(old('nome', $cliente->nome)); ?>" required maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['nome'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="wrap-cpf" class="<?php echo e(old('type', $cliente->type) === 'PJ' ? 'hidden' : ''); ?>">
                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF <span class="text-red-500">*</span></label>
                    <input type="text" name="cpf" id="cpf" value="<?php echo e(old('cpf', $cliente->cpf ? \App\Models\Cliente::formatarCpf($cliente->cpf) : '')); ?>" maxlength="14"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['cpf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="000.000.000-00">
                    <?php $__errorArgs = ['cpf'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div id="wrap-cnpj" class="<?php echo e(old('type', $cliente->type) === 'PF' ? 'hidden' : ''); ?>">
                    <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ <span class="text-red-500">*</span></label>
                    <input type="text" name="cnpj" id="cnpj" value="<?php echo e(old('cnpj', $cliente->cnpj ? \App\Models\Cliente::formatarCnpj($cliente->cnpj) : '')); ?>" maxlength="18"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['cnpj'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                           placeholder="00.000.000/0000-00">
                    <?php $__errorArgs = ['cnpj'];
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
                    <input type="email" name="email" id="email" value="<?php echo e(old('email', $cliente->email)); ?>" maxlength="255"
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
                </div>

                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="<?php echo e(old('telefone', $cliente->telefone)); ?>" maxlength="20"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 <?php $__errorArgs = ['telefone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['telefone'];
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

        <?php $end = $cliente->enderecos->first(); ?>
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereço (opcional)</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="logradouro" class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" value="<?php echo e(old('logradouro', $end?->logradouro)); ?>" maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['logradouro'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['logradouro'];
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
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input type="text" name="numero" id="numero" value="<?php echo e(old('numero', $end?->numero)); ?>" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                    <div>
                        <label for="complemento" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" name="complemento" id="complemento" value="<?php echo e(old('complemento', $end?->complemento)); ?>" maxlength="100"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="bairro" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="bairro" id="bairro" value="<?php echo e(old('bairro', $end?->bairro)); ?>" maxlength="100"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cidade" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" id="cidade" value="<?php echo e(old('cidade', $end?->cidade)); ?>" maxlength="100"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['cidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['cidade'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                        <select name="estado" id="estado" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">UF</option>
                            <?php $__currentLoopData = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $uf): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($uf); ?>" <?php echo e(old('estado', $end?->estado) === $uf ? 'selected' : ''); ?>><?php echo e($uf); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['estado'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div>
                        <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="cep" id="cep" value="<?php echo e(old('cep', $end ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $end->cep) : '')); ?>" maxlength="10"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm <?php $__errorArgs = ['cep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="00000-000">
                        <?php $__errorArgs = ['cep'];
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

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Salvar alterações
            </button>
            <a href="<?php echo e(route('users.show', $cliente)); ?>" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
(function() {
    var typeEl = document.getElementById('type');
    var wrapCpf = document.getElementById('wrap-cpf');
    var wrapCnpj = document.getElementById('wrap-cnpj');
    var cpfInput = document.getElementById('cpf');
    var cnpjInput = document.getElementById('cnpj');

    function toggleDoc() {
        var isPJ = typeEl.value === 'PJ';
        wrapCpf.classList.toggle('hidden', isPJ);
        wrapCnpj.classList.toggle('hidden', !isPJ);
        if (isPJ) { cpfInput.value = ''; cpfInput.removeAttribute('required'); cnpjInput.setAttribute('required', 'required'); }
        else { cnpjInput.value = ''; cnpjInput.removeAttribute('required'); cpfInput.setAttribute('required', 'required'); }
    }
    if (typeEl) typeEl.addEventListener('change', toggleDoc);

    function maskCpf(v) { v = v.replace(/\D/g, ''); return v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4').substring(0, 14); }
    function maskCnpj(v) { v = v.replace(/\D/g, ''); return v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5').substring(0, 18); }
    if (cpfInput) cpfInput.addEventListener('input', function() { this.value = maskCpf(this.value); });
    if (cnpjInput) cnpjInput.addEventListener('input', function() { this.value = maskCnpj(this.value); });
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/snews/projects/juristack/resources/views/clientes/edit.blade.php ENDPATH**/ ?>