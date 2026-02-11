@extends('layouts.app')

@section('pageTitle', 'Novo cliente')

@section('content')
<div class="max-w-2xl">
    <p class="text-gray-600 text-sm mb-6">
        Cadastre um novo cliente (Pessoa Física ou Jurídica).
    </p>

    <form method="POST" action="{{ route('clientes.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados do cliente</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select name="type" id="type" required
                            class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                        <option value="PF" {{ old('type', 'PF') === 'PF' ? 'selected' : '' }}>Pessoa Física (PF)</option>
                        <option value="PJ" {{ old('type') === 'PJ' ? 'selected' : '' }}>Pessoa Jurídica (PJ)</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-1">Nome / Razão social <span class="text-red-500">*</span></label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome') }}" required maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('nome') border-red-500 @enderror"
                           placeholder="{{ old('type', 'PF') === 'PJ' ? 'Razão social' : 'Nome completo' }}">
                    @error('nome')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div id="wrap-cpf" class="{{ old('type', 'PF') === 'PJ' ? 'hidden' : '' }}">
                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-1">CPF <span class="text-red-500">*</span></label>
                    <input type="text" name="cpf" id="cpf" value="{{ old('cpf') }}" maxlength="14"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('cpf') border-red-500 @enderror"
                           placeholder="000.000.000-00">
                    @error('cpf')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div id="wrap-cnpj" class="{{ old('type', 'PF') === 'PF' ? 'hidden' : '' }}">
                    <label for="cnpj" class="block text-sm font-medium text-gray-700 mb-1">CNPJ <span class="text-red-500">*</span></label>
                    <input type="text" name="cnpj" id="cnpj" value="{{ old('cnpj') }}" maxlength="18"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('cnpj') border-red-500 @enderror"
                           placeholder="00.000.000/0000-00">
                    @error('cnpj')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="{{ old('telefone') }}" maxlength="20"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('telefone') border-red-500 @enderror">
                    @error('telefone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereço (opcional)</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="logradouro" class="block text-sm font-medium text-gray-700 mb-1">Logradouro</label>
                    <input type="text" name="logradouro" id="logradouro" value="{{ old('logradouro') }}" maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('logradouro') border-red-500 @enderror"
                           placeholder="Rua, Avenida...">
                    @error('logradouro')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="numero" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                        <input type="text" name="numero" id="numero" value="{{ old('numero') }}" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('numero') border-red-500 @enderror">
                        @error('numero')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="complemento" class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                        <input type="text" name="complemento" id="complemento" value="{{ old('complemento') }}" maxlength="100"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('complemento') border-red-500 @enderror">
                        @error('complemento')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="bairro" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                    <input type="text" name="bairro" id="bairro" value="{{ old('bairro') }}" maxlength="100"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('bairro') border-red-500 @enderror">
                    @error('bairro')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cidade" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="cidade" id="cidade" value="{{ old('cidade') }}" maxlength="100"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('cidade') border-red-500 @enderror">
                        @error('cidade')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">UF</label>
                        <select name="estado" id="estado"
                                class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('estado') border-red-500 @enderror">
                            <option value="">UF</option>
                            @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                                <option value="{{ $uf }}" {{ old('estado') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                        @error('estado')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="cep" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="cep" id="cep" value="{{ old('cep') }}" maxlength="10"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('cep') border-red-500 @enderror"
                               placeholder="00000-000">
                        @error('cep')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Cadastrar cliente
            </button>
            <a href="{{ route('clientes.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
(function() {
    var typeEl = document.getElementById('type');
    var wrapCpf = document.getElementById('wrap-cpf');
    var wrapCnpj = document.getElementById('wrap-cnpj');
    var cpfInput = document.getElementById('cpf');
    var cnpjInput = document.getElementById('cnpj');

    function toggleDoc() {
        var isPJ = typeEl.value === 'PJ';
        wrapCpf.classList.toggle('hidden', isPJ);
        wrapCnpj.classList.toggle('hidden', !isPJ);
        if (isPJ) { cpfInput.value = ''; cpfInput.removeAttribute('required'); cnpjInput.setAttribute('required', 'required'); }
        else { cnpjInput.value = ''; cnpjInput.removeAttribute('required'); cpfInput.setAttribute('required', 'required'); }
    }
    if (typeEl) typeEl.addEventListener('change', toggleDoc);

    function maskCpf(v) {
        v = v.replace(/\D/g, '');
        return v.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4').substring(0, 14);
    }
    function maskCnpj(v) {
        v = v.replace(/\D/g, '');
        return v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/, '$1.$2.$3/$4-$5').substring(0, 18);
    }
    if (cpfInput) cpfInput.addEventListener('input', function() { this.value = maskCpf(this.value); });
    if (cnpjInput) cnpjInput.addEventListener('input', function() { this.value = maskCnpj(this.value); });
})();
</script>
@endsection
