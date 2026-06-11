<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_client_users_cannot_access_internal_customer_listing(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $client = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);

        $response = $this->actingAs($client)->get('/customers');

        $response->assertForbidden();
    }

    public function test_client_users_can_upload_files_through_unified_authentication(): void
    {
        Storage::fake('public');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $client = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);
        $customer = Customer::create([
            'user_id' => $client->id,
            'enterprise_id' => $enterprise->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
        ]);

        $response = $this
            ->actingAs($client)
            ->from('/dashboard')
            ->post('/customers/upload', [
                'file' => UploadedFile::fake()->create('arquivo.pdf', 100, 'application/pdf'),
            ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('customer_files', [
            'customer_id' => $customer->id,
            'document_type' => 'other',
            'original_name' => 'arquivo.pdf',
        ]);
    }

    public function test_client_users_can_download_their_own_uploaded_files(): void
    {
        Storage::fake('public');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $client = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);
        $customer = Customer::create([
            'user_id' => $client->id,
            'enterprise_id' => $enterprise->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
        ]);

        Storage::disk('public')->put('customers/' . $customer->id . '/arquivo.pdf', 'conteudo');

        $fileId = \App\Models\CustomerFile::create([
            'customer_id' => $customer->id,
            'document_type' => 'other',
            'path' => 'customers/' . $customer->id . '/arquivo.pdf',
            'original_name' => 'arquivo.pdf',
            'mime' => 'application/pdf',
            'size' => 8,
        ])->id;

        $response = $this
            ->actingAs($client)
            ->get(route('client.files.download', $fileId));

        $response->assertOk();
    }

    public function test_client_users_cannot_download_files_from_other_customers_in_the_same_enterprise(): void
    {
        Storage::fake('public');

        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $client = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_CLIENT,
        ]);
        $ownCustomer = Customer::create([
            'user_id' => $client->id,
            'enterprise_id' => $enterprise->id,
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
        ]);
        $otherCustomer = Customer::create([
            'enterprise_id' => $enterprise->id,
            'name' => 'Outro Cliente',
            'email' => 'outro@example.com',
        ]);

        Storage::disk('public')->put('customers/' . $otherCustomer->id . '/arquivo.pdf', 'conteudo');

        $fileId = \App\Models\CustomerFile::create([
            'customer_id' => $otherCustomer->id,
            'document_type' => 'other',
            'path' => 'customers/' . $otherCustomer->id . '/arquivo.pdf',
            'original_name' => 'arquivo.pdf',
            'mime' => 'application/pdf',
            'size' => 8,
        ])->id;

        $response = $this
            ->actingAs($client)
            ->get(route('client.files.download', $fileId));

        $response->assertForbidden();
        $this->assertSame($ownCustomer->id, $client->customerProfile->id);
    }

    public function test_enterprise_admin_can_access_office_access_screen(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this->actingAs($admin)->get('/acessos-escritorio');

        $response->assertOk();
    }

    public function test_lawyer_cannot_access_office_access_screen(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $lawyer = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_LAWYER,
        ]);

        $response = $this->actingAs($lawyer)->get('/acessos-escritorio');

        $response->assertForbidden();
    }

    public function test_enterprise_admin_can_create_internal_office_access(): void
    {
        $enterprise = Enterprise::create(['name' => 'Empresa Teste']);
        $admin = User::factory()->create([
            'enterprise_id' => $enterprise->id,
            'role' => User::ROLE_ENTERPRISE_ADMIN,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post('/acessos-escritorio', [
                'name' => 'Advogado Novo',
                'email' => 'advogado@teste.com',
                'role' => User::ROLE_LAWYER,
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'is_active' => '1',
                'enterprise_id' => 999999,
            ]);

        $response->assertRedirect('/acessos-escritorio');

        $this->assertDatabaseHas('users', [
            'name' => 'Advogado Novo',
            'email' => 'advogado@teste.com',
            'role' => User::ROLE_LAWYER,
            'enterprise_id' => $enterprise->id,
            'is_active' => true,
        ]);
    }
}
