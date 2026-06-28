@extends('layouts.app')

@section('pageTitle', 'Conectar WhatsApp')

@section('content')
@php
    $status = $enterprise?->whatsapp_connection_status;
    $statusLabel = $statusLabels[$status] ?? ($status ? ucfirst($status) : 'Nao configurado');
    $isConnected = in_array($status, ['connected', 'open'], true);
    $webhookUrl = config('services.whatsapp.webhook_url') ?: url('/api/whatsapp/webhook');
@endphp

<div class="w-full max-w-5xl">
    <p class="mb-6 text-sm text-gray-600">
        Conecte o WhatsApp do escritorio para enviar cobrancas, solicitacoes de documentos e notificacoes pelo EvolutionGo.
    </p>

    @if(session('success'))
        <div class="mb-4 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            {{ $errors->first() }}
        </div>
    @endif

    @if($statusError)
        <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ $statusError }}
        </div>
    @endif

    @if($enterprises->isNotEmpty())
        <form method="GET" action="{{ route('whatsapp.connection.show') }}" class="mb-6 flex flex-wrap items-end gap-2">
            <div class="min-w-[280px]">
                <label for="enterprise_id" class="mb-1 block text-sm font-medium text-gray-700">Escritorio</label>
                <select name="enterprise_id" id="enterprise_id" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">Selecione</option>
                    @foreach($enterprises as $option)
                        <option value="{{ $option->id }}" @selected((int) $selectedEnterpriseId === (int) $option->id)>
                            {{ $option->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="rounded-md bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                Abrir conexao
            </button>
        </form>
    @endif

    @if(! $enterprise)
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Selecione um escritorio</h2>
            <p class="mt-2 text-sm text-gray-600">Escolha um escritorio para visualizar ou iniciar a conexao do WhatsApp.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1fr_380px]">
            <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-4">
                    <h2 class="text-base font-semibold text-gray-900">{{ $enterprise->name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">Instancia Evolution: {{ $enterprise->evolution_instance ?: 'ainda nao criada' }}</p>
                </div>

                <div class="grid grid-cols-1 gap-4 p-5 md:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-xs font-semibold uppercase text-gray-500">Status</p>
                        <p class="mt-2 text-lg font-semibold {{ $isConnected ? 'text-emerald-700' : 'text-gray-900' }}">{{ $statusLabel }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-xs font-semibold uppercase text-gray-500">Conectado em</p>
                        <p class="mt-2 text-sm font-medium text-gray-900">{{ $enterprise->whatsapp_connected_at?->format('d/m/Y H:i') ?: 'Pendente' }}</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-xs font-semibold uppercase text-gray-500">Ultima desconexao</p>
                        <p class="mt-2 text-sm font-medium text-gray-900">{{ $enterprise->whatsapp_disconnected_at?->format('d/m/Y H:i') ?: 'Sem registro' }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 p-5">
                    @unless($isEvolutionConfigured)
                        <div class="rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            Configure EVOLUTION_API_BASE_URL no ambiente para habilitar a conexao.
                        </div>
                    @endunless

                    @unless($isWebhookConfigured)
                        <div class="mt-3 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                            Configure WHATSAPP_WEBHOOK_TOKEN para ativar o chatbot com seguranca.
                        </div>
                    @endunless

                    <div class="mt-4 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('whatsapp.connection.connect', ['enterprise_id' => $enterprise->id]) }}">
                            @csrf
                            <button type="submit" @disabled(! $isEvolutionConfigured) class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 disabled:cursor-not-allowed disabled:bg-gray-300">
                                {{ $enterprise->evolution_instance ? 'Gerar novo QR Code' : 'Criar conexao' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('whatsapp.connection.refresh', ['enterprise_id' => $enterprise->id]) }}">
                            @csrf
                            <button type="submit" @disabled(! $enterprise->evolution_instance || ! $isEvolutionConfigured) class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400">
                                Atualizar status
                            </button>
                        </form>

                        @if($enterprise->evolution_instance)
                            <form method="POST" action="{{ route('whatsapp.connection.disconnect', ['enterprise_id' => $enterprise->id]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" @disabled(! $isEvolutionConfigured) class="inline-flex items-center rounded-md border border-red-200 bg-white px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-400">
                                    Desconectar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            <aside class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">QR Code</h2>
                <p class="mt-1 text-sm text-gray-500">Abra o WhatsApp no celular do escritorio e escaneie o codigo.</p>

                <div class="mt-5 flex aspect-square items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-4">
                    @if($enterprise->whatsapp_qr_code)
                        <img src="{{ $enterprise->whatsapp_qr_code }}" alt="QR Code para conectar WhatsApp" class="h-full w-full object-contain">
                    @elseif($isConnected)
                        <div class="text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="mt-3 text-sm font-medium text-gray-900">WhatsApp conectado</p>
                        </div>
                    @else
                        <div class="text-center text-sm text-gray-500">
                            Gere um QR Code para iniciar a conexao.
                        </div>
                    @endif
                </div>

                @if($connection && ($connection['pairing_code'] ?? null))
                    <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase text-gray-500">Codigo de pareamento</p>
                        <p class="mt-1 text-lg font-semibold tracking-wider text-gray-900">{{ $connection['pairing_code'] }}</p>
                    </div>
                @endif

                <div class="mt-4 rounded-md border border-gray-200 bg-gray-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase text-gray-500">Webhook do chatbot</p>
                    <p class="mt-1 break-all text-sm font-medium text-gray-900">{{ $webhookUrl }}</p>
                    <p class="mt-2 text-xs text-gray-500">A conexao cadastra automaticamente esta URL na Evolution para mensagens recebidas. O token configurado em WHATSAPP_WEBHOOK_TOKEN e enviado junto na URL cadastrada.</p>
                </div>
            </aside>
        </div>
    @endif
</div>
@endsection
