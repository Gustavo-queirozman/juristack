<div class="grid grid-cols-1 gap-6 xl:grid-cols-[1.1fr_0.9fr]">
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome do plano</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $plan->name) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $plan->slug) }}" required class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('slug') border-red-500 @enderror">
                    @error('slug')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descricao curta</label>
                    <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('description') border-red-500 @enderror">{{ old('description', $plan->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Valor</label>
                    <input id="price" name="price" type="text" value="{{ old('price', $plan->price_cents ? number_format($plan->price_cents / 100, 2, ',', '.') : '') }}" placeholder="297,00" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('price') border-red-500 @enderror">
                    @error('price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700">Moeda</label>
                    <input id="currency" name="currency" type="text" maxlength="3" value="{{ old('currency', $plan->currency ?: 'brl') }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm uppercase shadow-sm @error('currency') border-red-500 @enderror">
                    @error('currency')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="billing_interval" class="block text-sm font-medium text-gray-700">Recorrencia</label>
                    <select id="billing_interval" name="billing_interval" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('billing_interval') border-red-500 @enderror">
                        <option value="month" @selected(old('billing_interval', $plan->billing_interval) === 'month')>Mensal</option>
                        <option value="year" @selected(old('billing_interval', $plan->billing_interval) === 'year')>Anual</option>
                    </select>
                    @error('billing_interval')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="interval_count" class="block text-sm font-medium text-gray-700">Multiplicador da recorrencia</label>
                    <input id="interval_count" name="interval_count" type="number" min="1" max="12" value="{{ old('interval_count', $plan->interval_count ?: 1) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('interval_count') border-red-500 @enderror">
                    @error('interval_count')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="trial_days" class="block text-sm font-medium text-gray-700">Teste gratis (dias)</label>
                    <input id="trial_days" name="trial_days" type="number" min="0" max="365" value="{{ old('trial_days', $plan->trial_days) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('trial_days') border-red-500 @enderror">
                    @error('trial_days')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="button_label" class="block text-sm font-medium text-gray-700">Texto do botao</label>
                    <input id="button_label" name="button_label" type="text" value="{{ old('button_label', $plan->button_label) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('button_label') border-red-500 @enderror">
                    @error('button_label')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label for="features_text" class="block text-sm font-medium text-gray-700">Beneficios do plano</label>
                    <textarea id="features_text" name="features_text" rows="8" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('features_text') border-red-500 @enderror">{{ old('features_text', $featuresText) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Um item por linha.</p>
                    @error('features_text')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">Publicacao e destaque</h2>
            <div class="mt-4 space-y-4 text-sm text-gray-700">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active)) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    Plano ativo
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_public" value="1" @checked(old('is_public', $plan->is_public)) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    Exibir na pagina institucional
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $plan->is_featured)) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    Marcar como destaque
                </label>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="contact_only" value="1" @checked(old('contact_only', $plan->contact_only)) class="rounded border-gray-300 text-indigo-600 shadow-sm">
                    Plano comercial sem checkout
                </label>
            </div>

            <div class="mt-4">
                <label for="sort_order" class="block text-sm font-medium text-gray-700">Ordem de exibicao</label>
                <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $plan->sort_order ?: 0) }}" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm @error('sort_order') border-red-500 @enderror">
                @error('sort_order')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">Stripe</h2>
            <div class="mt-4 space-y-3 text-sm text-gray-600">
                <div class="flex items-center justify-between">
                    <span>Integracao</span>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $stripeEnabled ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $stripeEnabled ? 'Ativa' : 'Pendente' }}
                    </span>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Produto Stripe</p>
                    <p class="mt-1 font-mono text-xs text-gray-700">{{ $plan->stripe_product_id ?: 'Ainda nao sincronizado' }}</p>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500">Preco Stripe</p>
                    <p class="mt-1 font-mono text-xs text-gray-700">{{ $plan->stripe_price_id ?: 'Ainda nao sincronizado' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
