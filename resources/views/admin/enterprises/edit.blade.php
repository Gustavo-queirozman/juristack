@extends('layouts.app')

@section('pageTitle', 'Editar Escritorio')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Editar escritorio</h1>
            <p class="mt-1 text-sm text-gray-600">
                Ajuste os dados principais do ambiente {{ $enterprise->name }}.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('office-access.index', ['enterprise_id' => $enterprise->id]) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Ver acessos
            </a>
            <a href="{{ route('register.client', $enterprise->slug) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Link do cliente
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.enterprises.update', $enterprise) }}" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        @include('admin.enterprises._form', ['enterprise' => $enterprise])

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('admin.enterprises.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Voltar</a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Salvar alteracoes
            </button>
        </div>
    </form>

    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 px-4 py-3">
            <h2 class="text-sm font-semibold text-gray-900">Administradores do escritorio</h2>
            <p class="mt-1 text-xs text-gray-500">Use a area de acessos para editar senhas, papeis e novos usuarios.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($enterprise->users as $adminUser)
                <div class="px-4 py-3">
                    <p class="text-sm font-medium text-gray-900">{{ $adminUser->name }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $adminUser->email }}</p>
                </div>
            @empty
                <div class="px-4 py-6 text-sm text-gray-500">Nenhum administrador interno encontrado.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
