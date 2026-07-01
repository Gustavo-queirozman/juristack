@extends('layouts.app')

@section('pageTitle', 'Credenciais Stripe')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Credenciais do Stripe</h1>
        <p class="mt-1 text-sm text-gray-600">
            Configure as chaves globais da plataforma para checkout, assinaturas e webhooks.
        </p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <form method="POST" action="{{ route('admin.billing.settings.update') }}" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <div class="flex items-center justify-between rounded-xl border border-indigo-100 bg-indigo-50 px-4 py-3">
                <div>
                    <p class="text-sm font-semibold text-indigo-900">Stripe habilitado</p>
                    <p class="mt-1 text-xs text-indigo-700">Ative apenas depois de salvar as chaves corretas.</p>
                </div>
                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700">
                    <input type="checkbox" name="is_stripe_enabled" value="1" @checked(old('is_stripe_enabled', $settings->is_stripe_enabled)) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    Ativar
                </label>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="stripe_publishable_key" class="block text-sm font-medium text-gray-700">Publishable key</label>
                    <input id="stripe_publishable_key" name="stripe_publishable_key" type="text" value="{{ old('stripe_publishable_key', $settings->stripe_publishable_key) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('stripe_publishable_key') border-red-500 @enderror">
                    @error('stripe_publishable_key')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700">Secret key</label>
                    <input id="stripe_secret_key" name="stripe_secret_key" type="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('stripe_secret_key') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Deixe em branco para manter a chave atual.</p>
                    @error('stripe_secret_key')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="stripe_webhook_secret" class="block text-sm font-medium text-gray-700">Webhook secret</label>
                    <input id="stripe_webhook_secret" name="stripe_webhook_secret" type="password" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('stripe_webhook_secret') border-red-500 @enderror">
                    <p class="mt-1 text-xs text-gray-500">Deixe em branco para manter o segredo atual.</p>
                    @error('stripe_webhook_secret')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="default_currency" class="block text-sm font-medium text-gray-700">Moeda padrao</label>
                    <input id="default_currency" name="default_currency" type="text" maxlength="3" value="{{ old('default_currency', $settings->default_currency) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm uppercase shadow-sm @error('default_currency') border-red-500 @enderror">
                    @error('default_currency')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Salvar credenciais
                </button>
            </div>
        </form>

        <div class="space-y-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-gray-900">Status atual</p>
                <div class="mt-4 space-y-3 text-sm text-gray-600">
                    <div class="flex items-center justify-between">
                        <span>Checkout</span>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $settings->isConfigured() ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $settings->isConfigured() ? 'Pronto' : 'Pendente' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Webhook secret</span>
                        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ filled($settings->stripe_webhook_secret) ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ filled($settings->stripe_webhook_secret) ? 'Configurado' : 'Faltando' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Moeda</span>
                        <span class="font-medium uppercase text-gray-900">{{ $settings->default_currency }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-semibold text-gray-900">URL do webhook</p>
                <p class="mt-2 text-sm text-gray-600">Cadastre esta URL no painel do Stripe para receber eventos de assinatura.</p>
                <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 break-all">
                    {{ $webhookUrl }}
                </div>
                <p class="mt-3 text-xs text-gray-500">Eventos recomendados: <code>checkout.session.completed</code>, <code>customer.subscription.created</code>, <code>customer.subscription.updated</code> e <code>customer.subscription.deleted</code>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
