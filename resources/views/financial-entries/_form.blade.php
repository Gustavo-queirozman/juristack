<div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
        <h3 class="text-sm font-semibold text-gray-900">Dados do lancamento</h3>
    </div>
    <div class="p-4 space-y-4">
        @if($enterprises->isNotEmpty())
            <div>
                <label for="enterprise_id" class="block text-sm font-medium text-gray-700 mb-1">Escritorio <span class="text-red-500">*</span></label>
                <select name="enterprise_id" id="enterprise_id" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('enterprise_id') border-red-500 @enderror">
                    <option value="">Selecione</option>
                    @foreach($enterprises as $enterprise)
                        <option value="{{ $enterprise->id }}" {{ (int) old('enterprise_id', $selectedEnterpriseId ?? (isset($financialEntry) ? $financialEntry->enterprise_id : null)) === (int) $enterprise->id ? 'selected' : '' }}>
                            {{ $enterprise->name }}
                        </option>
                    @endforeach
                </select>
                @error('enterprise_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="entry_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                <select name="entry_type" id="entry_type" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('entry_type') border-red-500 @enderror">
                    <option value="">Selecione</option>
                    @foreach($entryTypeOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('entry_type', isset($financialEntry) ? $financialEntry->entry_type : '') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('entry_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <select name="customer_id" id="customer_id"
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('customer_id') border-red-500 @enderror">
                    <option value="">Selecione</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ (int) old('customer_id', isset($financialEntry) ? $financialEntry->customer_id : null) === (int) $customer->id ? 'selected' : '' }}>
                            {{ $customer->display_name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Obrigatorio para contas a receber.</p>
                @error('customer_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titulo <span class="text-red-500">*</span></label>
            <input type="text" name="title" id="title" value="{{ old('title', isset($financialEntry) ? $financialEntry->title : '') }}" required maxlength="255"
                   class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('title') border-red-500 @enderror">
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Valor <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" value="{{ old('amount', isset($financialEntry) ? number_format((float) $financialEntry->amount, 2, '.', '') : '') }}" required min="0.01" step="0.01"
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('amount') border-red-500 @enderror">
                @error('amount')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="entry_date" class="block text-sm font-medium text-gray-700 mb-1">Vencimento <span class="text-red-500">*</span></label>
                <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date', isset($financialEntry) && $financialEntry->entry_date ? $financialEntry->entry_date->format('Y-m-d') : '') }}" required
                       class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('entry_date') border-red-500 @enderror">
                @error('entry_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Forma de pagamento <span class="text-red-500">*</span></label>
                <select name="payment_method" id="payment_method" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('payment_method') border-red-500 @enderror">
                    <option value="">Selecione</option>
                    @foreach($paymentMethodOptions as $value => $label)
                        <option value="{{ $value }}" {{ old('payment_method', isset($financialEntry) ? $financialEntry->payment_method : '') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('payment_method')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Observacoes</label>
            <textarea name="notes" id="notes" rows="4"
                      class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('notes') border-red-500 @enderror">{{ old('notes', isset($financialEntry) ? $financialEntry->notes : '') }}</textarea>
            @error('notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>

        <label class="flex items-start gap-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
            <input type="checkbox" name="whatsapp_reminder_enabled" value="1"
                   {{ old('whatsapp_reminder_enabled', isset($financialEntry) ? $financialEntry->whatsapp_reminder_enabled : true) ? 'checked' : '' }}
                   class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm">
            <span>
                <span class="block text-sm font-medium text-gray-800">Habilitar cobranca por WhatsApp</span>
                <span class="block text-xs text-gray-500">Quando houver telefone do cliente, o sistema permite cobrar manualmente e prepara o envio automatico por webhook.</span>
            </span>
        </label>
        @error('whatsapp_reminder_enabled')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
