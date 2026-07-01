@extends('layouts.app')

@section('pageTitle', 'Novo Escritorio')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Novo escritorio</h1>
        <p class="mt-1 text-sm text-gray-600">
            Crie o ambiente do escritorio e o primeiro administrador interno.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.enterprises.store') }}" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf

        @include('admin.enterprises._form', ['enterprise' => null])

        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
            <h2 class="text-sm font-semibold text-indigo-900">Administrador inicial do escritorio</h2>
            <p class="mt-1 text-xs text-indigo-800">
                Esse usuario sera criado com papel de administrador do escritorio.
            </p>

            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="admin_name" class="block text-sm font-medium text-gray-700">Nome <span class="text-red-500">*</span></label>
                    <input id="admin_name" name="admin_name" type="text" value="{{ old('admin_name') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('admin_name') border-red-500 @enderror">
                    @error('admin_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="admin_email" class="block text-sm font-medium text-gray-700">E-mail <span class="text-red-500">*</span></label>
                    <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('admin_email') border-red-500 @enderror">
                    @error('admin_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="admin_password" class="block text-sm font-medium text-gray-700">Senha <span class="text-red-500">*</span></label>
                    <input id="admin_password" name="admin_password" type="password" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('admin_password') border-red-500 @enderror">
                    @error('admin_password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="admin_password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar senha <span class="text-red-500">*</span></label>
                    <input id="admin_password_confirmation" name="admin_password_confirmation" type="password" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('admin.enterprises.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900">Voltar</a>
            <button type="submit" class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                Criar escritorio
            </button>
        </div>
    </form>
</div>
@endsection
