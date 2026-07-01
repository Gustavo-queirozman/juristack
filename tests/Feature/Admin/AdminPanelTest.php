<?php

namespace Tests\Feature\Admin;

use App\Models\BillingSetting;
use App\Models\Enterprise;
use App\Models\SaasPlan;
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

    public function test_global_admin_can_update_stripe_credentials(): void
    {
        $admin = User::query()->where('email', 'admin@juristack.com.br')->firstOrFail();

        $response = $this
            ->actingAs($admin)
            ->put(route('admin.billing.settings.update'), [
                'stripe_publishable_key' => 'pk_test_123',
                'stripe_secret_key' => 'sk_test_123',
                'stripe_webhook_secret' => 'whsec_123',
                'default_currency' => 'brl',
                'is_stripe_enabled' => '1',
            ]);

        $response->assertRedirect();

        $settings = BillingSetting::query()->firstOrFail();

        $this->assertSame('pk_test_123', $settings->stripe_publishable_key);
        $this->assertSame('sk_test_123', $settings->stripe_secret_key);
        $this->assertSame('whsec_123', $settings->stripe_webhook_secret);
        $this->assertTrue($settings->is_stripe_enabled);
    }

    public function test_global_admin_can_create_saas_plan(): void
    {
        $admin = User::query()->where('email', 'admin@juristack.com.br')->firstOrFail();

        $response = $this
            ->actingAs($admin)
            ->post(route('admin.billing.plans.store'), [
                'name' => 'Plano Starter',
                'slug' => 'starter',
                'description' => 'Plano inicial',
                'price' => '297,00',
                'currency' => 'brl',
                'billing_interval' => 'month',
                'interval_count' => 1,
                'trial_days' => 14,
                'button_label' => 'Assinar',
                'features_text' => "Acesso ao painel\nSuporte por email",
                'sort_order' => 1,
                'is_active' => '1',
                'is_public' => '1',
                'is_featured' => '1',
            ]);

        $response->assertRedirect(route('admin.billing.plans.index'));

        $plan = SaasPlan::query()->where('slug', 'starter')->firstOrFail();

        $this->assertSame(29700, $plan->price_cents);
        $this->assertSame(['Acesso ao painel', 'Suporte por email'], $plan->features);
        $this->assertTrue($plan->is_public);
        $this->assertTrue($plan->is_featured);
    }
}
