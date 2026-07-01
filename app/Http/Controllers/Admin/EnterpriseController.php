<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EnterpriseController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('search'));

        $enterprises = Enterprise::query()
            ->withCount([
                'users as enterprise_admins_count' => fn ($query) => $query->where('role', User::ROLE_ENTERPRISE_ADMIN),
                'users as lawyers_count' => fn ($query) => $query->where('role', User::ROLE_LAWYER),
                'customers',
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('cnp', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.enterprises.index', [
            'enterprises' => $enterprises,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.enterprises.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'cnp' => $request->filled('cnp')
                ? preg_replace('/\D/', '', $request->string('cnp')->toString())
                : null,
        ]);

        $validated = $request->validate($this->storeRules());
        $enterpriseData = $this->enterprisePayload($validated);

        DB::transaction(function () use ($enterpriseData, $validated): void {
            $enterprise = Enterprise::create($enterpriseData);

            User::create([
                'enterprise_id' => $enterprise->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => $validated['admin_password'],
                'role' => User::ROLE_ENTERPRISE_ADMIN,
                'is_active' => true,
            ]);
        });

        return redirect()
            ->route('admin.enterprises.index')
            ->with('success', 'Escritorio e administrador inicial criados com sucesso.');
    }

    public function edit(Enterprise $enterprise): View
    {
        $enterprise->load([
            'users' => fn ($query) => $query
                ->where('role', User::ROLE_ENTERPRISE_ADMIN)
                ->orderBy('name'),
        ]);

        return view('admin.enterprises.edit', [
            'enterprise' => $enterprise,
        ]);
    }

    public function update(Request $request, Enterprise $enterprise): RedirectResponse
    {
        $request->merge([
            'cnp' => $request->filled('cnp')
                ? preg_replace('/\D/', '', $request->string('cnp')->toString())
                : null,
        ]);

        $validated = $request->validate($this->updateRules($enterprise));

        $enterprise->update($this->enterprisePayload($validated));

        return redirect()
            ->route('admin.enterprises.edit', $enterprise)
            ->with('success', 'Dados do escritorio atualizados com sucesso.');
    }

    private function storeRules(): array
    {
        return array_merge($this->enterpriseRules(), [
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    private function updateRules(Enterprise $enterprise): array
    {
        return $this->enterpriseRules($enterprise);
    }

    private function enterpriseRules(?Enterprise $enterprise = null): array
    {
        $cnpRule = Rule::unique('enterprises', 'cnp');

        if ($enterprise) {
            $cnpRule = $cnpRule->ignore($enterprise->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'cnp' => ['nullable', 'string', 'max:30', $cnpRule],
            'email' => ['nullable', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function enterprisePayload(array $validated): array
    {
        return [
            'name' => $validated['name'],
            'cnp' => ! empty($validated['cnp']) ? preg_replace('/\D/', '', $validated['cnp']) : null,
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
        ];
    }
}
