<?php $__env->startSection('pageTitle', $customer->name); ?>

<?php $__env->startSection('content'); ?>
<?php
    $generalFiles = $customer->files->whereNull('datajud_processo_id')->sortByDesc('created_at')->values();
    $pendingDocumentRequests = $customer->documentRequests
        ->where('status', \App\Models\CustomerDocumentRequest::STATUS_PENDING)
        ->values();
    $pendingDocumentRequestsCount = $pendingDocumentRequests->count();
    $completedDocumentRequests = $customer->documentRequests
        ->where('status', \App\Models\CustomerDocumentRequest::STATUS_FULFILLED)
        ->take(5)
        ->values();
    $contractSignatureBlockedReason = null;
    if (!$customer->email) {
        $contractSignatureBlockedReason = 'Informe um e-mail no cadastro do cliente antes de solicitar a assinatura.';
    } elseif ($pendingDocumentRequestsCount > 0) {
        $contractSignatureBlockedReason = $pendingDocumentRequestsCount === 1
            ? 'Existe 1 solicitacao de documento pendente. O contrato so pode ser enviado depois que o cliente concluir esse envio.'
            : 'Existem ' . $pendingDocumentRequestsCount . ' solicitacoes de documentos pendentes. O contrato so pode ser enviado depois que o cliente concluir esses envios.';
    }
    $canRequestContractSignature = $contractSignatureBlockedReason === null;
    $processFolders = $customer->processos->map(function ($processo) use ($customer) {
        return [
            'processo' => $processo,
            'files' => $customer->files
                ->where('datajud_processo_id', $processo->id)
                ->sortByDesc('created_at')
                ->values(),
        ];
    })->values();
