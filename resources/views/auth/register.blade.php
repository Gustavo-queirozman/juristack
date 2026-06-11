<x-guest-layout>
    @php
        $isClientInvite = ($registrationType ?? 'office') === 'client' && isset($selectedEnterprise) && $selectedEnterprise;
    @endphp

    <div class="space-y-6">
        @if($isClientInvite)
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Portal do cliente</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Complete seu cadastro para enviar documentos e acompanhar o andamento do seu processo.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="registration_type" value="client">
                <input type="hidden" name="enterprise_slug" value="{{ old('enterprise_slug', $selectedEnterprise->slug) }}">

                <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
                    <p class="text-sm font-medium text-indigo-900">Escritorio responsavel</p>
                    <p class="mt-1 text-base font-semibold text-gray-900">{{ $selectedEnterprise->name }}</p>
                    <p class="mt-1 text-xs text-indigo-700">
                        Este cadastro ja esta vinculado ao link enviado pelo escritorio.
                    </p>
                    @error('enterprise_slug')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="name" value="Nome completo" />
                        <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" autocomplete="name" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="cnp" value="CPF ou CNPJ" />
                            <x-text-input id="cnp" class="mt-1 block w-full" type="text" name="cnp" :value="old('cnp')" required />
                            <x-input-error :messages="$errors->get('cnp')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="mobile_phone" value="Celular" />
                            <x-text-input id="mobile_phone" class="mt-1 block w-full" type="text" name="mobile_phone" :value="old('mobile_phone')" autocomplete="tel" required />
                            <x-input-error :messages="$errors->get('mobile_phone')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="sm:col-span-2">
                            <x-input-label for="email" value="E-mail" />
                            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" autocomplete="username" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="birth_date" value="Nascimento" />
                            <x-text-input id="birth_date" class="mt-1 block w-full" type="date" name="birth_date" :value="old('birth_date')" />
                            <x-input-error :messages="$errors->get('birth_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="city" value="Cidade" />
                            <x-text-input id="city" class="mt-1 block w-full" type="text" name="city" :value="old('city')" />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="state" value="UF" />
                            <x-text-input id="state" class="mt-1 block w-full" type="text" name="state" maxlength="2" :value="old('state')" />
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="profession" value="Profissao" />
                        <x-text-input id="profession" class="mt-1 block w-full" type="text" name="profession" :value="old('profession')" />
                        <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="password" value="Senha" />
                            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" autocomplete="new-password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Confirmar senha" />
                            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" autocomplete="new-password" required />
                        </div>
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-4">
                    <div class="mb-3">
                        <p class="text-sm font-medium text-gray-900">Documentos iniciais</p>
                        <p class="mt-1 text-xs text-gray-500">Envie agora o que ja tiver. Depois voce podera complementar dentro do portal.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label for="identification_file" value="Documento de identificacao" />
                            <input id="identification_file" name="identification_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                            <x-input-error :messages="$errors->get('identification_file')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="cpf_file" value="CPF" />
                            <input id="cpf_file" name="cpf_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                            <x-input-error :messages="$errors->get('cpf_file')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="address_proof_file" value="Comprovante de residencia" />
                            <input id="address_proof_file" name="address_proof_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                            <x-input-error :messages="$errors->get('address_proof_file')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="income_proof_file" value="Comprovante de renda" />
                            <input id="income_proof_file" name="income_proof_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                            <x-input-error :messages="$errors->get('income_proof_file')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="power_of_attorney_file" value="Procuracao" />
                            <input id="power_of_attorney_file" name="power_of_attorney_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                            <x-input-error :messages="$errors->get('power_of_attorney_file')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="medical_report_file" value="Laudo ou documento complementar" />
                            <input id="medical_report_file" name="medical_report_file" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm text-gray-600">
                            <x-input-error :messages="$errors->get('medical_report_file')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">
                        Ja tenho conta
                    </a>

                    <x-primary-button>
                        Criar acesso ao portal
                    </x-primary-button>
                </div>
            </form>
        @else
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Criar escritorio</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Cadastro administrativo para criar o ambiente interno do escritorio.
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="registration_type" value="office">

                <div>
                    <x-input-label for="enterprise_name" value="Nome do escritorio" />
                    <x-text-input id="enterprise_name" class="mt-1 block w-full" type="text" name="enterprise_name" :value="old('enterprise_name')" autocomplete="organization" required />
                    <x-input-error :messages="$errors->get('enterprise_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="enterprise_cnp" value="CNPJ do escritorio" />
                    <x-text-input id="enterprise_cnp" class="mt-1 block w-full" type="text" name="enterprise_cnp" :value="old('enterprise_cnp')" />
                    <x-input-error :messages="$errors->get('enterprise_cnp')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="office_name" value="Nome do administrador" />
                    <x-text-input id="office_name" class="mt-1 block w-full" type="text" name="office_name" :value="old('office_name', old('name'))" autocomplete="name" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="office_email" value="E-mail do administrador" />
                    <x-text-input id="office_email" class="mt-1 block w-full" type="email" name="office_email" :value="old('office_email', old('email'))" autocomplete="username" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="office_password" value="Senha" />
                        <x-text-input id="office_password" class="mt-1 block w-full" type="password" name="office_password" autocomplete="new-password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="office_password_confirmation" value="Confirmar senha" />
                        <x-text-input id="office_password_confirmation" class="mt-1 block w-full" type="password" name="office_password_confirmation" autocomplete="new-password" required />
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">
                        Ja tenho conta
                    </a>

                    <x-primary-button>
                        Criar escritorio
                    </x-primary-button>
                </div>
            </form>
        @endif
    </div>
</x-guest-layout>
