<?php $__env->startSection('pageTitle', 'Preencher: ' . $template->title); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <div class="mb-4">
        <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Voltar para Documentos
        </a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="px-4 py-3 border-b border-gray-200 bg-indigo-50">
            <h2 class="text-base font-semibold text-indigo-900"><?php echo e($template->title); ?></h2>
            <p class="text-sm text-indigo-700 mt-0.5">Preencha os dados abaixo. O documento será gerado em PDF e ficará disponível para download.</p>
            <?php if($customer): ?>
            <p class="text-sm text-indigo-600 mt-1 font-medium">Dados do cliente “<?php echo e($customer->name); ?>” foram aplicados. Ajuste se necessário.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <form action="<?php echo e(route('document-templates.generate', $template->id)); ?>" method="POST" class="p-6" id="form-gerar-documento">
            <?php echo csrf_field(); ?>
            <?php if(request('customer_id')): ?>
            <input type="hidden" name="customer_id" value="<?php echo e(request('customer_id')); ?>">
            <?php endif; ?>
            <?php if($errors->any()): ?>
            <div class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 mb-6">
                <p class="font-medium">Corrija os erros:</p>
                <ul class="list-disc list-inside mt-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($e); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php
                $longFields = ['narrativa_fatos', 'fundamentacao_juridica', 'pedidos', 'pedidos_defesa', 'preliminares', 'defesa_merito', 'objeto_contrato', 'valor_por_extenso'];
                $ufs = ['AC'=>'Acre','AL'=>'Alagoas','AM'=>'Amazonas','AP'=>'Amapá','BA'=>'Bahia','CE'=>'Ceará','DF'=>'Distrito Federal','ES'=>'Espírito Santo','GO'=>'Goiás','MA'=>'Maranhão','MG'=>'Minas Gerais','MS'=>'Mato Grosso do Sul','MT'=>'Mato Grosso','PA'=>'Pará','PB'=>'Paraíba','PE'=>'Pernambuco','PI'=>'Piauí','PR'=>'Paraná','RJ'=>'Rio de Janeiro','RN'=>'Rio Grande do Norte','RO'=>'Rondônia','RR'=>'Roraima','RS'=>'Rio Grande do Sul','SC'=>'Santa Catarina','SE'=>'Sergipe','SP'=>'São Paulo','TO'=>'Tocantins'];
                $estadosCivis = ['Solteiro(a)'=>'Solteiro(a)','Casado(a)'=>'Casado(a)','Divorciado(a)'=>'Divorciado(a)','Viúvo(a)'=>'Viúvo(a)','União estável'=>'União estável','Separado(a)'=>'Separado(a)'];
                $nacionalidades = ['Brasileiro(a)'=>'Brasileiro(a)','Estrangeiro(a)'=>'Estrangeiro(a)'];
            ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                <?php $__currentLoopData = $placeholders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $label = \App\Models\DocumentTemplate::placeholderLabel($key);
                    $val = old('data.'.$key, $defaultData[$key] ?? '');
                    $isLong = in_array($key, $longFields);
                    if ($key === 'data') {
                        if ($val === '') $val = now()->format('Y-m-d');
                        elseif (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $val, $m)) $val = $m[3].'-'.$m[2].'-'.$m[1];
                    }
                    if ($key === 'nacionalidade' && $val === '') $val = 'Brasileiro(a)';
                ?>

                <?php if($isLong): ?>
                <div class="md:col-span-2">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <textarea name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" rows="4" required
                              class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                              placeholder="Preencha <?php echo e($label); ?>"><?php echo e($val); ?></textarea>
                </div>
                <?php elseif($key === 'cpf'): ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <input type="text" name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" value="<?php echo e($val); ?>" required
                           maxlength="14" data-mask="cpf"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                           placeholder="000.000.000-00">
                </div>
                <?php elseif($key === 'data'): ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <input type="date" name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" value="<?php echo e($val); ?>" required
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <?php elseif($key === 'hora'): ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <input type="time" name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" value="<?php echo e($val); ?>" required
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
                <?php elseif($key === 'uf_oab'): ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <select name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Selecione o UF</option>
                        <?php $__currentLoopData = $ufs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sigla => $nome): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($sigla); ?>" <?php echo e($val === $sigla ? 'selected' : ''); ?>><?php echo e($sigla); ?> – <?php echo e($nome); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php elseif($key === 'estado_civil'): ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <select name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        <?php $__currentLoopData = $estadosCivis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt => $labelOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($opt); ?>" <?php echo e($val === $opt ? 'selected' : ''); ?>><?php echo e($labelOpt); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php elseif($key === 'nacionalidade'): ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <select name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        <?php $__currentLoopData = $nacionalidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt => $labelOpt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($opt); ?>" <?php echo e($val === $opt ? 'selected' : ''); ?>><?php echo e($labelOpt); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php else: ?>
                <div class="field-group">
                    <label for="data_<?php echo e($key); ?>" class="block text-sm font-medium text-gray-700 mb-1"><?php echo e($label); ?> *</label>
                    <input type="text" name="data[<?php echo e($key); ?>]" id="data_<?php echo e($key); ?>" value="<?php echo e($val); ?>" required
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                           placeholder="<?php echo e($label); ?>">
                </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="flex flex-wrap gap-3 pt-6 mt-6 border-t border-gray-200">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0v6-6m0 6v-6m-6 0h12"/></svg>
                    Gerar e baixar documento
                </button>
                <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function() {
    var form = document.getElementById('form-gerar-documento');
    if (!form) return;

    function formatCpf(v) {
        v = v.replace(/\D/g, '');
        if (v.length <= 3) return v;
        if (v.length <= 6) return v.replace(/(\d{3})(.*)/, '$1.$2');
        if (v.length <= 9) return v.replace(/(\d{3})(\d{3})(.*)/, '$1.$2.$3');
        return v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, '$1.$2.$3-$4').slice(0, 14);
    }

    var cpfEl = document.getElementById('data_cpf');
    if (cpfEl) {
        cpfEl.addEventListener('input', function() {
            this.value = formatCpf(this.value);
        });
        if (cpfEl.value && !cpfEl.value.match(/^\d{3}\.\d{3}\.\d{3}-\d{2}$/)) {
            cpfEl.value = formatCpf(cpfEl.value);
        }
    }
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/juristack/resources/views/document-templates/fill.blade.php ENDPATH**/ ?>