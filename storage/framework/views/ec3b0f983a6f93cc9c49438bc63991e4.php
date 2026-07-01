<?php $__env->startSection('pageTitle', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="w-full max-w-full">
    <?php if(!empty($userName)): ?>
        <p class="mb-2 text-sm text-gray-600">Ola, <span class="font-medium text-gray-900"><?php echo e($userName); ?></span>.</p>
    <?php endif; ?>

    <?php if($isClient): ?>
        <?php if(session('success')): ?>
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <p class="mb-6 text-sm text-gray-600">
            Este e o seu portal do cliente. Aqui voce envia documentos, acompanha materiais liberados pelo escritorio e consulta o andamento dos seus processos.
        </p>

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Cadastro vinculado</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalClientes)); ?></p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Processos em acompanhamento</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalProcessos)); ?></p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Arquivos enviados</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalArquivos)); ?></p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Documentos disponiveis</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalDocumentos)); ?></p>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h2 class="text-sm font-semibold text-gray-900">Enviar documento</h2>
                    <p class="mt-1 text-xs text-gray-500">Selecione o tipo do documento, descreva se necessario e envie um ou mais arquivos.</p>
                </div>
                <form method="POST" action="<?php echo e(route('customers.upload')); ?>" enctype="multipart/form-data" class="space-y-4 p-4">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="datajud_processo_id" class="block text-sm font-medium text-gray-700">Processo</label>
                            <select id="datajud_processo_id" name="datajud_processo_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Anexo geral do cadastro</option>
                                <?php $__currentLoopData = $clientProcessOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $processOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($processOption->id); ?>" <?php if((string) old('datajud_processo_id') === (string) $processOption->id): echo 'selected'; endif; ?>>
                                        <?php echo e($processOption->numero_processo); ?><?php echo e($processOption->tribunal ? ' - ' . $processOption->tribunal : ''); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Selecione um processo quando o anexo fizer parte dele.</p>
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
                            <label for="document_type" class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                            <select id="document_type" name="document_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                            <label for="description" class="block text-sm font-medium text-gray-700">Descricao</label>
                            <input id="description" name="description" type="text" value="<?php echo e(old('description')); ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: frente e verso do RG">
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="mt-2 text-sm text-red-600"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div>
                        <label for="files" class="block text-sm font-medium text-gray-700">Arquivos</label>
                        <input id="files" name="files[]" type="file" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                        <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, WebP e PDF. Ate 5 MB por arquivo.</p>
                        <?php $__errorArgs = ['file'];
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
                    </div>
                    <div class="flex justify-end">
                        <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Enviar para o escritorio
                        </button>
                    </div>
                </form>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h2 class="text-sm font-semibold text-gray-900">Checklist do portal</h2>
                    <p class="mt-1 text-xs text-gray-500">Resumo do que ja foi anexado no seu cadastro.</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $fileChecklist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-3 px-4 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900"><?php echo e($item['label']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($item['count']); ?> arquivo(s)</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium <?php echo e($item['count'] > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'); ?>">
                                <?php echo e($item['status']); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-6 text-sm text-gray-500">Nenhum checklist disponivel.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-amber-200 bg-white shadow-sm">
                <div class="border-b border-amber-200 px-4 py-3 bg-amber-50/70">
                    <h2 class="text-sm font-semibold text-gray-900">Documentos solicitados</h2>
                    <p class="mt-1 text-xs text-gray-600">Pendencias abertas pelo escritorio para o seu cadastro ou para um processo especifico.</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $clientPendingDocumentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requestItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($requestItem->document_type_label); ?></p>
                            <?php if($requestItem->processo): ?>
                                <p class="mt-1 text-xs text-indigo-600">
                                    Processo: <?php echo e($requestItem->processo->numero_processo); ?><?php echo e($requestItem->processo->tribunal ? ' - ' . $requestItem->processo->tribunal : ''); ?>

                                </p>
                            <?php endif; ?>
                            <?php if($requestItem->description): ?>
                                <p class="mt-2 text-sm text-gray-600"><?php echo e($requestItem->description); ?></p>
                            <?php endif; ?>
                            <p class="mt-2 text-xs text-gray-400">Solicitado em <?php echo e($requestItem->created_at?->format('d/m/Y H:i')); ?></p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-6 text-sm text-gray-500">Nenhum documento pendente no momento.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h2 class="text-sm font-semibold text-gray-900">Solicitacoes atendidas</h2>
                    <p class="mt-1 text-xs text-gray-500">Ultimos pedidos de documentos que ja foram identificados como enviados.</p>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $clientRecentDocumentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $requestItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900"><?php echo e($requestItem->document_type_label); ?></p>
                            <?php if($requestItem->processo): ?>
                                <p class="mt-1 text-xs text-indigo-600">
                                    Processo: <?php echo e($requestItem->processo->numero_processo); ?><?php echo e($requestItem->processo->tribunal ? ' - ' . $requestItem->processo->tribunal : ''); ?>

                                </p>
                            <?php endif; ?>
                            <p class="mt-2 text-xs text-emerald-700">
                                Atendido em <?php echo e($requestItem->fulfilled_at?->format('d/m/Y H:i') ?: $requestItem->updated_at?->format('d/m/Y H:i')); ?>

                            </p>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-6 text-sm text-gray-500">Nenhuma solicitacao atendida ainda.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Arquivos enviados</h2>
                        <p class="mt-1 text-xs text-gray-500">Tudo que voce ja encaminhou pelo portal.</p>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $clientFiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900"><?php echo e($file->original_name); ?></p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        <?php echo e($file->document_type_label); ?>

                                        <?php if($file->description): ?>
                                            · <?php echo e($file->description); ?>

                                        <?php endif; ?>
                                    </p>
                                    <?php if($file->processo): ?>
                                        <p class="mt-1 text-xs text-indigo-600">
                                            Processo: <?php echo e($file->processo->numero_processo); ?><?php echo e($file->processo->tribunal ? ' - ' . $file->processo->tribunal : ''); ?>

                                        </p>
                                    <?php endif; ?>
                                    <p class="mt-1 text-xs text-gray-400">Enviado em <?php echo e($file->created_at?->format('d/m/Y H:i')); ?></p>
                                </div>
                                <a href="<?php echo e(route('client.files.download', $file)); ?>" target="_blank" rel="noopener" class="shrink-0 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                    Abrir
                                </a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-6 text-sm text-gray-500">Voce ainda nao enviou arquivos.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Documentos liberados</h2>
                        <p class="mt-1 text-xs text-gray-500">Arquivos e pecas que o escritorio deixou disponiveis para voce.</p>
                    </div>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $clientDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $document): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900"><?php echo e($document->title); ?></p>
                                    <p class="mt-1 text-xs text-gray-500"><?php echo e(\App\Models\Document::TYPES[$document->type] ?? $document->type); ?></p>
                                    <p class="mt-1 text-xs text-gray-400">Atualizado em <?php echo e($document->updated_at?->format('d/m/Y H:i')); ?></p>
                                </div>
                                <?php if($document->document_link): ?>
                                    <a href="<?php echo e(route('client.documents.download', $document->id)); ?>" class="shrink-0 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        Baixar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-6 text-sm text-gray-500">Nenhum documento liberado ate o momento.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-gray-900">Andamento dos processos</h2>
                <p class="mt-1 text-xs text-gray-500">Status consolidado com base no ultimo movimento registrado para o seu processo.</p>
            </div>
            <div class="divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $clientProcesses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $process): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $statusLabel = $process->latest_movement_name ?: 'Em acompanhamento';
                        $statusDate = $process->latest_movement_date ?: $process->datahora_ultima_atualizacao ?: $process->updated_at;
                        $updatedAt = $process->datahora_ultima_atualizacao ?: $process->updated_at;
                    ?>
                    <div class="px-4 py-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900"><?php echo e($process->numero_processo); ?></p>
                                <p class="mt-1 text-xs text-gray-500">
                                    <?php echo e($process->tribunal ?: 'Tribunal nao informado'); ?>

                                    <?php if($process->grau): ?>
                                        · <?php echo e($process->grau); ?>

                                    <?php endif; ?>
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    Ultima atualizacao em <?php echo e($updatedAt?->format('d/m/Y H:i') ?: 'Nao informada'); ?>

                                </p>
                            </div>
                            <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2 lg:min-w-[240px]">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-indigo-600">Ultimo status do processo</p>
                                <p class="mt-1 text-sm font-medium text-indigo-900"><?php echo e($statusLabel); ?></p>
                                <p class="mt-1 text-xs text-indigo-700">
                                    Ultima movimentacao:
                                    <?php echo e($statusDate?->format('d/m/Y H:i') ?: 'Sem movimentacao detalhada'); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="px-4 py-6 text-sm text-gray-500">Nenhum processo vinculado ao seu cadastro ainda.</div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="mb-6 text-sm text-gray-600">
            Visao geral da sua conta. Acompanhe metricas e acesse rapidamente as principais areas.
        </p>

        <?php if($inviteEnterprise): ?>
            <div class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Link de cadastro do cliente</p>
                        <p class="mt-1 text-sm text-indigo-800">
                            Envie este link para o cliente entrar no portal ja vinculado ao escritorio
                            <span class="font-medium"><?php echo e($inviteEnterprise->name); ?></span>.
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input
                            id="dashboard-client-register-link"
                            type="text"
                            readonly
                            value="<?php echo e(route('register.client', $inviteEnterprise->slug)); ?>"
                            class="min-w-[320px] rounded-md border border-indigo-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm"
                        >
                        <button
                            type="button"
                            id="dashboard-copy-client-register-link"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                        >
                            Copiar link
                        </button>
                    </div>
                </div>
                <p id="dashboard-copy-client-register-feedback" class="mt-2 hidden text-sm text-emerald-700">Link copiado.</p>
            </div>
        <?php endif; ?>

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="<?php echo e(route('customers.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Clientes cadastrados</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalClientes)); ?></p>
                        <p class="mt-0.5 text-xs text-gray-400">no escopo da conta</p>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('datajud.salvos')); ?>" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Processos salvos</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalProcessos)); ?></p>
                        <p class="mt-0.5 text-xs text-gray-400">DataJud</p>
                    </div>
                </div>
            </a>

            <a href="<?php echo e(route('documents.index')); ?>" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Documentos</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalDocumentos)); ?></p>
                        <p class="mt-0.5 text-xs text-gray-400">gerados ou cadastrados</p>
                    </div>
                </div>
            </a>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Arquivos anexados</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900"><?php echo e(number_format($totalArquivos)); ?></p>
                        <p class="mt-0.5 text-xs text-gray-400">total no escopo</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-8 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                <h3 class="text-sm font-semibold text-gray-900">Acesso rapido</h3>
            </div>
            <div class="flex flex-wrap gap-3 p-4">
                <a href="<?php echo e(route('customers.create')); ?>" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Novo cliente
                </a>
                <a href="<?php echo e(route('datajud.index')); ?>" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Pesquisar processos
                </a>
                <a href="<?php echo e(route('documents.index')); ?>" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Ver documentos
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="m-0 text-sm font-semibold text-gray-900">Processos salvos recentes</h3>
                    <a href="<?php echo e(route('datajud.salvos')); ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $processosRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('datajud.salvo.show', $pm->id)); ?>" class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-gray-50">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-mono text-sm font-medium text-gray-900"><?php echo e($pm->numero_processo ?? '-'); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($pm->tribunal ?? 'DataJud'); ?> · <?php echo e($pm->updated_at?->diffForHumans()); ?></p>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-8 text-center text-sm text-gray-500">
                            Nenhum processo salvo ainda.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="m-0 text-sm font-semibold text-gray-900">Ultimos clientes</h3>
                    <a href="<?php echo e(route('customers.index')); ?>" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $ultimosClientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('customers.show', $c)); ?>" class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-gray-50">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                <?php echo e(strtoupper(mb_substr($c->name ?? '?', 0, 1))); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900"><?php echo e($c->name ?? 'Sem nome'); ?></p>
                                <p class="truncate text-xs text-gray-500"><?php echo e($c->email ?? '-'); ?></p>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-4 py-8 text-center text-sm text-gray-500">
                            Nenhum cliente cadastrado.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if(!$isClient && $inviteEnterprise): ?>
    <script>
        (function () {
            var copyButton = document.getElementById('dashboard-copy-client-register-link');
            var copyInput = document.getElementById('dashboard-client-register-link');
            var copyFeedback = document.getElementById('dashboard-copy-client-register-feedback');

            if (!copyButton || !copyInput) {
                return;
            }

            copyButton.addEventListener('click', async function () {
                try {
                    await navigator.clipboard.writeText(copyInput.value);
                } catch (error) {
                    copyInput.select();
                    document.execCommand('copy');
                }

                if (copyFeedback) {
                    copyFeedback.classList.remove('hidden');
                    setTimeout(function () {
                        copyFeedback.classList.add('hidden');
                    }, 2500);
                }
            });
        })();
    </script>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\TECNOLOGIA\OneDrive - Faculdade Atenas\Área de Trabalho\juristack\resources\views/dashboard.blade.php ENDPATH**/ ?>