@extends('layouts.app')

@section('pageTitle', 'Novo acesso do escritório')

@section('content')
<div class="max-w-3xl">
    <p class="text-gray-600 text-sm mb-6">
        Cadastre um novo usuário interno para o escritório, definindo o perfil de acesso e a senha inicial.
    </p>

    @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800">
            <p class="font-medium m-0 mb-1">Corrija os erros abaixo:</p>
            <ul class="list-disc list-inside m-0 space-y-0.5">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('office-access.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados do acesso</h3>
            </div>
            <div class="p-4 space-y-4">
                @if($enterprises->isNotEmpty())
                <div>
                    <label for="enterprise_id" class="block text-sm font-medium text-gray-700 mb-1">Escritório <span class="text-red-500">*</span></label>
                    <select name="enterprise_id" id="enterprise_id" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('enterprise_id') border-red-500 @enderror">
                        <option value="">— Selecione o escritório —</option>
                        @foreach($enterprises as $enterprise)
                            <option value="{{ $enterprise->id }}" {{ (int) old('enterprise_id', $selectedEnterpriseId) === (int) $enterprise->id ? 'selected' : '' }}>
                                {{ $enterprise->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('enterprise_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required maxlength="255"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('email') border-red-500 @enderror">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Perfil <span class="text-red-500">*</span></label>
                        <select name="role" id="role" required
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('role') border-red-500 @enderror">
                            <option value="">— Selecione —</option>
                            @foreach($roleOptions as $role => $label)
                                <option value="{{ $role }}" {{ old('role') === $role ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="oab_state" class="block text-sm font-medium text-gray-700 mb-1">UF da OAB</label>
                        <input type="text" name="oab_state" id="oab_state" value="{{ old('oab_state') }}" maxlength="2"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('oab_state') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-gray-500">Opcional. Use quando o acesso for de advogado.</p>
                        @error('oab_state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="oab_number" class="block text-sm font-medium text-gray-700 mb-1">Número da OAB</label>
                        <input type="text" name="oab_number" id="oab_number" value="{{ old('oab_number') }}" maxlength="32"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('oab_number') border-red-500 @enderror">
                        @error('oab_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha inicial <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('password') border-red-500 @enderror">
                        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-gray-700">Criar acesso já ativo</label>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                Criar acesso
            </button>
            <a href="{{ route('office-access.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection
