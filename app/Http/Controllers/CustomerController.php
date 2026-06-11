<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerFile;
use App\Models\User;
use App\Rules\CepValido;
use App\Rules\CpfOuCnpjValido;
use App\Rules\RgValido;
use App\Rules\TelefoneCelularValido;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->scopedCustomersQuery($request->user());

        $busca = $request->get('busca');
        if ($busca !== null && $busca !== '') {
            $term = '%' . trim($busca) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('cnp', 'like', $term)
                    ->orWhere('mobile_phone', 'like', $term)
                    ->orWhere('tags', 'like', $term);
            });
        }

        $customers = $query->withCount('files')->latest()->paginate(15)->withQueryString();

        $inviteEnterprise = ! $request->user()->isAdmin() && $request->user()->enterprise_id
            ? $request->user()->enterprise()->first(['id', 'name', 'slug'])
            : null;

        return view('customer.index', compact('customers', 'busca', 'inviteEnterprise'));
    }

    public function create()
    {
        $availableTags = $this->availableTagsFor(request()->user());

        return view('customer.create', compact('availableTags'));
    }

    public function store(Request $request)
    {
        $actor = $request->user();

        $this->mergeCnpForValidation($request);
        $validated = $request->validate($this->customerValidationRules(null));
        $validated = $this->normalizeForStorage($validated);

        $customerData = $this->extractCustomerData($validated);
        $customerData['enterprise_id'] = $actor->isAdmin()
            ? ($validated['enterprise_id'] ?? null)
            : $actor->enterprise_id;

        DB::transaction(function () use ($customerData, $validated): void {
            $customer = Customer::create($customerData);
            $this->syncCustomerLogin($customer, $validated);
        });

        return redirect()->route('customers.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Customer $customer)
    {
        $this->ensureCustomerAccessible(request()->user(), $customer);
        $customer->load('files');

        return view('customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $this->ensureCustomerAccessible(request()->user(), $customer);

        $availableTags = $this->availableTagsFor(request()->user());

        return view('customer.edit', compact('customer', 'availableTags'));
    }

    public function update(Request $request, Customer $customer)
    {
        $actor = $request->user();
        $this->ensureCustomerAccessible($actor, $customer);

        $this->mergeCnpForValidation($request);
        $validated = $request->validate($this->customerValidationRules($customer));
        $validated = $this->normalizeForStorage($validated);

        $customerData = $this->extractCustomerData($validated);
        $customerData['enterprise_id'] = $actor->isAdmin()
            ? ($validated['enterprise_id'] ?? $customer->enterprise_id)
            : $actor->enterprise_id;

        DB::transaction(function () use ($customer, $customerData, $validated): void {
            $customer->update($customerData);
            $this->syncCustomerLogin($customer, $validated);
        });

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        $this->ensureCustomerAccessible(request()->user(), $customer);

        foreach ($customer->files as $file) {
            Storage::disk('public')->delete($file->path);
        }

        if ($customer->user && $customer->user->hasRole(User::ROLE_CLIENT)) {
            $customer->user->delete();
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    public function uploadForCustomer(Request $request, Customer $customer)
    {
        $this->ensureCustomerAccessible($request->user(), $customer);
        $this->validateUploadPayload($request, true);

        $count = $this->storeCustomerFiles(
            $customer,
            $this->uploadedFilesFromRequest($request),
            $request->input('document_type'),
            $request->input('description')
        );

        $msg = $count === 1
            ? 'Arquivo enviado e vinculado ao cliente.'
            : $count . ' arquivos enviados e vinculados ao cliente.';

        return back()->with('success', $msg);
    }

    public function downloadFile(Request $request, Customer $customer, CustomerFile $file)
    {
        $this->ensureCustomerAccessible($request->user(), $customer);

        if ($file->customer_id !== $customer->id) {
            abort(403);
        }

        if (! Storage::disk('public')->exists($file->path)) {
            abort(404);
        }

        $path = Storage::disk('public')->path($file->path);
        $name = $file->original_name ?: basename($file->path);
        $mime = $file->mime ?: 'application/octet-stream';
        $forceDownload = $request->boolean('download');
        $raw = $request->boolean('raw');

        if ($forceDownload) {
            return response()->download($path, $name, [
                'Content-Type' => $mime,
            ]);
        }

        if ($raw) {
            return response()->file($path, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
            ]);
        }

        $isImage = str_starts_with((string) $mime, 'image/');
        if ($isImage) {
            $routeName = $request->user()?->isClient()
                ? 'client.files.download'
                : 'customers.files.download';
            $urlImage = route($routeName, $request->user()?->isClient() ? $file : [$customer, $file]) . '?raw=1';
            $urlDownload = route($routeName, $request->user()?->isClient() ? $file : [$customer, $file]) . '?download=1';

            $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . e($name) . '</title>';
            $html .= '<style>body{margin:0;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;background:#1a1a1a;gap:1rem;padding:1rem;}';
            $html .= 'img{max-width:100%;max-height:85vh;object-fit:contain;}';
            $html .= 'a{color:#6366f1;text-decoration:none;font-family:sans-serif;font-size:0.875rem;} a:hover{text-decoration:underline;}</style></head><body>';
            $html .= '<img src="' . e($urlImage) . '" alt="' . e($name) . '">';
            $html .= '<a href="' . e($urlDownload) . '" download>Baixar arquivo</a></body></html>';

            return response($html)->header('Content-Type', 'text/html; charset=utf-8');
        }

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
        ]);
    }

    public function destroyFile(Customer $customer, CustomerFile $file)
    {
        $this->ensureCustomerAccessible(request()->user(), $customer);

        if ($file->customer_id !== $customer->id) {
            abort(403);
        }

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'Arquivo removido.');
    }

    public function uploadFiles(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->isClient(), 403);

        $customer = $user->customerProfile;
        abort_unless($customer, 403);

        $this->validateUploadPayload($request);

        $count = $this->storeCustomerFiles(
            $customer,
            $this->uploadedFilesFromRequest($request),
            $request->input('document_type'),
            $request->input('description')
        );

        $message = $count === 1
            ? 'Arquivo enviado e vinculado ao seu cadastro.'
            : $count . ' arquivos enviados e vinculados ao seu cadastro.';

        return back()->with('success', $message);
    }

    public function downloadOwnFile(Request $request, CustomerFile $file)
    {
        $user = $request->user();
        abort_unless($user && $user->isClient(), 403);

        $customer = $user->customerProfile;
        abort_unless($customer && (int) $file->customer_id === (int) $customer->id, 403);

        return $this->downloadFile($request, $customer, $file);
    }

    protected function customerValidationRules(?Customer $customer = null): array
    {
        $estados = array_keys(config('estados', []));

        $cnpRules = ['nullable', 'string', 'max:20', new CpfOuCnpjValido];
        $emailRules = ['nullable', 'email', 'max:255'];

        if ($customer) {
            $cnpRules[] = Rule::unique('customers', 'cnp')->ignore($customer->id);
            $emailRules[] = Rule::unique('customers', 'email')->ignore($customer->id);
        } else {
            $cnpRules[] = Rule::unique('customers', 'cnp');
            $emailRules[] = Rule::unique('customers', 'email');
        }

        return [
            'enterprise_id' => ['nullable', 'exists:enterprises,id'],
            'name' => 'required|string|max:255',
            'cnp' => $cnpRules,
            'rg' => ['nullable', 'string', 'max:20', new RgValido],
            'email' => $emailRules,
            'mobile_phone' => ['nullable', 'string', 'max:20', new TelefoneCelularValido],
            'phone' => ['nullable', 'string', 'max:20', new TelefoneCelularValido],
            'phone_2' => ['nullable', 'string', 'max:20', new TelefoneCelularValido],
            'zip_code' => ['nullable', 'string', 'max:20', new CepValido],
            'state' => ['nullable', 'string', 'max:2', 'in:' . implode(',', $estados)],
            'city' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:255',
            'number' => 'nullable|string|max:20',
            'profession' => 'nullable|string|max:100',
            'marital_status' => 'nullable|string|max:50',
            'gender' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'rg_issue_date' => 'nullable|date',
            'cnh' => 'nullable|string|max:50',
            'cnh_issue_date' => 'nullable|date',
            'cnh_expiration_date' => 'nullable|date',
            'father_name' => 'nullable|string|max:255',
            'father_birth_date' => 'nullable|date',
            'mother_name' => 'nullable|string|max:255',
            'mother_birth_date' => 'nullable|date',
            'my_inss_password' => 'nullable|string|max:100',
            'tags' => ['nullable', 'array'],
            'tags.*' => ['nullable', 'string', 'max:60'],
            'create_login' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }

    protected function mergeCnpForValidation(Request $request): void
    {
        $cnp = $request->input('cnp');
        if ($cnp !== null && $cnp !== '') {
            $request->merge(['cnp' => preg_replace('/\D/', '', $cnp)]);
        }
    }

    protected function normalizeForStorage(array $validated): array
    {
        foreach (['cnp', 'zip_code', 'mobile_phone', 'phone', 'phone_2'] as $key) {
            if (! empty($validated[$key])) {
                $validated[$key] = preg_replace('/\D/', '', $validated[$key]);
            }
        }

        if (! empty($validated['state'])) {
            $validated['state'] = strtoupper($validated['state']);
        }

        if (array_key_exists('tags', $validated)) {
            $validated['tags'] = $this->normalizeTags($validated['tags']);
        }

        return $validated;
    }

    private function scopedCustomersQuery(User $user): Builder
    {
        $query = Customer::query();

        if ($user->isClient()) {
            return $query->whereKey($user->customerProfile?->id ?? 0);
        }

        if (! $user->isAdmin()) {
            $query->where('enterprise_id', $user->enterprise_id);
        }

        return $query;
    }

    private function ensureCustomerAccessible(User $user, Customer $customer): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ($user->isClient()) {
            abort_unless((int) $user->customerProfile?->id === (int) $customer->id, 403);
            return;
        }

        if ((int) $customer->enterprise_id !== (int) $user->enterprise_id) {
            abort(403);
        }
    }

    private function extractCustomerData(array $validated): array
    {
        unset(
            $validated['create_login'],
            $validated['password'],
            $validated['password_confirmation'],
            $validated['is_active']
        );

        return $validated;
    }

    private function availableTagsFor(User $user): array
    {
        return $this->scopedCustomersQuery($user)
            ->get(['tags'])
            ->pluck('tags')
            ->filter(fn ($tags) => is_array($tags))
            ->flatten()
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique(fn ($tag) => mb_strtolower($tag))
            ->sortBy(fn ($tag) => mb_strtolower($tag))
            ->values()
            ->all();
    }

    private function normalizeTags(?array $tags): ?array
    {
        if ($tags === null) {
            return null;
        }

        $normalized = collect($tags)
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique(fn ($tag) => mb_strtolower($tag))
            ->values()
            ->all();

        return $normalized === [] ? null : $normalized;
    }

    private function syncCustomerLogin(Customer $customer, array $validated): void
    {
        $shouldManageLogin = ! empty($validated['password'])
            || ! empty($validated['create_login'])
            || $customer->user_id;

        if (! $shouldManageLogin) {
            return;
        }

        if (empty($validated['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Informe um e-mail para criar acesso do cliente.',
            ]);
        }

        $existingQuery = User::query()->where('email', $validated['email']);
        if ($customer->user_id) {
            $existingQuery->where('id', '!=', $customer->user_id);
        }

        if ($existingQuery->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Ja existe um usuario com este e-mail.',
            ]);
        }

        if (! $customer->user && empty($validated['password'])) {
            throw ValidationException::withMessages([
                'password' => 'Informe uma senha para criar o acesso do cliente.',
            ]);
        }

        $attributes = [
            'enterprise_id' => $customer->enterprise_id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => User::ROLE_CLIENT,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ];

        if (! empty($validated['password'])) {
            $attributes['password'] = Hash::make($validated['password']);
        }

        if ($customer->user) {
            $customer->user->update($attributes);
            return;
        }

        $user = User::create($attributes);
        $customer->update(['user_id' => $user->id]);
    }

    private function validateUploadPayload(Request $request, bool $requireArrayFiles = false): void
    {
        $request->validate([
            'document_type' => ['nullable', Rule::in(array_keys(CustomerFile::DOCUMENT_TYPES))],
            'description' => ['nullable', 'string', 'max:255'],
            'files' => [$requireArrayFiles ? 'required' : 'nullable', 'array', 'min:1'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'file' => [$requireArrayFiles ? 'nullable' : 'required_without:files', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
        ], [
            'file.required_without' => 'Selecione ao menos um arquivo.',
            'files.required' => 'Selecione ao menos um arquivo.',
            'files.min' => 'Selecione ao menos um arquivo.',
            'files.*.mimes' => 'Tipos permitidos: JPG, PNG, WebP, PDF.',
            'files.*.max' => 'Cada arquivo deve ter no maximo 5 MB.',
            'file.mimes' => 'Tipos permitidos: JPG, PNG, WebP, PDF.',
            'file.max' => 'Cada arquivo deve ter no maximo 5 MB.',
        ]);

        if ($this->uploadedFilesFromRequest($request) === []) {
            throw ValidationException::withMessages([
                'files' => 'Selecione ao menos um arquivo.',
            ]);
        }
    }

    private function uploadedFilesFromRequest(Request $request): array
    {
        $uploadedFiles = $request->file('files');

        if (! is_array($uploadedFiles)) {
            $uploadedFiles = $uploadedFiles ? [$uploadedFiles] : [];
        }

        if ($uploadedFiles === [] && $request->hasFile('file')) {
            $uploadedFiles = [$request->file('file')];
        }

        return array_values(array_filter($uploadedFiles));
    }

    private function storeCustomerFiles(Customer $customer, array $uploadedFiles, ?string $documentType, ?string $description): int
    {
        $count = 0;

        foreach ($uploadedFiles as $uploaded) {
            $storedPath = $uploaded->store('customers/' . $customer->id, 'public');

            CustomerFile::create([
                'customer_id' => $customer->id,
                'document_type' => $documentType ?: 'other',
                'description' => $description,
                'path' => $storedPath,
                'original_name' => $uploaded->getClientOriginalName(),
                'mime' => $uploaded->getMimeType(),
                'size' => $uploaded->getSize(),
            ]);

            $count++;
        }

        return $count;
    }
}
