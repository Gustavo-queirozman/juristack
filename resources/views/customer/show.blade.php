@extends('layouts.app')

@section('pageTitle', $customer->name)

@section('content')
@php
    $generalFiles = $customer->files->whereNull('datajud_processo_id')->sortByDesc('created_at')->values();
    $pendingDocumentRequests = $customer->documentRequests
        ->where('status', \App\Models\CustomerDocumentRequest::STATUS_PENDING)
        ->values();
    $completedDocumentRequests = $customer->documentRequests
        ->where('status', \App\Models\CustomerDocumentRequest::STATUS_FULFILLED)
        ->take(5)
        ->values();
    $processFolders = $customer->processos->map(function ($processo) use ($customer) {
        return [
            'processo' => $processo,
            'files' => $customer->files
                ->where('datajud_processo_id', $processo->id)
                ->sortByDesc('created_at')
                ->values(),
        ];
    })->values();
@endphp

<div class="w-full max-w-full">
    @if(session('success'))
        <div class="mb-4 rounded-md bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 m-0">{{ $customer->name }}</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('customers.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Voltar
            </a>
            <a href="{{ route('customers.edit', $customer) }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Editar
            </a>
            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline form-delete-customer">
                @csrf
                @method('DELETE')
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
                @if($customer->cnp)<p class="m-0"><span class="font-medium text-gray-700">CPF/CNPJ:</span> {{ $customer->cnp }}</p>@endif
                @if($customer->rg)<p class="m-0"><span class="font-medium text-gray-700">RG:</span> {{ $customer->rg }}</p>@endif
                @if($customer->email)<p class="m-0"><span class="font-medium text-gray-700">E-mail:</span> {{ $customer->email }}</p>@endif
                @if($customer->mobile_phone)<p class="m-0"><span class="font-medium text-gray-700">Celular:</span> {{ $customer->mobile_phone }}</p>@endif
                @if($customer->phone)<p class="m-0"><span class="font-medium text-gray-700">Telefone:</span> {{ $customer->phone }}</p>@endif
                @if($customer->birth_date)<p class="m-0"><span class="font-medium text-gray-700">Nascimento:</span> {{ $customer->birth_date->format('d/m/Y') }}</p>@endif
                @if($customer->profession)<p class="m-0"><span class="font-medium text-gray-700">Profissao:</span> {{ $customer->profession }}</p>@endif
                @if($customer->marital_status)<p class="m-0"><span class="font-medium text-gray-700">Estado civil:</span> {{ $customer->marital_status }}</p>@endif
                @if(!empty($customer->tags))
                    <div class="pt-2">
                        <p class="m-0 mb-2 font-medium text-gray-700">Etiquetas:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($customer->tags as $tag)
                                <span class="inline-flex rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif
                @if(!$customer->cnp && !$customer->email && !$customer->mobile_phone && !$customer->phone && empty($customer->tags))
                    <p class="m-0 text-gray-500">Nenhum dado adicional informado.</p>
                @endif
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereco</h3>
            </div>
            <div class="p-4 space-y-2 text-sm">
                @if($customer->street || $customer->city)
                    <p class="m-0">
                        @if($customer->street){{ $customer->street }}@if($customer->number), {{ $customer->number }}@endif<br>@endif
                        @if($customer->neighborhood){{ $customer->neighborhood }}<br>@endif
                        @if($customer->city){{ $customer->city }}@if($customer->state) - {{ $customer->state }}@endif<br>@endif
                        @if($customer->zip_code)CEP {{ $customer->zip_code }}@endif
                    </p>
                @else
                    <p class="m-0 text-gray-500">Endereco nao informado.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 mb-8 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Solicitar documento ao cliente</h3>
                <p class="mt-1 text-xs text-gray-500">Crie uma pendencia geral do cliente ou vinculada a um processo e envie a notificacao por e-mail.</p>
            </div>
            <form method="POST" action="{{ route('customers.document-requests.store', $customer) }}" class="p-4 space-y-4">
                @csrf
                <div>
                    <label for="request-process" class="block text-sm font-medium text-gray-700 mb-1">Processo</label>
                    <select id="request-process" name="datajud_processo_id" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Solicitacao geral do cliente</option>
                        @foreach($customer->processos as $processo)
                            <option value="{{ $processo->id }}" @selected((string) old('datajud_processo_id') === (string) $processo->id)>
                                {{ $processo->numero_processo }}{{ $processo->tribunal ? ' - ' . $processo->tribunal : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('datajud_processo_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="request-document-type" class="block text-sm font-medium text-gray-700 mb-1">Documento solicitado</label>
                    <select id="request-document-type" name="document_type" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Selecione</option>
                        @foreach(\App\Models\CustomerFile::DOCUMENT_TYPES as $key => $label)
                            <option value="{{ $key }}" @selected(old('document_type') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('document_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="request-description" class="block text-sm font-medium text-gray-700 mb-1">Orientacoes para o cliente</label>
                    <textarea id="request-description" name="description" rows="4" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: envie frente e verso, documento atualizado, anexar laudo assinado.">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
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
                <span class="text-xs text-gray-500">{{ $customer->documentRequests->count() }} solicitacao(oes)</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($pendingDocumentRequests as $requestItem)
                    <div class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $requestItem->document_type_label }}</p>
                        <p class="mt-1 text-xs text-amber-700">Pendente</p>
                        @if($requestItem->processo)
                            <p class="mt-1 text-xs text-indigo-600">
                                Processo: {{ $requestItem->processo->numero_processo }}{{ $requestItem->processo->tribunal ? ' - ' . $requestItem->processo->tribunal : '' }}
                            </p>
                        @endif
                        @if($requestItem->description)
                            <p class="mt-2 text-sm text-gray-600">{{ $requestItem->description }}</p>
                        @endif
                        <p class="mt-2 text-xs text-gray-400">
                            Solicitado em {{ $requestItem->created_at?->format('d/m/Y H:i') }}
                            @if($requestItem->requester)
                                por {{ $requestItem->requester->name }}
                            @endif
                        </p>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-gray-500">Nenhuma solicitacao pendente no momento.</div>
                @endforelse

                @if($completedDocumentRequests->isNotEmpty())
                    <div class="px-4 py-3 bg-emerald-50/60">
                        <p class="text-xs font-semibold uppercase tracking-[0.08em] text-emerald-700">Atendidas recentemente</p>
                    </div>
                    @foreach($completedDocumentRequests as $requestItem)
                        <div class="px-4 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $requestItem->document_type_label }}</p>
                            <p class="mt-1 text-xs text-emerald-700">
                                Atendido em {{ $requestItem->fulfilled_at?->format('d/m/Y H:i') ?: $requestItem->updated_at?->format('d/m/Y H:i') }}
                            </p>
                            @if($requestItem->processo)
                                <p class="mt-1 text-xs text-indigo-600">
                                    Processo: {{ $requestItem->processo->numero_processo }}{{ $requestItem->processo->tribunal ? ' - ' . $requestItem->processo->tribunal : '' }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 m-0">Pasta de anexos</h3>
                <p class="mt-1 text-xs text-gray-500">Arquivos gerais do cliente e anexos separados por processo.</p>
            </div>
            <span class="text-xs text-gray-500">{{ $customer->files->count() }} arquivo(s)</span>
        </div>
        <div class="p-4">
            <div id="upload-files-area" class="mb-8 rounded-lg border border-dashed border-indigo-200 bg-indigo-50/40 p-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="xl:col-span-2">
                        <label for="files-input" class="block text-sm font-medium text-gray-700 mb-1">Adicionar arquivo(s)</label>
                        <input type="file" id="files-input" accept=".jpg,.jpeg,.png,.webp,.pdf" multiple
                               class="block w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:font-medium file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                        <p class="mt-1 text-xs text-gray-500">JPG, PNG, WebP ou PDF. Max. 5 MB cada arquivo.</p>
                    </div>
                    <div>
                        <label for="upload-process-select" class="block text-sm font-medium text-gray-700 mb-1">Processo</label>
                        <select id="upload-process-select" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Pasta geral do cliente</option>
                            @foreach($customer->processos as $processo)
                                <option value="{{ $processo->id }}">{{ $processo->numero_processo }}{{ $processo->tribunal ? ' - ' . $processo->tribunal : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="upload-document-type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        <select id="upload-document-type" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Selecione</option>
                            @foreach(\App\Models\CustomerFile::DOCUMENT_TYPES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-3 lg:flex-row lg:items-end">
                    <div class="flex-1">
                        <label for="upload-description" class="block text-sm font-medium text-gray-700 mb-1">Descricao</label>
                        <input type="text" id="upload-description" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: peticao assinada, laudo complementar, documento do autor">
                    </div>
                    <button type="button" id="upload-submit-btn" disabled class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        Enviar <span id="upload-count">0</span> arquivo(s)
                    </button>
                </div>
                @error('datajud_processo_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('document_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('files')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                @error('files.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <ul id="pending-files-list" class="list-none p-0 m-0 mt-4 space-y-1 text-sm text-gray-700 max-h-40 overflow-y-auto"></ul>
                <p id="upload-error" class="text-sm text-red-600 hidden mt-2"></p>
                <p id="upload-success" class="text-sm text-emerald-600 hidden mt-2"></p>
            </div>

            <div class="space-y-6">
                <section class="rounded-lg border border-gray-200 overflow-hidden">
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 m-0">Pasta geral do cliente</h4>
                            <p class="mt-1 text-xs text-gray-500">Arquivos sem relacao direta com um processo especifico.</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $generalFiles->count() }} arquivo(s)</span>
                    </div>
                    <div class="p-4">
                        @if($generalFiles->isEmpty())
                            <p class="text-sm text-gray-500 m-0">Nenhum arquivo geral anexado.</p>
                        @else
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
                                        @foreach($generalFiles as $file)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-2 text-sm text-gray-900">
                                                    <p class="m-0 font-medium">{{ $file->original_name }}</p>
                                                    <p class="mt-1 text-xs text-gray-500">{{ number_format($file->size / 1024, 1) }} KB</p>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600">
                                                    {{ $file->document_type_label }}
                                                    @if($file->description)
                                                        <p class="mt-1 text-xs text-gray-500">{{ $file->description }}</p>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-600">{{ $file->uploader_label }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-600">{{ $file->created_at?->format('d/m/Y H:i') }}</td>
                                                <td class="px-4 py-2 text-right">
                                                    <span class="inline-flex items-center gap-1">
                                                        <a href="{{ route('customers.files.download', [$customer, $file]) }}" target="_blank" rel="noopener" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Visualizar">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                        </a>
                                                        <a href="{{ route('customers.files.download', [$customer, $file]) }}?download=1" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Baixar">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                        </a>
                                                        <form method="POST" action="{{ route('customers.files.destroy', [$customer, $file]) }}" class="inline form-delete-file">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50 btn-delete-file" title="Remover">
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            </button>
                                                        </form>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 m-0">Pastas por processo</h4>
                            <p class="mt-1 text-xs text-gray-500">Os anexos ficam organizados individualmente para cada processo do cliente.</p>
                        </div>
                        <span class="text-xs text-gray-500">{{ $customer->processos->count() }} processo(s)</span>
                    </div>

                    @forelse($processFolders as $folder)
                        <div class="rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-slate-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 m-0">{{ $folder['processo']->numero_processo }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $folder['processo']->tribunal ?: 'Tribunal nao informado' }}
                                        @if($folder['processo']->classe_nome)
                                            - {{ $folder['processo']->classe_nome }}
                                        @endif
                                    </p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $folder['files']->count() }} arquivo(s)</span>
                            </div>
                            <div class="p-4">
                                @if($folder['files']->isEmpty())
                                    <p class="text-sm text-gray-500 m-0">Nenhum anexo vinculado a este processo.</p>
                                @else
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
                                                @foreach($folder['files'] as $file)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-2 text-sm text-gray-900">
                                                            <p class="m-0 font-medium">{{ $file->original_name }}</p>
                                                            <p class="mt-1 text-xs text-gray-500">{{ number_format($file->size / 1024, 1) }} KB</p>
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-600">
                                                            {{ $file->document_type_label }}
                                                            @if($file->description)
                                                                <p class="mt-1 text-xs text-gray-500">{{ $file->description }}</p>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $file->uploader_label }}</td>
                                                        <td class="px-4 py-2 text-sm text-gray-600">{{ $file->created_at?->format('d/m/Y H:i') }}</td>
                                                        <td class="px-4 py-2 text-right">
                                                            <span class="inline-flex items-center gap-1">
                                                                <a href="{{ route('customers.files.download', [$customer, $file]) }}" target="_blank" rel="noopener" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Visualizar">
                                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                                </a>
                                                                <a href="{{ route('customers.files.download', [$customer, $file]) }}?download=1" class="p-1.5 text-gray-500 hover:text-indigo-600 rounded hover:bg-gray-100" title="Baixar">
                                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                                </a>
                                                                <form method="POST" action="{{ route('customers.files.destroy', [$customer, $file]) }}" class="inline form-delete-file">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="button" class="p-1.5 text-gray-500 hover:text-red-600 rounded hover:bg-red-50 btn-delete-file" title="Remover">
                                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    </button>
                                                                </form>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="rounded-lg border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500">
                            Nenhum processo vinculado a este cliente ainda.
                        </div>
                    @endforelse
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

    var pendingFiles = [];
    var filesInput = document.getElementById('files-input');
    var processSelect = document.getElementById('upload-process-select');
    var documentTypeInput = document.getElementById('upload-document-type');
    var descriptionInput = document.getElementById('upload-description');
    var pendingList = document.getElementById('pending-files-list');
    var uploadBtn = document.getElementById('upload-submit-btn');
    var uploadCount = document.getElementById('upload-count');
    var uploadError = document.getElementById('upload-error');
    var uploadSuccess = document.getElementById('upload-success');
    var uploadUrl = '{{ route('customers.files.store', $customer) }}';
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
            if (processSelect && processSelect.value) {
                formData.append('datajud_processo_id', processSelect.value);
            }
            if (documentTypeInput && documentTypeInput.value) {
                formData.append('document_type', documentTypeInput.value);
            }
            if (descriptionInput && descriptionInput.value) {
                formData.append('description', descriptionInput.value);
            }
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
                        if (processSelect) processSelect.value = '';
                        if (documentTypeInput) documentTypeInput.value = '';
                        if (descriptionInput) descriptionInput.value = '';
                        renderPendingList();
                        if (uploadSuccess) {
                            uploadSuccess.textContent = (data.message || 'Arquivos enviados.') + ' Atualizando...';
                            uploadSuccess.classList.remove('hidden');
                        }
                        window.location.reload();
                    } else {
                        var msg = data.message || 'Erro ao enviar arquivos.';
                        if (data.errors) {
                            if (data.errors.files && data.errors.files[0]) msg = data.errors.files[0];
                            else if (data.errors.datajud_processo_id && data.errors.datajud_processo_id[0]) msg = data.errors.datajud_processo_id[0];
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
@endsection
