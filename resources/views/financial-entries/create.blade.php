@extends('layouts.app')

@section('pageTitle', 'Novo lancamento financeiro')

@section('content')
<div class="max-w-4xl">
    <p class="text-gray-600 text-sm mb-6">
        Cadastre contas a pagar e a receber com cliente, vencimento, forma de pagamento e observacoes.
    </p>

    <form method="POST" action="{{ route('financial-entries.store') }}" class="space-y-6">
        @csrf

        @include('financial-entries._form')

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Salvar lancamento
            </button>
            <a href="{{ route('financial-entries.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
