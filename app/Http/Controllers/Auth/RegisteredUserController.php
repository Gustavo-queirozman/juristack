<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\Enterprise;
use App\Models\User;
use App\Rules\CpfOuCnpjValido;
use App\Rules\TelefoneCelularValido;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request, ?string $enterpriseSlug = null): View
    {
        $selectedEnterprise = $enterpriseSlug
            ? Enterprise::query()->where('slug', $enterpriseSlug)->first()
            : null;

        $registrationType = $selectedEnterprise ? 'client' : 'office';

        return view('auth.register', [
            'documentTypes' => CustomerFile::DOCUMENT_TYPES,
            'registrationType' => $registrationType,
            'selectedEnterprise' => $selectedEnterprise,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $registrationType = $this->resolveRegistrationType($request);

        if ($registrationType === 'client') {
            return $this->registerClient($request);
        }

        return $this->registerOffice($request);
    }

    private function registerOffice(Request $request): RedirectResponse
    {
        $request->merge([
            'name' => $request->input('name', $request->input('office_name')),
            'email' => $request->input('email', $request->input('office_email')),
            'password' => $request->input('password', $request->input('office_password')),
            'password_confirmation' => $request->input('password_confirmation', $request->input('office_password_confirmation')),
        ]);

        $request->validate([
            'enterprise_name' => ['required', 'string', 'max:255'],
            'enterprise_cnp' => ['nullable', 'string', 'max:30', 'unique:enterprises,cnp'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $enterprise = Enterprise::create([
            'name' => $request->string('enterprise_name')->toString(),
            'cnp' => $request->filled('enterprise_cnp')
                ? preg_replace('/\D/', '', $request->string('enterprise_cnp')->toString())
                : null,
        ]);

        $user = User::create([
            'enterprise_id' => $enterprise->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_ENTERPRISE_ADMIN,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return to_route('dashboard');
    }

    private function registerClient(Request $request): RedirectResponse
    {
        $request->merge([
            'cnp' => $request->filled('cnp')
                ? preg_replace('/\D/', '', $request->string('cnp')->toString())
                : null,
        ]);

        $validated = $request->validate([
            'enterprise_slug' => ['required', 'exists:enterprises,slug'],
            'name' => ['required', 'string', 'max:255'],
            'cnp' => ['required', 'string', 'max:20', new CpfOuCnpjValido, 'unique:customers,cnp'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class, 'unique:customers,email'],
            'mobile_phone' => ['required', 'string', 'max:20', new TelefoneCelularValido],
            'birth_date' => ['nullable', 'date'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'size:2'],
            'profession' => ['nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'identification_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'cpf_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'address_proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'income_proof_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'power_of_attorney_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'medical_report_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ]);

        $enterprise = Enterprise::query()
            ->where('slug', $validated['enterprise_slug'])
            ->first();

        if (! $enterprise) {
            throw ValidationException::withMessages([
                'enterprise_slug' => 'Link de cadastro invalido ou expirado.',
            ]);
        }

        $customer = DB::transaction(function () use ($validated, $enterprise): Customer {
            $user = User::create([
                'enterprise_id' => $enterprise->id,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_CLIENT,
                'is_active' => true,
            ]);

            $customer = Customer::create([
                'user_id' => $user->id,
                'enterprise_id' => $enterprise->id,
                'name' => $validated['name'],
                'cnp' => $validated['cnp'],
                'email' => $validated['email'],
                'mobile_phone' => preg_replace('/\D/', '', $validated['mobile_phone']),
                'birth_date' => $validated['birth_date'] ?? null,
                'city' => $validated['city'] ?? null,
                'state' => isset($validated['state']) ? strtoupper($validated['state']) : null,
                'profession' => $validated['profession'] ?? null,
            ]);

            event(new Registered($user));

            return $customer->load('user');
        });

        $this->storeClientDocumentsFromRegistration($request, $customer);

        Auth::login($customer->user);

        $request->session()->regenerate();
        $request->session()->regenerateToken();

        return to_route('dashboard');
    }

    private function resolveRegistrationType(Request $request): string
    {
        if ($request->input('registration_type') === 'office') {
            return 'office';
        }

        if ($request->input('registration_type') === 'client') {
            return 'client';
        }

        return $request->filled('enterprise_slug') ? 'client' : 'office';
    }

    private function storeClientDocumentsFromRegistration(Request $request, Customer $customer): void
    {
        $documentFields = [
            'identification' => 'identification_file',
            'cpf' => 'cpf_file',
            'address_proof' => 'address_proof_file',
            'income_proof' => 'income_proof_file',
            'power_of_attorney' => 'power_of_attorney_file',
            'medical_report' => 'medical_report_file',
        ];

        foreach ($documentFields as $documentType => $field) {
            if (! $request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            $storedPath = $file->store('customers/' . $customer->id, 'public');

            CustomerFile::create([
                'customer_id' => $customer->id,
                'document_type' => $documentType,
                'path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }
}
