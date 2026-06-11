<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OfficeAccessController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        $users = $this->scopedUsersQuery($actor)
            ->with('enterprise')
            ->when($actor->isAdmin() && $request->filled('enterprise_id'), function (Builder $query) use ($request) {
                $query->where('enterprise_id', $request->integer('enterprise_id'));
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $enterprises = $actor->isAdmin()
            ? Enterprise::orderBy('name')->get(['id', 'name'])
            : collect();

        $inviteEnterprise = $actor->isAdmin()
            ? ($request->filled('enterprise_id')
                ? Enterprise::query()->find($request->integer('enterprise_id'), ['id', 'name', 'slug'])
                : null)
            : Enterprise::query()->find($actor->enterprise_id, ['id', 'name', 'slug']);

        return view('office-access.index', [
            'users' => $users,
            'enterprises' => $enterprises,
            'inviteEnterprise' => $inviteEnterprise,
            'selectedEnterpriseId' => $request->integer('enterprise_id') ?: null,
            'roleLabels' => User::roleLabels(),
        ]);
    }

    public function create(Request $request)
    {
        $actor = $request->user();

        return view('office-access.create', [
            'roleOptions' => User::internalRoleLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
            'selectedEnterpriseId' => $actor->isAdmin() ? old('enterprise_id') : $actor->enterprise_id,
        ]);
    }

    public function store(Request $request)
    {
        $actor = $request->user();
        $validated = $request->validate($this->rules($actor));

        $enterpriseId = $this->resolveEnterpriseId($actor, $validated['enterprise_id'] ?? null);

        User::create([
            'enterprise_id' => $enterpriseId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        return redirect()->route('office-access.index')
            ->with('success', 'Acesso do escritório criado com sucesso.');
    }

    public function edit(Request $request, int $user)
    {
        $actor = $request->user();
        $target = $this->scopedUsersQuery($actor)->with('enterprise')->findOrFail($user);

        return view('office-access.edit', [
            'accessUser' => $target,
            'roleOptions' => User::internalRoleLabels(),
            'enterprises' => $actor->isAdmin() ? Enterprise::orderBy('name')->get(['id', 'name']) : collect(),
        ]);
    }

    public function update(Request $request, int $user)
    {
        $actor = $request->user();
        $target = $this->scopedUsersQuery($actor)->findOrFail($user);
        $this->guardAgainstSelfManagement($actor, $target);

        $validated = $request->validate($this->rules($actor, $target));
        $enterpriseId = $this->resolveEnterpriseId($actor, $validated['enterprise_id'] ?? $target->enterprise_id);

        $target->fill([
            'enterprise_id' => $enterpriseId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ]);

        if (! empty($validated['password'])) {
            $target->password = $validated['password'];
        }

        $this->ensureEnterpriseAdminStillExists($target, $validated['role'], (bool) ($validated['is_active'] ?? true), $enterpriseId);

        $target->save();

        return redirect()->route('office-access.index')
            ->with('success', 'Acesso atualizado com sucesso.');
    }

    public function destroy(Request $request, int $user)
    {
        $actor = $request->user();
        $target = $this->scopedUsersQuery($actor)->findOrFail($user);
        $this->guardAgainstSelfManagement($actor, $target);
        $this->ensureEnterpriseAdminStillExists($target, null, false, $target->enterprise_id, true);

        $target->delete();

        return redirect()->route('office-access.index')
            ->with('success', 'Acesso removido com sucesso.');
    }

    private function scopedUsersQuery(User $actor): Builder
    {
        $query = User::query()->whereIn('role', User::INTERNAL_ROLES);

        if (! $actor->isAdmin()) {
            $query->where('enterprise_id', $actor->enterprise_id);
        }

        return $query;
    }

    private function rules(User $actor, ?User $target = null): array
    {
        $emailRule = Rule::unique('users', 'email');
        if ($target) {
            $emailRule = $emailRule->ignore($target->id);
        }

        $enterpriseRules = $actor->isAdmin()
            ? ['required', 'integer', 'exists:enterprises,id']
            : ['nullable'];

        return [
            'enterprise_id' => $enterpriseRules,
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $emailRule],
            'role' => ['required', Rule::in(User::INTERNAL_ROLES)],
            'is_active' => ['nullable', 'boolean'],
            'password' => [$target ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ];
    }

    private function resolveEnterpriseId(User $actor, ?int $enterpriseId): ?int
    {
        if ($actor->isAdmin()) {
            return $enterpriseId;
        }

        return $actor->enterprise_id;
    }

    private function guardAgainstSelfManagement(User $actor, User $target): void
    {
        if ((int) $actor->id === (int) $target->id) {
            throw ValidationException::withMessages([
                'access' => 'Use a área de perfil para alterar seu próprio acesso.',
            ]);
        }
    }

    private function ensureEnterpriseAdminStillExists(
        User $target,
        ?string $newRole,
        bool $newIsActive,
        ?int $newEnterpriseId,
        bool $deleting = false
    ): void {
        if (! $target->hasRole(User::ROLE_ENTERPRISE_ADMIN)) {
            return;
        }

        $willLeaveAdminRole = $deleting
            || $newRole !== User::ROLE_ENTERPRISE_ADMIN
            || ! $newIsActive
            || (int) $newEnterpriseId !== (int) $target->enterprise_id;

        if (! $willLeaveAdminRole || ! $target->enterprise_id) {
            return;
        }

        $otherAdmins = User::query()
            ->where('enterprise_id', $target->enterprise_id)
            ->where('role', User::ROLE_ENTERPRISE_ADMIN)
            ->where('is_active', true)
            ->whereKeyNot($target->id)
            ->count();

        if ($otherAdmins === 0) {
            throw ValidationException::withMessages([
                'access' => 'O escritório precisa manter ao menos um administrador ativo.',
            ]);
        }
    }
}
