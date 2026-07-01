@extends('layouts.app')

@section('pageTitle', 'Editar Plano SaaS')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Editar plano</h1>
        <p class="mt-1 text-sm text-gray-600">
            Atualize os dados comerciais e a sincronizacao do plano com o Stripe.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.billing.plans.update', $plan) }}" class="space-y-6">
        @csrf
        @method('PUT')
        @include('admin.billing.plans._form')

        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.billing.plans.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Voltar
            </a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Salvar alteracoes
            </button>
        </div>
    </form>
</div>
@endsection
