@extends('layouts.app')

@section('pageTitle', 'Escritorios')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Escritorios</h1>
            <p class="mt-1 text-sm text-gray-600">
                Gerencie os ambientes cadastrados e acesse rapidamente os usuarios internos.
            </p>
        </div>
        <a href="{{ route('admin.enterprises.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            Novo escritorio
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.enterprises.index') }}" class="flex flex-col gap-3 lg:flex-row">
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Buscar por nome, slug, e-mail ou CNPJ"
                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">
                    Filtrar
                </button>
                @if($search !== '')
                    <a href="{{ route('admin.enterprises.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Limpar
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($enterprises as $enterprise)
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="min-w-0">
                        <p class="text-lg font-semibold text-gray-900">{{ $enterprise->name }}</p>
                        <p class="mt-1 text-xs text-gray-500">Slug: {{ $enterprise->slug }}</p>
                        @if($enterprise->email)
                            <p class="mt-1 text-sm text-gray-600">{{ $enterprise->email }}</p>
                        @endif
                        @if($enterprise->phone)
                            <p class="mt-1 text-sm text-gray-600">{{ $enterprise->phone }}</p>
                        @endif
                        @if($enterprise->cnp)
                            <p class="mt-1 text-sm text-gray-600">CNPJ: {{ $enterprise->cnp }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-600">
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $enterprise->enterprise_admins_count }}</div>
                            <div>Admins</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $enterprise->lawyers_count }}</div>
                            <div>Advogados</div>
                        </div>
                        <div class="rounded-lg border border-gray-200 px-3 py-2">
                            <div class="font-semibold text-gray-900">{{ $enterprise->customers_count }}</div>
                            <div>Clientes</div>
                        </div>
                    </div>
                </div>

                @if($enterprise->address)
                    <p class="mt-4 text-sm text-gray-600">{{ $enterprise->address }}</p>
                @endif

                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('admin.enterprises.edit', $enterprise) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        Editar
                    </a>
                    <a href="{{ route('office-access.index', ['enterprise_id' => $enterprise->id]) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        Acessos internos
                    </a>
                    <a href="{{ route('register.client', $enterprise->slug) }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                        Link do cliente
                    </a>
                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white px-4 py-8 text-sm text-gray-500 shadow-sm xl:col-span-2">
                Nenhum escritorio encontrado para o filtro informado.
            </div>
        @endforelse
    </div>

    {{ $enterprises->links() }}
</div>
@endsection
