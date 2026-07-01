@extends('layouts.app')

@section('pageTitle', 'Painel Administrativo')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Painel administrativo</h1>
            <p class="mt-1 text-sm text-gray-600">
                Visao global do JuriStack para administradores do sistema.
            </p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.enterprises.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Novo escritorio
            </a>
            <a href="{{ route('office-access.index') }}" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Gerenciar acessos internos
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Escritorios</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['enterprises']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Admins globais</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['global_admins']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Admins de escritorio</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['enterprise_admins']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Advogados</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['lawyers']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Usuarios clientes</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['clients']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Cadastros de clientes</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['customers']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Documentos</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['documents']) }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Lancamentos financeiros</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($stats['financial_entries']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">Escritorios recentes</h2>
                    <p class="mt-1 text-xs text-gray-500">Ultimos ambientes criados na plataforma.</p>
                </div>
                <a href="{{ route('admin.enterprises.index') }}" class="text-xs font-medium text-indigo-600 hover:text-indigo-800">Ver todos</a>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($latestEnterprises as $enterprise)
                    <div class="px-4 py-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $enterprise->name }}</p>
                                <p class="mt-1 text-xs text-gray-500">
                                    Slug: {{ $enterprise->slug }}
                                    @if($enterprise->cnp)
                                        | CNPJ: {{ $enterprise->cnp }}
                                    @endif
                                </p>
                                <p class="mt-1 text-xs text-gray-400">Criado em {{ $enterprise->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="grid grid-cols-3 gap-2 text-center text-xs text-gray-600">
                                <div class="rounded-lg border border-gray-200 px-3 py-2">
                                    <div class="font-semibold text-gray-900">{{ $enterprise->enterprise_admins_count }}</div>
                                    <div>Admins</div>
                                </div>
                                <div class="rounded-lg border border-gray-200 px-3 py-2">
                                    <div class="font-semibold text-gray-900">{{ $enterprise->internal_users_count }}</div>
                                    <div>Equipe</div>
                                </div>
                                <div class="rounded-lg border border-gray-200 px-3 py-2">
                                    <div class="font-semibold text-gray-900">{{ $enterprise->customers_count }}</div>
                                    <div>Clientes</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('admin.enterprises.edit', $enterprise) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Editar escritorio
                            </a>
                            <a href="{{ route('office-access.index', ['enterprise_id' => $enterprise->id]) }}" class="inline-flex items-center rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                Ver acessos
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-gray-500">Nenhum escritorio cadastrado ainda.</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200 px-4 py-3">
                <h2 class="text-sm font-semibold text-gray-900">Administradores de escritorio</h2>
                <p class="mt-1 text-xs text-gray-500">Ultimos acessos principais criados para operacao.</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentOfficeAdmins as $admin)
                    <div class="px-4 py-3">
                        <p class="text-sm font-medium text-gray-900">{{ $admin->name }}</p>
                        <p class="mt-1 text-xs text-gray-500">{{ $admin->email }}</p>
                        <p class="mt-1 text-xs text-indigo-700">{{ $admin->enterprise?->name ?? 'Sem escritorio vinculado' }}</p>
                    </div>
                @empty
                    <div class="px-4 py-6 text-sm text-gray-500">Nenhum administrador de escritorio cadastrado ainda.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
