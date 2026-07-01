@extends('layouts.app')

@section('pageTitle', 'Planos SaaS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Planos de assinatura</h1>
            <p class="mt-1 text-sm text-gray-600">
                Cadastre os planos exibidos na pagina institucional e sincronize os precos com o Stripe.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.billing.settings.edit') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Credenciais Stripe
            </a>
            <a href="{{ route('admin.billing.plans.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Novo plano
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            {{ session('warning') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $stripeEnabled ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                {{ $stripeEnabled ? 'Stripe pronto para sincronizar' : 'Configure o Stripe antes do checkout' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($plans as $plan)
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-lg font-semibold text-gray-900">{{ $plan->name }}</p>
                            @if($plan->is_featured)
                                <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">Destaque</span>
                            @endif
                            @if($plan->contact_only)
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">Comercial</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-600">{{ $plan->description }}</p>
                        <p class="mt-2 text-sm font-medium text-gray-900">{{ $plan->display_price }}{{ $plan->display_period }}</p>
                        <p class="mt-1 text-xs text-gray-500">Slug: {{ $plan->slug }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-center text-xs text-gray-600">
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $plan->is_public ? 'Sim' : 'Nao' }}</div>
                            <div>Publico</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $plan->is_active ? 'Sim' : 'Nao' }}</div>
                            <div>Ativo</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $plan->stripe_product_id ? 'Ok' : '-' }}</div>
                            <div>Produto</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $plan->stripe_price_id ? 'Ok' : '-' }}</div>
                            <div>Preco</div>
                        </div>
                    </div>
                </div>

                @if(($plan->features ?? []) !== [])
                    <ul class="mt-4 space-y-2 text-sm text-gray-600">
                        @foreach(array_slice($plan->features ?? [], 0, 4) as $feature)
                            <li class="flex items-start gap-2">
                                <span class="mt-1 h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.billing.plans.edit', $plan) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        Editar
                    </a>
                    <a href="{{ route('register', ['plan' => $plan->slug]) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        Abrir cadastro publico
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-8 text-sm text-gray-500 shadow-sm xl:col-span-2">
                Nenhum plano cadastrado ainda.
            </div>
        @endforelse
    </div>

    {{ $plans->links() }}
</div>
@endsection
