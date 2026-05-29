<?php $__env->startSection('pageTitle', $customer->name); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <?php if(session('success')): ?>
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0"><?php echo e($customer->name); ?></h2>
        <div class="flex flex-wrap gap-2">
            <a href="<?php echo e(route('customers.index')); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Voltar
            </a>
            <a href="<?php echo e(route('customers.edit', $customer)); ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Editar
            </a>
            <form method="POST" action="<?php echo e(route('customers.destroy', $customer)); ?>" class="inline form-delete-customer">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-700 text-sm font-medium rounded-md hover:bg-red-50 btn-delete-customer">
                    Excluir cliente
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados pessoais</h3>
            </div>
            <div class="p-4 space-y-2 text-sm">
                <?php if($customer->cnp): ?><p class="m-0"><span class="font-medium text-gray-700">CPF/CNP:</span> <?php echo e($customer->cnp); ?></p><?php endif; ?>
                <?php if($customer->rg): ?><p class="m-0"><span class="font-medium text-gray-700">RG:</span> <?php echo e($customer->rg); ?></p><?php endif; ?>
                <?php if($customer->email): ?><p class="m-0"><span class="font-medium text-gray-700">E-mail:</span> <?php echo e($customer->email); ?></p><?php endif; ?>
                <?php if($customer->mobile_phone): ?><p class="m-0"><span class="font-medium text-gray-700">Celular:</span> <?php echo e($customer->mobile_phone); ?></p><?php endif; ?>
                <?php if($customer->phone): ?><p class="m-0"><span class="font-medium text-gray-700">Telefone:</span> <?php echo e($customer->phone); ?></p><?php endif; ?>
                <?php if($customer->birth_date): ?><p class="m-0"><span class="font-medium text-gray-700">Nascimento:</span> <?php echo e($customer->birth_date->format('d/m/Y')); ?></p><?php endif; ?>
                <?php if($customer->profession): ?><p class="m-0"><span class="font-medium text-gray-700">Profissão:</span> <?php echo e($customer->profession); ?></p><?php endif; ?>
                <?php if($customer->marital_status): ?><p class="m-0"><span class="font-medium text-gray-700">Estado civil:</span> <?php echo e($customer->marital_status); ?></p><?php endif; ?>
                <?php if(!$customer->cnp && !$customer->email && !$customer->mobile_phone && !$customer->phone): ?>
                    <p class="m-0 text-gray-500">Nenhum dado adicional informado.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereço</h3>
            </div>
            <div class="p-4 space-y-2 text-sm">
                <?php if($customer->street || $customer->city): ?>
                    <p class="m-0">
                        <?php if($customer->street): ?><?php echo e($customer->street); ?><?php if($customer->number): ?>, <?php echo e($customer->number); ?><?php endif; ?><br><?php endif; ?>
                        <?php if($customer->neighborhood): ?><?php echo e($customer->neighborhood); ?><br><?php endif; ?>
                        <?php if($customer->city): ?><?php echo e($customer->city); ?><?php if($customer->state): ?> - <?php echo e($customer->state); ?><?php endif; ?><br><?php endif; ?>
                        <?php if($customer->zip_code): ?>CEP <?php echo e($customer->zip_code); ?><?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="m-0 text-gray-500">Endereço não informado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
            <h3 class="text-sm font-semibold text-gray-900 m-0">Arquivos do cliente</h3>
            <span class="text-xs text-gray-500"><?php echo e($customer->files->count()); ?> arquivo(s)</span>
        </div>
        <div class="p-4">
            <div id="upload-files-area" class="mb-6">
                <div class="flex flex-wrap items-end gap-2 mb-3">
                    <div class="flex-1 min-w-[200px]">
                        <label for="files-input" class="block text-sm font-medium text-gray-700 mb-1">Adicionar arquivo(s)</label>
                        <input type="file" id="files-input" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Selecione um ou mais. JPG, PNG, WebP ou PDF. Máx. 5 MB cada. Você pode adicionar em várias escolhas.</p>
                    </div>
                    <button type="button" id="upload-submit-btn" disabled class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        Enviar <span id="upload-count">0</span> arquivo(s)
                    </button>
                </div>
                <ul id="pending-files-list" class="list-none p-0 m-0 space-y-1 text-sm text-gray-700 mb-2 max-h-40 overflow-y-auto"></ul>
                <?php $__errorArgs = ['files'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p id="upload-error" class="text-sm text-red-600 hidden mt-1"></p>
                <p id="upload-success" class="text-sm text-emerald-600 hidden mt-1"></p>
            </div>

            <?php if($customer->files->isEmpty()): ?>
                <p class="text-sm text-gray-500 m-0">Nenhum arquivo anexado. Use o formulário acima para enviar.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Nome do arquivo</th>
                                <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Tamanho</th>
                                <th scope="col" class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $customer->files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-900">
                                        <a href="<?php echo e(route('customers.files.download', [$customer, $file])); ?>" target="_blank" rel="noopener" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                            <?php echo e($file->original_name); ?>

                                        </a>
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-600"><?php echo e(number_format($file->size / 1024, 1)); ?> KB</td>
                                    <td class="px-4 py-2 text-right">
                                        <span class="inline-flex items-center gap-1">
                                            <a href="<?php echo e(route('customers.files.download', [$customer, $file])); ?>" target="_blank" rel="noopener" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Visualizar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="<?php echo e(route('customers.files.download', [$customer, $file])); ?>?download=1" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Baixar">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                            <form method="POST" action="<?php echo e(route('customers.files.destroy', [$customer, $file])); ?>" class="inline form-delete-file">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="button" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50 btn-delete-file" title="Remover">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>


<div id="modal-delete-customer" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir cliente</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja excluir este cliente? Os arquivos anexados também serão removidos.</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modal-delete-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="modal-delete-confirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Excluir</button>
        </div>
    </div>
</div>


<div id="modal-delete-file" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-file-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Remover arquivo</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja remover este arquivo?</p>
        <div class="flex gap-2 justify-end">
            <button type="button" id="modal-delete-file-cancel" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">Cancelar</button>
            <button type="button" id="modal-delete-file-confirm" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Remover</button>
        </div>
    </div>
</div>

<script>
(function() {
    var formCustomer = null;
    var formFile = null;
    var modalCustomer = document.getElementById('modal-delete-customer');
    var modalFile = document.getElementById('modal-delete-file');

    document.querySelectorAll('.btn-delete-customer').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formCustomer = this.closest('form');
            if (modalCustomer) { modalCustomer.classList.remove('hidden'); modalCustomer.classList.add('flex'); }
        });
    });
    document.querySelectorAll('.btn-delete-file').forEach(function(btn) {
        btn.addEventListener('click', function() {
            formFile = this.closest('form');
            if (modalFile) { modalFile.classList.remove('hidden'); modalFile.classList.add('flex'); }
        });
    });

    function closeCustomer() {
        formCustomer = null;
        if (modalCustomer) { modalCustomer.classList.add('hidden'); modalCustomer.classList.remove('flex'); }
    }
    function closeFile() {
        formFile = null;
        if (modalFile) { modalFile.classList.add('hidden'); modalFile.classList.remove('flex'); }
    }

    if (document.getElementById('modal-delete-cancel')) document.getElementById('modal-delete-cancel').addEventListener('click', closeCustomer);
    if (document.getElementById('modal-delete-backdrop')) document.getElementById('modal-delete-backdrop').addEventListener('click', closeCustomer);
    if (document.getElementById('modal-delete-confirm')) document.getElementById('modal-delete-confirm').addEventListener('click', function() {
        if (formCustomer) formCustomer.submit();
        closeCustomer();
    });

    if (document.getElementById('modal-delete-file-cancel')) document.getElementById('modal-delete-file-cancel').addEventListener('click', closeFile);
    if (document.getElementById('modal-delete-file-backdrop')) document.getElementById('modal-delete-file-backdrop').addEventListener('click', closeFile);
    if (document.getElementById('modal-delete-file-confirm')) document.getElementById('modal-delete-file-confirm').addEventListener('click', function() {
        if (formFile) formFile.submit();
        closeFile();
    });

    // Upload: acumular arquivos na lista e enviar todos
    var pendingFiles = [];
    var filesInput = document.getElementById('files-input');
    var pendingList = document.getElementById('pending-files-list');
    var uploadBtn = document.getElementById('upload-submit-btn');
    var uploadCount = document.getElementById('upload-count');
    var uploadError = document.getElementById('upload-error');
    var uploadSuccess = document.getElementById('upload-success');
    var uploadUrl = '<?php echo e(route('customers.files.store', $customer)); ?>';
    var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';

    function renderPendingList() {
        if (!pendingList) return;
        pendingList.innerHTML = '';
        pendingFiles.forEach(function(file, index) {
            var li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-2 py-1 border-b border-gray-100';
            li.innerHTML = '<span class="truncate">' + (file.name || 'Arquivo') + '</span> <span class="text-gray-500 text-xs">' + (file.size ? Math.round(file.size / 1024) + ' KB' : '') + '</span> <button type="button" class="text-red-600 hover:text-red-800 text-xs font-medium remove-pending" data-index="' + index + '">Remover</button>';
            pendingList.appendChild(li);
        });
        if (uploadCount) uploadCount.textContent = pendingFiles.length;
        if (uploadBtn) uploadBtn.disabled = pendingFiles.length === 0;

        pendingList.querySelectorAll('.remove-pending').forEach(function(btn) {
            btn.addEventListener('click', function() {
                var i = parseInt(this.getAttribute('data-index'), 10);
                pendingFiles.splice(i, 1);
                renderPendingList();
            });
        });
    }

    if (filesInput) {
        filesInput.addEventListener('change', function() {
            var list = this.files;
            if (!list || list.length === 0) return;
            for (var i = 0; i < list.length; i++) {
                pendingFiles.push(list[i]);
            }
            this.value = '';
            renderPendingList();
        });
    }

    if (uploadBtn) {
        uploadBtn.addEventListener('click', function() {
            if (pendingFiles.length === 0) return;
            if (uploadError) { uploadError.classList.add('hidden'); uploadError.textContent = ''; }
            if (uploadSuccess) uploadSuccess.classList.add('hidden');
            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Enviando...';

            var formData = new FormData();
            formData.append('_token', csrfToken);
            pendingFiles.forEach(function(file) {
                formData.append('files[]', file);
            });

            fetch(uploadUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(function(res) {
                return res.text().then(function(text) {
                    var data = {};
                    try { data = JSON.parse(text); } catch (e) {}
                    if (res.ok) {
                        pendingFiles = [];
                        renderPendingList();
                        if (uploadSuccess) {
                            uploadSuccess.textContent = 'Arquivos enviados. Atualizando...';
                            uploadSuccess.classList.remove('hidden');
                        }
                        window.location.reload();
                    } else {
                        var msg = data.message || 'Erro ao enviar arquivos.';
                        if (data.errors) {
                            if (data.errors.files && data.errors.files[0]) msg = data.errors.files[0];
                            else if (data.errors['files.0'] && data.errors['files.0'][0]) msg = data.errors['files.0'][0];
                            else if (data.errors['files.*'] && data.errors['files.*'][0]) msg = data.errors['files.*'][0];
                        }
                        throw new Error(msg);
                    }
                });
            })
            .catch(function(err) {
                if (uploadError) {
                    uploadError.textContent = err.message || 'Erro ao enviar. Tente novamente.';
                    uploadError.classList.remove('hidden');
                }
                uploadBtn.disabled = false;
                uploadBtn.innerHTML = 'Enviar <span id="upload-count">' + pendingFiles.length + '</span> arquivo(s)';
            });
        });
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/customer/show.blade.php ENDPATH**/ ?>