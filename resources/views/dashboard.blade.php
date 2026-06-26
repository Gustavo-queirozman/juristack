@extends('layouts.app')

@section('pageTitle', 'Dashboard')

@section('content')
<div class="w-full max-w-full">
    @if(!empty($userName))
        <p class="mb-2 text-sm text-gray-600">Ola, <span class="font-medium text-gray-900">{{ $userName }}</span>.</p>
    @endif

    @if($isClient)
        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <p class="mb-6 text-sm text-gray-600">
            Este e o seu portal do cliente. Aqui voce envia documentos, acompanha materiais liberados pelo escritorio e consulta o andamento dos seus processos.
        </p>

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Cadastro vinculado</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalClientes) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Processos em acompanhamento</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalProcessos) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Arquivos enviados</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalArquivos) }}</p>
            </div>
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Documentos disponiveis</p>
                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalDocumentos) }}</p>
            </div>
        </div>

        <div class="mb-8 grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-4 py-3">
                    <h2 class="text-sm font-semibold text-gray-900">Enviar documento</h2>
                    <p class="mt-1 text-xs text-gray-500">Selecione o tipo do documento, descreva se necessario e envie um ou mais arquivos.</p>
                </div>
                <form method="POST" action="{{ route('customers.upload') }}" enctype="multipart/form-data" class="space-y-4 p-4">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label for="document_type" class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                            <select id="document_type" name="document_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Selecione</option>
                                @foreach(\App\Models\CustomerFile::DOCUMENT_TYPES as $key => $label)
                                    <option value="{{ $key }}" @selected(old('document_type') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('document_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Descricao</label>
                            <input id="description" name="description" type="text" value="{{ old('description') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: frente e verso do RG">
                            @error('description')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label for="files" class="block text-sm font-medium text-gray-700">Arquivos</label>
                        <input id="files" name="files[]" type="file" multiple accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                        <p class="mt-1 text-xs text-gray-500">Formatos aceitos: JPG, PNG, WebP e PDF. Ate 5 MB por arquivo.</p>
                        @error('file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        @error('files')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        @error('files.*')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
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
                    @forelse($fileChecklist as $item)
                        <div class="flex items-center justify-between gap-3 px-4 py-3">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $item['count'] }} arquivo(s)</p>
                            </div>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $item['count'] > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $item['status'] }}
                            </span>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-gray-500">Nenhum checklist disponivel.</div>
                    @endforelse
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
                    @forelse($clientFiles as $file)
                        <div class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $file->original_name }}</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $file->document_type_label }}
                                        @if($file->description)
                                            · {{ $file->description }}
                                        @endif
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">Enviado em {{ $file->created_at?->format('d/m/Y H:i') }}</p>
                                </div>
                                <a href="{{ route('client.files.download', $file) }}" target="_blank" rel="noopener" class="shrink-0 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                    Abrir
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-gray-500">Voce ainda nao enviou arquivos.</div>
                    @endforelse
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
                    @forelse($clientDocuments as $document)
                        <div class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-medium text-gray-900">{{ $document->title }}</p>
                                    <p class="mt-1 text-xs text-gray-500">{{ \App\Models\Document::TYPES[$document->type] ?? $document->type }}</p>
                                    <p class="mt-1 text-xs text-gray-400">Atualizado em {{ $document->updated_at?->format('d/m/Y H:i') }}</p>
                                </div>
                                @if($document->document_link)
                                    <a href="{{ route('client.documents.download', $document->id) }}" class="shrink-0 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                        Baixar
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-gray-500">Nenhum documento liberado ate o momento.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-gray-900">Andamento dos processos</h2>
                <p class="mt-1 text-xs text-gray-500">Status consolidado com base no ultimo movimento registrado para o seu processo.</p>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($clientProcesses as $process)
                    @php
                        $latestMovement = $process->latestMovement;
                        $statusLabel = $latestMovement?->nome ?: 'Em acompanhamento';
                        $statusDate = $latestMovement?->data_hora ?: $process->datahora_ultima_atualizacao ?: $process->updated_at;
                        $updatedAt = $process->datahora_ultima_atualizacao ?: $process->updated_at;
                    @endphp
                    <div class="px-4 py-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $process->numero_processo }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ $process->tribunal ?: 'Tribunal nao informado' }}
                                    @if($process->grau)
                                        · {{ $process->grau }}
                                    @endif
                                </p>
                                <p class="mt-1 text-xs text-gray-400">
                                    Ultima atualizacao em {{ $updatedAt?->format('d/m/Y H:i') ?: 'Nao informada' }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-indigo-100 bg-indigo-50 px-3 py-2 lg:min-w-[240px]">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-indigo-600">Ultimo status</p>
                                <p class="mt-1 text-sm font-medium text-indigo-900">{{ $statusLabel }}</p>
                                <p class="mt-1 text-xs text-indigo-700">
                                    {{ $statusDate?->format('d/m/Y H:i') ?: 'Sem movimentacao detalhada' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-gray-500">Nenhum processo vinculado ao seu cadastro ainda.</div>
                @endforelse
            </div>
        </div>
    @else
        <p class="mb-6 text-sm text-gray-600">
            Visao geral da sua conta. Acompanhe metricas e acesse rapidamente as principais areas.
        </p>

        @if($inviteEnterprise)
            <div class="mb-6 rounded-xl border border-indigo-200 bg-indigo-50 p-4">
                <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-indigo-900">Link de cadastro do cliente</p>
                        <p class="mt-1 text-sm text-indigo-800">
                            Envie este link para o cliente entrar no portal ja vinculado ao escritorio
                            <span class="font-medium">{{ $inviteEnterprise->name }}</span>.
                        </p>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <input
                            id="dashboard-client-register-link"
                            type="text"
                            readonly
                            value="{{ route('register.client', $inviteEnterprise->slug) }}"
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
        @endif

        <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('customers.index') }}" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Clientes cadastrados</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalClientes) }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">no escopo da conta</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('datajud.salvos') }}" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Processos salvos</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalProcessos) }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">DataJud</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('documents.index') }}" class="group rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-all duration-200 hover:border-indigo-200 hover:shadow-md">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Documentos</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalDocumentos) }}</p>
                        <p class="mt-0.5 text-xs text-gray-400">gerados ou cadastrados</p>
                    </div>
                </div>
            </a>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-medium text-gray-500">Arquivos anexados</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($totalArquivos) }}</p>
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
                <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Novo cliente
                </a>
                <a href="{{ route('datajud.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Pesquisar processos
                </a>
                <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Ver documentos
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="m-0 text-sm font-semibold text-gray-900">Processos salvos recentes</h3>
                    <a href="{{ route('datajud.salvos') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($processosRecentes as $pm)
                        <a href="{{ route('datajud.salvo.show', $pm->id) }}" class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-gray-50">
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-mono text-sm font-medium text-gray-900">{{ $pm->numero_processo ?? '-' }}</p>
                                <p class="text-xs text-gray-500">{{ $pm->tribunal ?? 'DataJud' }} · {{ $pm->updated_at?->diffForHumans() }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-gray-500">
                            Nenhum processo salvo ainda.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="m-0 text-sm font-semibold text-gray-900">Ultimos clientes</h3>
                    <a href="{{ route('customers.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($ultimosClientes as $c)
                        <a href="{{ route('customers.show', $c) }}" class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-gray-50">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                {{ strtoupper(mb_substr($c->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $c->name ?? 'Sem nome' }}</p>
                                <p class="truncate text-xs text-gray-500">{{ $c->email ?? '-' }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-8 text-center text-sm text-gray-500">
                            Nenhum cliente cadastrado.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif
</div>

@if(!$isClient && $inviteEnterprise)
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
@endif
@endsection
