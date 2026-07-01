<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700">Nome do escritorio <span class="text-red-500">*</span></label>
        <input id="name" name="name" type="text" value="{{ old('name', $enterprise->name ?? '') }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('name') border-red-500 @enderror">
        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="cnp" class="block text-sm font-medium text-gray-700">CNPJ</label>
        <input id="cnp" name="cnp" type="text" value="{{ old('cnp', $enterprise->cnp ?? '') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('cnp') border-red-500 @enderror">
        @error('cnp')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700">E-mail principal</label>
        <input id="email" name="email" type="email" value="{{ old('email', $enterprise->email ?? '') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('email') border-red-500 @enderror">
        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
        <input id="phone" name="phone" type="text" value="{{ old('phone', $enterprise->phone ?? '') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('phone') border-red-500 @enderror">
        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label for="address" class="block text-sm font-medium text-gray-700">Endereco</label>
        <textarea id="address" name="address" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('address') border-red-500 @enderror">{{ old('address', $enterprise->address ?? '') }}</textarea>
        @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
