@extends('layouts.app')

@section('pageTitle', 'Editar cliente')

@section('content')
@php
    $initialTags = collect(old('tags', $customer->tags ?? []))
        ->filter(fn ($tag) => filled($tag))
        ->values()
        ->all();
@endphp
<div class="max-w-3xl">
    <p class="text-gray-600 text-sm mb-6">
        Edite os dados do cliente.
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

    <form method="POST" action="{{ route('customers.update', $customer) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Dados pessoais</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="cnp" class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ</label>
                        <input type="text" name="cnp" id="cnp" value="{{ old('cnp', $customer->cnp ? (strlen(preg_replace('/\D/', '', $customer->cnp)) === 11 ? \App\Models\Customer::formatarCpf($customer->cnp) : \App\Models\Customer::formatarCnpj($customer->cnp)) : '') }}" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('cnp') border-red-500 @enderror">
                        @error('cnp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="rg" class="block text-sm font-medium text-gray-700 mb-1">RG</label>
                        <input type="text" name="rg" id="rg" value="{{ old('rg', $customer->rg) }}" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $customer->email) }}" maxlength="255"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('email') border-red-500 @enderror">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="mobile_phone" class="block text-sm font-medium text-gray-700 mb-1">Celular</label>
                        <input type="text" name="mobile_phone" id="mobile_phone" value="{{ old('mobile_phone', $customer->mobile_phone) }}" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $customer->phone) }}" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                    <div>
                        <label for="phone_2" class="block text-sm font-medium text-gray-700 mb-1">Telefone 2</label>
                        <input type="text" name="phone_2" id="phone_2" value="{{ old('phone_2', $customer->phone_2) }}" maxlength="20"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-1">Data de nascimento</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $customer->birth_date?->format('Y-m-d')) }}"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gênero</label>
                        <select name="gender" id="gender" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                            <option value="">-</option>
                            <option value="M" {{ old('gender', $customer->gender) === 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('gender', $customer->gender) === 'F' ? 'selected' : '' }}>Feminino</option>
                            <option value="Outro" {{ old('gender', $customer->gender) === 'Outro' ? 'selected' : '' }}>Outro</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="profession" class="block text-sm font-medium text-gray-700 mb-1">Profissão</label>
                        <input type="text" name="profession" id="profession" value="{{ old('profession', $customer->profession) }}" maxlength="100"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                    <div>
                        <label for="marital_status" class="block text-sm font-medium text-gray-700 mb-1">Estado civil</label>
                        <select name="marital_status" id="marital_status" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                            <option value="">-</option>
                            <option value="Solteiro(a)" {{ old('marital_status', $customer->marital_status) === 'Solteiro(a)' ? 'selected' : '' }}>Solteiro(a)</option>
                            <option value="Casado(a)" {{ old('marital_status', $customer->marital_status) === 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                            <option value="Divorciado(a)" {{ old('marital_status', $customer->marital_status) === 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                            <option value="Viúvo(a)" {{ old('marital_status', $customer->marital_status) === 'Viúvo(a)' ? 'selected' : '' }}>Viúvo(a)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Etiquetas</h3>
            </div>
            <div class="p-4 space-y-4">
                <div>
                    <label for="tag-input" class="block text-sm font-medium text-gray-700 mb-1">Cadastrar etiqueta para o cliente</label>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <input type="text" id="tag-input" list="available-tags" maxlength="60" placeholder="Ex.: Processo trabalhista"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                        <button type="button" id="add-tag-btn" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Adicionar etiqueta
                        </button>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Exemplos: Processo trabalhista, Processo criminal, Contrato, Audiência.
                    </p>
                    <datalist id="available-tags">
                        @foreach($availableTags as $tag)
                            <option value="{{ $tag }}"></option>
                        @endforeach
                    </datalist>
                    @error('tags')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    @error('tags.*')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div id="tags-empty-state" class="rounded-md border border-dashed border-gray-300 bg-gray-50 px-3 py-4 text-sm text-gray-500">
                    Nenhuma etiqueta adicionada.
                </div>
                <div id="tags-list" class="hidden flex flex-wrap gap-2"></div>
                <div id="tags-hidden-inputs"></div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-900">Endereço</h3>
            </div>
            <div class="p-4 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="zip_code" id="zip_code" value="{{ old('zip_code', $customer->zip_code ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $customer->zip_code) : '') }}" maxlength="9" placeholder="00000-000"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('zip_code') border-red-500 @enderror">
                        @error('zip_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="state" id="state" class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('state') border-red-500 @enderror">
                            <option value="">- Selecione o estado -</option>
                            @foreach(config('estados', []) as $uf => $nome)
                                <option value="{{ $uf }}" {{ old('state', $customer->state) === $uf ? 'selected' : '' }}>{{ $uf }} - {{ $nome }}</option>
                            @endforeach
                        </select>
                        @error('state')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $customer->city) }}" maxlength="100"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="neighborhood" class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                        <input type="text" name="neighborhood" id="neighborhood" value="{{ old('neighborhood', $customer->neighborhood) }}" maxlength="100"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                    <div>
                        <label for="street" class="block text-sm font-medium text-gray-700 mb-1">Rua</label>
                        <input type="text" name="street" id="street" value="{{ old('street', $customer->street) }}" maxlength="255"
                               class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                    <input type="text" name="number" id="number" value="{{ old('number', $customer->number) }}" maxlength="20"
                           class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm max-w-xs">
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Salvar alterações
            </button>
            <a href="{{ route('customers.show', $customer) }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
(function() {
    var initialTags = @json($initialTags);

    function onlyDigits(s) { return (s || '').replace(/\D/g, ''); }
    function maskCpf(v) {
        v = onlyDigits(v);
        if (v.length <= 11) return v.replace(/(\d{3})(\d{3})(\d{3})(\d{0,2})/, function(_, a, b, c, d) { return (a + (b ? '.' + b : '') + (c ? '.' + c : '') + (d ? '-' + d : '')).trim(); });
        return v.substring(0, 14);
    }
    function maskCnpj(v) {
        v = onlyDigits(v);
        return v.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{0,2})/, function(_, a, b, c, d, e) { return a + (b ? '.' + b : '') + (c ? '.' + c : '') + (d ? '/' + d : '') + (e ? '-' + e : ''); }).substring(0, 18);
    }
    function maskCnp(v) {
        var d = onlyDigits(v);
        if (d.length <= 11) return maskCpf(v);
        return maskCnpj(v);
    }
    function maskRg(v) { return v.replace(/[^\d.\-\sXx]/g, '').substring(0, 20); }
    function maskCep(v) {
        v = onlyDigits(v);
        return v.replace(/(\d{5})(\d{0,3})/, function(_, a, b) { return a + (b ? '-' + b : ''); }).substring(0, 9);
    }
    function maskFone(v) {
        v = onlyDigits(v);
        if (v.length <= 10) return v.replace(/(\d{2})(\d{4})(\d{0,4})/, function(_, a, b, c) { return '(' + a + ') ' + b + (c ? '-' + c : ''); });
        return v.replace(/(\d{2})(\d{5})(\d{0,4})/, function(_, a, b, c) { return '(' + a + ') ' + b + (c ? '-' + c : ''); }).substring(0, 15);
    }
    function normalizeTag(value) {
        return (value || '').trim().replace(/\s+/g, ' ');
    }

    var cnp = document.getElementById('cnp');
    var rg = document.getElementById('rg');
    var zipCode = document.getElementById('zip_code');
    var mobilePhone = document.getElementById('mobile_phone');
    var phone = document.getElementById('phone');
    var phone2 = document.getElementById('phone_2');
    var tagInput = document.getElementById('tag-input');
    var addTagBtn = document.getElementById('add-tag-btn');
    var tagsList = document.getElementById('tags-list');
    var tagsHiddenInputs = document.getElementById('tags-hidden-inputs');
    var tagsEmptyState = document.getElementById('tags-empty-state');
    var selectedTags = initialTags.slice();

    function formatOnLoad(el, fn) { if (el && el.value && onlyDigits(el.value)) el.value = fn(el.value); }

    function renderTags() {
        if (!tagsList || !tagsHiddenInputs || !tagsEmptyState) return;

        tagsList.innerHTML = '';
        tagsHiddenInputs.innerHTML = '';

        if (selectedTags.length === 0) {
            tagsList.classList.add('hidden');
            tagsEmptyState.classList.remove('hidden');
            return;
        }

        tagsList.classList.remove('hidden');
        tagsEmptyState.classList.add('hidden');

        selectedTags.forEach(function(tag, index) {
            var chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700';
            chip.innerHTML = '<span>' + tag + '</span><button type="button" class="text-indigo-500 hover:text-indigo-700" data-index="' + index + '" aria-label="Remover etiqueta">&times;</button>';
            tagsList.appendChild(chip);

            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = tag;
            tagsHiddenInputs.appendChild(input);
        });

        tagsList.querySelectorAll('button[data-index]').forEach(function(button) {
            button.addEventListener('click', function() {
                var index = Number(this.getAttribute('data-index'));
                selectedTags.splice(index, 1);
                renderTags();
            });
        });
    }

    function addTag(value) {
        var normalized = normalizeTag(value);
        if (!normalized) return;

        var alreadyExists = selectedTags.some(function(tag) {
            return tag.toLowerCase() === normalized.toLowerCase();
        });

        if (!alreadyExists) {
            selectedTags.push(normalized);
            renderTags();
        }

        if (tagInput) {
            tagInput.value = '';
            tagInput.focus();
        }
    }

    if (cnp) { cnp.addEventListener('input', function() { this.value = maskCnp(this.value); }); formatOnLoad(cnp, maskCnp); }
    if (rg) rg.addEventListener('input', function() { this.value = maskRg(this.value); });
    if (zipCode) { zipCode.addEventListener('input', function() { this.value = maskCep(this.value); }); formatOnLoad(zipCode, maskCep); }
    if (mobilePhone) { mobilePhone.addEventListener('input', function() { this.value = maskFone(this.value); }); formatOnLoad(mobilePhone, maskFone); }
    if (phone) { phone.addEventListener('input', function() { this.value = maskFone(this.value); }); formatOnLoad(phone, maskFone); }
    if (phone2) { phone2.addEventListener('input', function() { this.value = maskFone(this.value); }); formatOnLoad(phone2, maskFone); }
    if (addTagBtn) addTagBtn.addEventListener('click', function() { addTag(tagInput ? tagInput.value : ''); });
    if (tagInput) {
        tagInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                addTag(this.value);
            }
        });
    }

    renderTags();
})();
</script>
@endsection
