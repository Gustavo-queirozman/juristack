<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\Enterprise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_client_registration_screen_can_be_rendered_from_enterprise_slug_link(): void
    {
        $enterprise = Enterprise::create([
            'name' => 'Escritorio Central',
        ]);

        $response = $this->get(route('register.client', $enterprise->slug));

        $response->assertOk()
            ->assertSee('Portal do cliente')
            ->assertSee($enterprise->name)
            ->assertDontSee('Escritorio responsavel</label>', false);
    }

    public function test_new_office_users_can_register(): void
    {
        $response = $this->post('/register', [
            'enterprise_name' => 'Silva & Rocha',
            'enterprise_cnp' => '12.345.678/0001-99',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('enterprises', [
            'name' => 'Silva & Rocha',
            'cnp' => '12345678000199',
        ]);

        $enterprise = Enterprise::where('name', 'Silva & Rocha')->firstOrFail();
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertSame($enterprise->id, $user->enterprise_id);
        $this->assertSame(User::ROLE_ENTERPRISE_ADMIN, $user->role);
    }

    public function test_new_client_users_can_register_with_initial_documents(): void
    {
        Storage::fake('public');

        $enterprise = Enterprise::create(['name' => 'Escritorio Central']);

        $response = $this->post('/register', [
            'registration_type' => 'client',
            'enterprise_slug' => $enterprise->slug,
            'name' => 'Maria da Silva',
            'cnp' => '123.456.789-09',
            'email' => 'maria@example.com',
            'mobile_phone' => '(31) 99999-0000',
            'birth_date' => '1990-05-10',
            'city' => 'Belo Horizonte',
            'state' => 'MG',
            'profession' => 'Professora',
            'password' => 'password',
            'password_confirmation' => 'password',
            'identification_file' => UploadedFile::fake()->create('rg.pdf', 100, 'application/pdf'),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'maria@example.com')->firstOrFail();
        $customer = Customer::where('email', 'maria@example.com')->firstOrFail();

        $this->assertSame(User::ROLE_CLIENT, $user->role);
        $this->assertSame($enterprise->id, $user->enterprise_id);
        $this->assertSame($user->id, $customer->user_id);

        $this->assertDatabaseHas('customer_files', [
            'customer_id' => $customer->id,
            'document_type' => 'identification',
            'original_name' => 'rg.pdf',
        ]);
    }
}
