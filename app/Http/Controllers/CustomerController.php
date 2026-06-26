<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocumentRequest;
use App\Models\CustomerFile;
use App\Models\DatajudProcesso;
use App\Models\User;
use App\Notifications\CustomerDocumentRequestNotification;
use App\Services\ServiceContractService;
use App\Rules\CepValido;
use App\Rules\CpfOuCnpjValido;
use App\Rules\RgValido;
use App\Rules\TelefoneCelularValido;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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
        $contractSigners = $this->availableContractSignersFor(request()->user());

        return view('customer.create', compact('availableTags', 'contractSigners'));
    }

    public function store(Request $request)
    {
        $actor = $request->user();

        $this->mergeCnpForValidation($request);
        $validated = $request->validate(array_merge(
            $this->customerValidationRules(null),
            $this->serviceContractValidationRules($request, $actor)
        ));
        $validated = $this->normalizeForStorage($validated);
        $serviceContractPayload = $this->extractServiceContractPayload($validated, $actor);

        $customerData = $this->extractCustomerData($validated);
        $customerData['enterprise_id'] = $actor->isAdmin()
            ? ($validated['enterprise_id'] ?? null)
            : $actor->enterprise_id;

        $customer = null;
        DB::transaction(function () use ($customerData, $validated, &$customer): void {
            $customer = Customer::create($customerData);
            $this->syncCustomerLogin($customer, $validated);
        });

        $successMessage = 'Cliente cadastrado com sucesso!';

        if ($customer && $serviceContractPayload !== null) {
            try {
                app(ServiceContractService::class)->createAndSend($customer->fresh(['enterprise', 'user']), $actor, $serviceContractPayload);
                $successMessage = 'Cliente cadastrado com sucesso e contrato enviado para assinatura por e-mail.';
            } catch (\Throwable $exception) {
                Log::error('Falha ao gerar/enviar contrato de prestacao de servicos.', [
                    'customer_id' => $customer->id,
                    'enterprise_id' => $customer->enterprise_id,
                    'error' => $exception->getMessage(),
                ]);

                $successMessage = 'Cliente cadastrado com sucesso, mas o contrato não pôde ser enviado.';
            }
        }

        return redirect()->route('customers.index')
            ->with('success', $successMessage);
    }

    public function show(Customer $customer)
    {
        $this->ensureCustomerAccessible(request()->user(), $customer);
        $customer->load([
            'files.processo',
            'files.uploader',
            'documentRequests.processo',
            'documentRequests.requester',
            'processos' => fn ($query) => $query->latest('updated_at'),
        ]);
        $contractSigners = $this->availableContractSignersFor(request()->user());

        return view('customer.show', compact('customer', 'contractSigners'));
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
        $actor = $request->user();

        $this->ensureCustomerAccessible($actor, $customer);
        $this->validateUploadPayload($request, true);

        $processo = $this->resolveUploadProcess($request, $customer, $actor);

        $count = $this->storeCustomerFiles(
            $customer,
            $this->uploadedFilesFromRequest($request),
            $actor,
            $processo,
            $request->input('document_type'),
            $request->input('description')
        );

        $msg = $count === 1
            ? 'Arquivo enviado e vinculado ao cliente.'
            : $count . ' arquivos enviados e vinculados ao cliente.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $msg,
                'processo' => $processo?->only(['id', 'numero_processo', 'tribunal']),
            ]);
        }

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

    public function storeDocumentRequest(Request $request, Customer $customer)
    {
        $actor = $request->user();

        $this->ensureCustomerAccessible($actor, $customer);

        $validated = $request->validate([
            'datajud_processo_id' => ['nullable', 'integer', 'exists:datajud_processos,id'],
            'document_type' => ['required', Rule::in(array_keys(CustomerFile::DOCUMENT_TYPES))],
            'description' => ['nullable', 'string', 'max:1000'],
        ], [
            'document_type.required' => 'Selecione o tipo do documento solicitado.',
        ]);

        $processo = $this->resolveUploadProcess($request, $customer, $actor);

        $documentRequest = CustomerDocumentRequest::create([
            'enterprise_id' => $customer->enterprise_id,
            'customer_id' => $customer->id,
            'datajud_processo_id' => $processo?->id,
            'requested_by_user_id' => $actor->id,
            'document_type' => $validated['document_type'],
            'description' => $validated['description'] ?? null,
            'status' => CustomerDocumentRequest::STATUS_PENDING,
        ]);

        $documentRequest->loadMissing(['customer.user', 'processo']);

        $notified = $this->notifyCustomerDocumentRequest($documentRequest);

        $message = $notified
            ? 'Solicitacao registrada e cliente notificado por e-mail.'
            : 'Solicitacao registrada, mas o cliente nao possui e-mail para notificacao.';

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', $message);
    }

    public function sendServiceContract(Request $request, Customer $customer)
    {
        $actor = $request->user();

        $this->ensureCustomerAccessible($actor, $customer);

        $validated = $request->validate(
            $this->serviceContractValidationRules($request, $actor, true)
        );

        if (! $customer->email) {
            throw ValidationException::withMessages([
                'send_service_contract' => 'Informe um e-mail do cliente antes de solicitar a assinatura do contrato.',
            ]);
        }

        $pendingRequestsCount = $customer->documentRequests()->pending()->count();
        if ($pendingRequestsCount > 0) {
            throw ValidationException::withMessages([
                'send_service_contract' => 'Nao e possivel solicitar a assinatura enquanto houver documentos pendentes de envio pelo cliente.',
            ]);
        }

        $payload = $this->extractServiceContractPayload($validated, $actor, true);

        try {
            app(ServiceContractService::class)->createAndSend($customer->fresh(['enterprise', 'user']), $actor, $payload);
        } catch (\Throwable $exception) {
            Log::error('Falha ao gerar/enviar contrato de prestacao de servicos.', [
                'customer_id' => $customer->id,
                'enterprise_id' => $customer->enterprise_id,
                'error' => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'send_service_contract' => 'Nao foi possivel enviar o contrato para assinatura. Tente novamente.',
            ]);
        }

        return redirect()
            ->route('customers.show', $customer)
            ->with('success', 'Contrato de prestacao de servicos enviado para assinatura por e-mail.');
    }

    public function uploadFiles(Request $request)
    {
        $user = $request->user();
        abort_unless($user && $user->isClient(), 403);

        $customer = $user->customerProfile;
        abort_unless($customer, 403);

        $this->validateUploadPayload($request);
        $processo = $this->resolveUploadProcess($request, $customer, $user);

        $count = $this->storeCustomerFiles(
            $customer,
            $this->uploadedFilesFromRequest($request),
            $user,
            $processo,
            $request->input('document_type'),
            $request->input('description')
        );

        $fulfilledRequests = $this->fulfillMatchingDocumentRequests(
            $customer,
            $processo,
            $request->input('document_type') ?: 'other',
            $user
        );

        $message = $count === 1
            ? 'Arquivo enviado e vinculado ao seu cadastro.'
            : $count . ' arquivos enviados e vinculados ao seu cadastro.';

        if ($fulfilledRequests > 0) {
            $message .= ' ' . ($fulfilledRequests === 1
                ? '1 solicitacao foi marcada como atendida.'
                : $fulfilledRequests . ' solicitacoes foram marcadas como atendidas.');
        }

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

    protected function serviceContractValidationRules(Request $request, User $actor, bool $requireContract = false): array
    {
        $contractSigners = $this->availableContractSignersFor($actor);
        $shouldRequire = fn () => $requireContract || $request->boolean('send_service_contract');

        return [
            'send_service_contract' => ['nullable', 'boolean'],
            'service_contract_signer_type' => [
                'nullable',
                Rule::requiredIf($shouldRequire),
                Rule::in(['enterprise', 'lawyer']),
            ],
            'service_contract_signer_user_id' => [
                'nullable',
                Rule::requiredIf(fn () => $shouldRequire() && $request->input('service_contract_signer_type') === 'lawyer'),
                Rule::in($contractSigners->pluck('id')->all()),
            ],
            'service_contract_subject' => [
                'nullable',
                Rule::requiredIf($shouldRequire),
                'string',
                'max:255',
            ],
            'service_contract_city' => ['nullable', 'string', 'max:100'],
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
            $validated['is_active'],
            $validated['send_service_contract'],
            $validated['service_contract_signer_type'],
            $validated['service_contract_signer_user_id'],
            $validated['service_contract_subject'],
            $validated['service_contract_city']
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

    private function availableContractSignersFor(User $user)
    {
        if (! $user->enterprise_id) {
            return collect();
        }

        return User::query()
            ->where('enterprise_id', $user->enterprise_id)
            ->whereIn('role', User::INTERNAL_ROLES)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'oab_state', 'oab_number']);
    }

    private function extractServiceContractPayload(array $validated, User $actor, bool $forceContract = false): ?array
    {
        if (! $forceContract && empty($validated['send_service_contract'])) {
            return null;
        }

        $payload = [
            'signer_type' => $validated['service_contract_signer_type'],
            'subject' => $validated['service_contract_subject'],
            'city' => $validated['service_contract_city'] ?? null,
        ];

        if (($validated['service_contract_signer_type'] ?? null) === 'lawyer') {
            $signerUser = $this->availableContractSignersFor($actor)
                ->firstWhere('id', (int) ($validated['service_contract_signer_user_id'] ?? 0));

            if (! $signerUser) {
                throw ValidationException::withMessages([
                    'service_contract_signer_user_id' => 'Selecione um advogado responsável válido.',
                ]);
            }

            $payload['signer_user'] = $signerUser;
        }

        return $payload;
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
            'datajud_processo_id' => ['nullable', 'integer', 'exists:datajud_processos,id'],
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

    private function resolveUploadProcess(Request $request, Customer $customer, User $actor): ?DatajudProcesso
    {
        $processoId = $request->integer('datajud_processo_id');
        if (! $processoId) {
            return null;
        }

        $processo = DatajudProcesso::query()->findOrFail($processoId);

        if ((int) $processo->customer_id !== (int) $customer->id) {
            throw ValidationException::withMessages([
                'datajud_processo_id' => 'O processo informado nao pertence a este cliente.',
            ]);
        }

        if (! $actor->isAdmin() && (int) $processo->enterprise_id !== (int) $actor->enterprise_id) {
            abort(403);
        }

        return $processo;
    }

    private function storeCustomerFiles(
        Customer $customer,
        array $uploadedFiles,
        User $actor,
        ?DatajudProcesso $processo,
        ?string $documentType,
        ?string $description
    ): int
    {
        $count = 0;
        $baseDirectory = $processo
            ? 'customers/' . $customer->id . '/processos/' . $processo->id
            : 'customers/' . $customer->id . '/geral';

        foreach ($uploadedFiles as $uploaded) {
            $storedPath = $uploaded->store($baseDirectory, 'public');

            CustomerFile::create([
                'customer_id' => $customer->id,
                'datajud_processo_id' => $processo?->id,
                'uploaded_by_user_id' => $actor->id,
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

    private function notifyCustomerDocumentRequest(CustomerDocumentRequest $documentRequest): bool
    {
        $customer = $documentRequest->customer;
        $user = $customer?->user;
        $email = $user?->email ?: $customer?->email;

        if (! $email) {
            return false;
        }

        $notification = new CustomerDocumentRequestNotification($documentRequest);

        if ($user && $user->email) {
            $user->notify($notification);
        } else {
            Notification::route('mail', $email)->notify($notification);
        }

        $documentRequest->forceFill(['notified_at' => now()])->save();

        return true;
    }

    private function fulfillMatchingDocumentRequests(
        Customer $customer,
        ?DatajudProcesso $processo,
        string $documentType,
        User $actor
    ): int
    {
        if (! $actor->isClient()) {
            return 0;
        }

        $query = $customer->documentRequests()
            ->pending()
            ->where('document_type', $documentType);

        if ($processo) {
            $query->where(function (Builder $builder) use ($processo) {
                $builder->whereNull('datajud_processo_id')
                    ->orWhere('datajud_processo_id', $processo->id);
            });
        } else {
            $query->whereNull('datajud_processo_id');
        }

        $requests = $query->get();

        if ($requests->isEmpty()) {
            return 0;
        }

        CustomerDocumentRequest::query()
            ->whereKey($requests->pluck('id'))
            ->update([
                'status' => CustomerDocumentRequest::STATUS_FULFILLED,
                'fulfilled_at' => now(),
                'updated_at' => now(),
            ]);

        return $requests->count();
    }
}
