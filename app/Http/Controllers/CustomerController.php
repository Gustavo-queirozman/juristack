<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerFile;
use App\Rules\CepValido;
use App\Rules\CpfOuCnpjValido;
use App\Rules\RgValido;
use App\Rules\TelefoneCelularValido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        $busca = $request->get('busca');
        if ($busca !== null && $busca !== '') {
            $term = '%' . trim($busca) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('cnp', 'like', $term)
                    ->orWhere('mobile_phone', 'like', $term);
            });
        }

        $customers = $query->withCount('files')->latest()->paginate(15)->withQueryString();

        return view('customer.index', compact('customers', 'busca'));
    }

    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $this->mergeCnpForValidation($request);
        $validated = $request->validate($this->customerValidationRules(null));

        $validated = $this->normalizeForStorage($validated);

        if (!empty($validated['tags']) && is_array($validated['tags'])) {
            $validated['tags'] = $validated['tags'];
        }

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function show(Customer $customer)
    {
        $customer->load('files');

        return view('customer.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $this->mergeCnpForValidation($request);
        $validated = $request->validate($this->customerValidationRules($customer));

        $validated = $this->normalizeForStorage($validated);

        $customer->update($validated);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Customer $customer)
    {
        foreach ($customer->files as $file) {
            Storage::disk('public')->delete($file->path);
        }
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Cliente removido com sucesso!');
    }

    /**
     * Upload de arquivo para um cliente (uso pelo admin na tela do cliente).
     */
    public function uploadForCustomer(Request $request, Customer $customer)
    {
        $request->validate([
            'files' => 'required|array|min:1',
            'files.*' => 'file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
        ], [
            'files.required' => 'Selecione ao menos um arquivo.',
            'files.min' => 'Selecione ao menos um arquivo.',
            'files.*.mimes' => 'Tipos permitidos: JPG, PNG, WebP, PDF.',
            'files.*.max' => 'Cada arquivo deve ter no máximo 5 MB.',
        ]);

        $uploadedFiles = $request->file('files');
        if (! is_array($uploadedFiles)) {
            $uploadedFiles = $uploadedFiles ? [$uploadedFiles] : [];
        }

        $count = 0;
        foreach ($uploadedFiles as $uploaded) {
            $storedPath = $uploaded->store('customers/' . $customer->id, 'public');

            CustomerFile::create([
                'customer_id'   => $customer->id,
                'path'          => $storedPath,
                'original_name' => $uploaded->getClientOriginalName(),
                'mime'          => $uploaded->getMimeType(),
                'size'          => $uploaded->getSize(),
            ]);
            $count++;
        }

        $msg = $count === 1
            ? 'Arquivo enviado e vinculado ao cliente.'
            : $count . ' arquivos enviados e vinculados ao cliente.';

        return back()->with('success', $msg);
    }

    /**
     * Visualizar / baixar arquivo do cliente.
     * ?download=1 força download (Content-Disposition: attachment).
     * Para imagens em modo visualização, retorna HTML com a imagem e link "Baixar".
     */
    public function downloadFile(Request $request, Customer $customer, CustomerFile $file)
    {
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
            $urlImage = route('customers.files.download', [$customer, $file]) . '?raw=1';
            $urlDownload = route('customers.files.download', [$customer, $file]) . '?download=1';

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

    /**
     * Remover arquivo do cliente.
     */
    public function destroyFile(Customer $customer, CustomerFile $file)
    {
        if ($file->customer_id !== $customer->id) {
            abort(403);
        }
        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('success', 'Arquivo removido.');
    }

    /**
     * Upload quando o próprio cliente está logado (guard customer).
     */
    public function uploadFiles(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $customer = Auth::guard('customer')->user();

        $storedPath = $request->file('file')->store('customers/' . $customer->id, 'public');

        CustomerFile::create([
            'customer_id'   => $customer->id,
            'path'          => $storedPath,
            'original_name' => $request->file('file')->getClientOriginalName(),
            'mime'          => $request->file('file')->getMimeType(),
            'size'          => $request->file('file')->getSize(),
        ]);

        return back()->with('success', 'Arquivo enviado e vinculado ao seu usuário!');
    }

    /**
     * Regras de validação para criar/editar cliente.
     * Em edição ($customer não null), CPF/CNPJ e e-mail são únicos exceto o próprio registro.
     */
    protected function customerValidationRules(?Customer $customer = null): array
    {
        $estados = array_keys(config('estados', []));

        $cnpRules = ['nullable', 'string', 'max:20', new CpfOuCnpjValido];
        if ($customer) {
            $cnpRules[] = Rule::unique('customers', 'cnp')->ignore($customer->id);
        }

        $emailRules = ['nullable', 'email', 'max:255'];
        if ($customer) {
            $emailRules[] = Rule::unique('customers', 'email')->ignore($customer->id);
        }

        return [
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
            'tags' => 'nullable',
        ];
    }

    /**
     * Mescla CPF/CNPJ só com dígitos no request para a validação unique comparar corretamente com o banco.
     */
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
        return $validated;
    }
}
