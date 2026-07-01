<?php

namespace Tests\Feature\Admin;

use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_admin_user_is_created_by_migration(): void
    {
        $user = User::query()->where('email', 'admin@juristack.com.br')->first();

        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_ADMIN, $user->role);
        $this->assertTrue((bool) $user->is_active);
        $this->assertTrue(Hash::check('12345678', $user->password));
    }

    public function test_global_admin_can_access_admin_panel(): void
    {
        $admin = User::query()->where('email', 'admin@juristack.com.br')->firstOrFail();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Painel administrativo');
    }

    public function test_enterprise_admin_cannot_access_admin_panel(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_global_admin_can_create_enterprise_with_initial_admin(): void
    {
        $admin = User::query()->where('email', 'admin@juristack.com.br')->firstOrFail();

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.enterprises.store'), [
                'name' => 'Escritorio Atlas',
                'cnp' => '12.345.678/0001-90',
                'email' => 'contato@atlas.com',
                'phone' => '(31) 3333-4444',
                'address' => 'Rua Central, 100',
                'admin_name' => 'Paula Souza',
                'admin_email' => 'paula@atlas.com',
                'admin_password' => '12345678',
                'admin_password_confirmation' => '12345678',
            ]);

        $response->assertRedirect(route('admin.enterprises.index'));

        $enterprise = Enterprise::query()->where('name', 'Escritorio Atlas')->firstOrFail();

        $this->assertSame('12345678000190', $enterprise->cnp);
        $this->assertDatabaseHas('users', [
            'enterprise_id' => $enterprise->id,
            'email' => 'paula@atlas.com',
            'role' => User::ROLE_ENTERPRISE_ADMIN,
            'is_active' => true,
        ]);
    }
}