?>

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
                <?php if($customer->cnp): ?><p class="m-0"><span class="font-medium text-gray-700">CPF/CNPJ:</span> <?php echo e($customer->cnp); ?></p><?php endif; ?>
                <?php if($customer->rg): ?><p class="m-0"><span class="font-medium text-gray-700">RG:</span> <?php echo e($customer->rg); ?></p><?php endif; ?>
                <?php if($customer->email): ?><p class="m-0"><span class="font-medium text-gray-700">E-mail:</span> <?php echo e($customer->email); ?></p><?php endif; ?>
                <?php if($customer->mobile_phone): ?><p class="m-0"><span class="font-medium text-gray-700">Celular:</span> <?php echo e($customer->mobile_phone); ?></p><?php endif; ?>
                <?php if($customer->phone): ?><p class="m-0"><span class="font-medium text-gray-700">Telefone:</span> <?php echo e($customer->phone); ?></p><?php endif; ?>
                <?php if($customer->birth_date): ?><p class="m-0"><span class="font-medium text-gray-700">Nascimento:</span> <?php echo e($customer->birth_date->format('d/m/Y')); ?></p><?php endif; ?>
                <?php if($customer->profession): ?><p class="m-0"><span class="font-medium text-gray-700">Profissao:</span> <?php echo e($customer->profession); ?></p><?php endif; ?>
                <?php if($customer->marital_status): ?><p class="m-0"><span class="font-medium text-gray-700">Estado civil:</span> <?php echo e($customer->marital_status); ?></p><?php endif; ?>
                <?php if(!empty($customer->tags)): ?>
                    <div class="pt-2">
                        <p class="m-0 mb-2 font-medium text-gray-700">Etiquetas:</p>
                        <div class="flex flex-wrap gap-2">
                            <?php $__currentLoopData = $customer->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700"><?php echo e($tag); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if(!$customer->cnp && !$customer->email && !$customer->mobile_phone && !$customer->phone && empty($customer->tags)): ?>
                    <p class="m-0 text-gray-500">Nenhum dado adicional informado.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereco</h3>
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
                    <p class="m-0 text-gray-500">Endereco nao informado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden mb-8">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-900">Solicitar assinatura do contrato de prestacao de servicos</h3>
            <p class="mt-1 text-xs text-gray-500">Envie o contrato por e-mail para o cliente somente quando a documentacao pendente estiver completa.</p>
        </div>
        <form method="POST" action="<?php echo e(route('customers.service-contract.send', $customer)); ?>" class="p-4 space-y-4">
            <?php echo csrf_field(); ?>

            <?php if($contractSignatureBlockedReason): ?>
                <div class="rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">
                    <?php echo e($contractSignatureBlockedReason); ?>

                </div>
            <?php else: ?>
                <div class="rounded-md border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
                    Cliente apto para receber o contrato por e-mail e assinar.
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="service_contract_signer_type" class="block text-sm font-medium text-gray-700 mb-1">Contrato firmado entre <span class="text-red-500">*</span></label>
                    <select name="service_contract_signer_type" id="service_contract_signer_type"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['service_contract_signer_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="enterprise" <?php if(old('service_contract_signer_type', 'enterprise') === 'enterprise'): echo 'selected'; endif; ?>>Cliente e escritorio</option>
                        <option value="lawyer" <?php if(old('service_contract_signer_type') === 'lawyer'): echo 'selected'; endif; ?>>Cliente e advogado</option>
                    </select>
                    <?php $__errorArgs = ['service_contract_signer_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div id="service-contract-signer-wrapper">
                    <label for="service_contract_signer_user_id" class="block text-sm font-medium text-gray-700 mb-1">Advogado responsavel</label>
                    <select name="service_contract_signer_user_id" id="service_contract_signer_user_id"
                            class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['service_contract_signer_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <option value="">Selecione</option>
                        <?php $__currentLoopData = $contractSigners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $signer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($signer->id); ?>" <?php if((int) old('service_contract_signer_user_id') === (int) $signer->id): echo 'selected'; endif; ?>>
                                <?php echo e($signer->name); ?><?php echo e($signer->oab_state && $signer->oab_number ? ' - OAB/'.$signer->oab_state.' '.$signer->oab_number : ''); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php if($contractSigners->isEmpty()): ?>
                        <p class="mt-1 text-xs text-amber-700">Nenhum usuario interno ativo foi encontrado para assinar como advogado.</p>
                    <?php endif; ?>
                    <?php $__errorArgs = ['service_contract_signer_user_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="service_contract_subject" class="block text-sm font-medium text-gray-700 mb-1">Objeto do contrato <span class="text-red-500">*</span></label>
                    <input type="text" name="service_contract_subject" id="service_contract_subject"
                           value="<?php echo e(old('service_contract_subject', 'atendimento e prestacao de servicos advocaticios')); ?>" maxlength="255"
                           class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['service_contract_subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['service_contract_subject'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="service_contract_city" class="block text-sm font-medium text-gray-700 mb-1">Cidade de emissao</label>
                    <input type="text" name="service_contract_city" id="service_contract_city"
                           value="<?php echo e(old('service_contract_city', $customer->city)); ?>" maxlength="100"
                           class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 <?php $__errorArgs = ['service_contract_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <?php $__errorArgs = ['service_contract_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <?php $__errorArgs = ['send_service_contract'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div class="flex justify-end">
                <button type="submit"
                        <?php if(!$canRequestContractSignature): echo 'disabled'; endif; ?>
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    Solicitar assinatura do contrato
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-8 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Solicitar documento ao cliente</h3>
                <p class="mt-1 text-xs text-gray-500">Crie uma pendencia geral do cliente ou vinculada a um processo e envie a notificacao por e-mail.</p>
            </div>
            <form method="POST" action="<?php echo e(route('customers.document-requests.store', $customer)); ?>" class="p-4 space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="request-process" class="block text-sm font-medium text-gray-700 mb-1">Processo</label>
                    <select id="request-process" name="datajud_processo_id" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Solicitacao geral do cliente</option>
                        <?php $__currentLoopData = $customer->processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($processo->id); ?>" <?php if((string) old('datajud_processo_id') === (string) $processo->id): echo 'selected'; endif; ?>>
                                <?php echo e($processo->numero_processo); ?><?php echo e($processo->tribunal ? ' - ' . $processo->tribunal : ''); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['datajud_processo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="request-document-type" class="block text-sm font-medium text-gray-700 mb-1">Documento solicitado</label>
                    <select id="request-document-type" name="document_type" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        <?php $__currentLoopData = \App\Models\CustomerFile::DOCUMENT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($key); ?>" <?php if(old('document_type') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label for="request-description" class="block text-sm font-medium text-gray-700 mb-1">Orientacoes para o cliente</label>
                    <textarea id="request-description" name="description" rows="4" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: envie frente e verso, documento atualizado, anexar laudo assinado."><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Solicitar e notificar cliente
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">Solicitacoes de documentos</h3>
                    <p class="mt-1 text-xs text-gray-500">Acompanhe o que ainda esta pendente e o que ja foi enviado pelo cliente.</p>
                </div>
                <span class="text-xs text-gray-500"><?php echo e($customer->documentRequests->count()); ?> solicitacao(oes)</span>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $pendingDocumentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requestItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900"><?php echo e($requestItem->document_type_label); ?></p>
                        <p class="mt-1 text-xs text-amber-700">Pendente</p>
                        <?php if($requestItem->processo): ?>
                            <p class="mt-1 text-xs text-indigo-600">
                                Processo: <?php echo e($requestItem->processo->numero_processo); ?><?php echo e($requestItem->processo->tribunal ? ' - ' . $requestItem->processo->tribunal : ''); ?>

                            </p>
                        <?php endif; ?>
                        <?php if($requestItem->description): ?>
                            <p class="mt-2 text-sm text-gray-600"><?php echo e($requestItem->description); ?></p>
                        <?php endif; ?>
                        <p class="mt-2 text-xs text-gray-400">
                            Solicitado em <?php echo e($requestItem->created_at?->format('d/m/Y H:i')); ?>

                            <?php if($requestItem->requester): ?>
                                por <?php echo e($requestItem->requester->name); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-6 text-sm text-gray-500">Nenhuma solicitacao pendente no momento.</div>
                <?php endif; ?>

                <?php if($completedDocumentRequests->isNotEmpty()): ?>
                    <div class="px-4 py-3 bg-emerald-50/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.08em] text-emerald-700">Atendidas recentemente</p>
                    </div>
                    <?php $__currentLoopData = $completedDocumentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requestItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($requestItem->document_type_label); ?></p>
                            <p class="mt-1 text-xs text-emerald-700">
                                Atendido em <?php echo e($requestItem->fulfilled_at?->format('d/m/Y H:i') ?: $requestItem->updated_at?->format('d/m/Y H:i')); ?>

                            </p>
                            <?php if($requestItem->processo): ?>
                                <p class="mt-1 text-xs text-indigo-600">
                                    Processo: <?php echo e($requestItem->processo->numero_processo); ?><?php echo e($requestItem->processo->tribunal ? ' - ' . $requestItem->processo->tribunal : ''); ?>

                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 m-0">Pasta de anexos</h3>
                <p class="mt-1 text-xs text-gray-500">Arquivos gerais do cliente e anexos separados por processo.</p>
            </div>
            <span class="text-xs text-gray-500"><?php echo e($customer->files->count()); ?> arquivo(s)</span>
        </div>
        <div class="p-4">
            <form id="upload-files-area" method="POST" action="<?php echo e(route('customers.files.store', $customer)); ?>" enctype="multipart/form-data" class="mb-8 rounded-lg border border-dashed border-indigo-200 bg-indigo-50/40 p-4">
                <?php echo csrf_field(); ?>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="xl:col-span-2">
                        <label for="files-input" class="block text-sm font-medium text-gray-700 mb-1">Adicionar arquivo(s)</label>
                        <input type="file" id="files-input" name="files[]" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-medium file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, WebP ou PDF. Max. 5 MB cada arquivo.</p>
                    </div>
                    <div>
                        <label for="upload-process-select" class="block text-sm font-medium text-gray-700 mb-1">Processo</label>
                        <select id="upload-process-select" name="datajud_processo_id" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pasta geral do cliente</option>
                            <?php $__currentLoopData = $customer->processos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($processo->id); ?>" <?php if((string) old('datajud_processo_id') === (string) $processo->id): echo 'selected'; endif; ?>><?php echo e($processo->numero_processo); ?><?php echo e($processo->tribunal ? ' - ' . $processo->tribunal : ''); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <label for="upload-document-type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select id="upload-document-type" name="document_type" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Selecione</option>
                            <?php $__currentLoopData = \App\Models\CustomerFile::DOCUMENT_TYPES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($key); ?>" <?php if(old('document_type') === $key): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="upload-description" class="block text-sm font-medium text-gray-700 mb-1">Descricao</label>
                        <input type="text" id="upload-description" name="description" value="<?php echo e(old('description')); ?>" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: peticao assinada, laudo complementar, documento do autor">
                    </div>
                    <button type="submit" id="upload-submit-btn" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Enviar arquivo(s)
                    </button>
                </div>
                <?php $__errorArgs = ['datajud_processo_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['document_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['files'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['files.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <ul id="pending-files-list" class="list-none p-0 m-0 mt-4 space-y-1 text-sm text-gray-700 max-h-40 overflow-y-auto"></ul>
            </form>

            <div class="space-y-6">
                <section class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 m-0">Pasta geral do cliente</h4>
                            <p class="mt-1 text-xs text-gray-500">Arquivos sem relacao direta com um processo especifico.</p>
                        </div>
                        <span class="text-xs text-gray-500"><?php echo e($generalFiles->count()); ?> arquivo(s)</span>
                    </div>
                    <div class="p-4">
                        <?php if($generalFiles->isEmpty()): ?>
                            <p class="text-sm text-gray-500 m-0">Nenhum arquivo geral anexado.</p>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Arquivo</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Tipo</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Enviado por</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Data</th>
                                            <th scope="col" class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Acoes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $__currentLoopData = $generalFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    <p class="m-0 font-medium"><?php echo e($file->original_name); ?></p>
                                                    <p class="mt-1 text-xs text-gray-500"><?php echo e(number_format($file->size / 1024, 1)); ?> KB</p>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600">
                                                    <?php echo e($file->document_type_label); ?>

                                                    <?php if($file->description): ?>
                                                        <p class="mt-1 text-xs text-gray-500"><?php echo e($file->description); ?></p>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600"><?php echo e($file->uploader_label); ?></td>
                                                <td class="px-4 py-2 text-sm text-gray-600"><?php echo e($file->created_at?->format('d/m/Y H:i')); ?></td>
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
                </section>

                <section class="space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 m-0">Pastas por processo</h4>
                            <p class="mt-1 text-xs text-gray-500">Os anexos ficam organizados individualmente para cada processo do cliente.</p>
                        </div>
                        <span class="text-xs text-gray-500"><?php echo e($customer->processos->count()); ?> processo(s)</span>
                    </div>

                    <?php $__empty_1 = true; $__currentLoopData = $processFolders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $folder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-slate-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 m-0"><?php echo e($folder['processo']->numero_processo); ?></p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <?php echo e($folder['processo']->tribunal ?: 'Tribunal nao informado'); ?>

                                        <?php if($folder['processo']->classe_nome): ?>
                                            - <?php echo e($folder['processo']->classe_nome); ?>

                                        <?php endif; ?>
                                    </p>
                                    <?php if($folder['processo']->responsibleLawyer): ?>
                                        <p class="mt-1 text-xs text-gray-500">
                                            Advogado responsavel: <?php echo e($folder['processo']->responsibleLawyer->name); ?>

                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo e($folder['files']->count()); ?> arquivo(s)</span>
                            </div>
                            <div class="p-4">
                                <?php if($folder['files']->isEmpty()): ?>
                                    <p class="text-sm text-gray-500 m-0">Nenhum anexo vinculado a este processo.</p>
                                <?php else: ?>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Arquivo</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Tipo</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Enviado por</th>
                                                    <th scope="col" class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase">Data</th>
                                                    <th scope="col" class="px-4 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Acoes</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                <?php $__currentLoopData = $folder['files']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-2 text-sm text-gray-900">
                                                            <p class="m-0 font-medium"><?php echo e($file->original_name); ?></p>
                                                            <p class="mt-1 text-xs text-gray-500"><?php echo e(number_format($file->size / 1024, 1)); ?> KB</p>
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-600">
                                                            <?php echo e($file->document_type_label); ?>

                                                            <?php if($file->description): ?>
                                                                <p class="mt-1 text-xs text-gray-500"><?php echo e($file->description); ?></p>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-600"><?php echo e($file->uploader_label); ?></td>
                                                        <td class="px-4 py-2 text-sm text-gray-600"><?php echo e($file->created_at?->format('d/m/Y H:i')); ?></td>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500">
                            Nenhum processo vinculado a este cliente ainda.
                        </div>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</div>

<div id="modal-delete-customer" class="fixed inset-0 z-[10000] hidden items-center justify-center p-4" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50" id="modal-delete-backdrop"></div>
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Excluir cliente</h3>
        <p class="text-gray-600 text-sm mb-4">Tem certeza que deseja excluir este cliente? Os arquivos anexados tambem serao removidos.</p>
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
    var signerType = document.getElementById('service_contract_signer_type');
    var signerWrapper = document.getElementById('service-contract-signer-wrapper');
    var signerSelect = document.getElementById('service_contract_signer_user_id');

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

    function toggleSignerField() {
        if (!signerType || !signerWrapper || !signerSelect) return;

        var requiresLawyer = signerType.value === 'lawyer';
        signerWrapper.style.display = requiresLawyer ? '' : 'none';

        if (!requiresLawyer) {
            signerSelect.value = '';
        }
    }

    if (signerType) {
        signerType.addEventListener('change', toggleSignerField);
        toggleSignerField();
    }

    var filesInput = document.getElementById('files-input');
    var pendingList = document.getElementById('pending-files-list');
 
    function renderPendingList(fileList) {
        if (!pendingList) return;
        pendingList.innerHTML = '';

        if (!fileList || fileList.length === 0) {
            return;
        }

        Array.from(fileList).forEach(function(file) {
            var li = document.createElement('li');
            li.className = 'flex items-center justify-between gap-2 py-1 border-b border-gray-100';
            li.innerHTML = '<span class="truncate">' + (file.name || 'Arquivo') + '</span> <span class="text-gray-500 text-xs">' + (file.size ? Math.round(file.size / 1024) + ' KB' : '') + '</span>';
            pendingList.appendChild(li);
        });
    }

    if (filesInput) {
        filesInput.addEventListener('change', function() {
            renderPendingList(this.files);
        });
    }
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views/customer/show.blade.php ENDPATH**/ ?>