<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetAllUserPasswordsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_resets_passwords_in_users_and_customer_legacy_table(): void
    {
        $user = User::factory()->create([
            'password' => 'senha-antiga',
        ]);

        DB::table('customers')->insert([
            'user_id' => $user->id,
            'enterprise_id' => null,
            'name' => 'Cliente Vinculado',
            'email' => 'cliente@example.com',
            'password' => Hash::make('senha-antiga-customer'),
            'remember_token' => 'legacy-token',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('customers')->insert([
            'user_id' => null,
            'enterprise_id' => null,
            'name' => 'Cliente Legado',
            'email' => 'legado@example.com',
            'password' => Hash::make('senha-legada'),
            'remember_token' => 'legacy-only-token',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => 'token-antigo',
            'created_at' => now(),
        ]);

        $this->artisan('users:reset-passwords', ['password' => 'nova-senha-123'])
            ->expectsOutput('Users atualizados: 1')
            ->expectsOutput('Customers atualizados: 2')
            ->expectsOutput('Tokens removidos: 1')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('nova-senha-123', $user->fresh()->password));

        $customerPasswords = DB::table('customers')
            ->orderBy('id')
            ->pluck('password');

        foreach ($customerPasswords as $customerPassword) {
            $this->assertTrue(Hash::check('nova-senha-123', $customerPassword));
        }

        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseHas('customers', [
            'email' => 'cliente@example.com',
            'remember_token' => null,
        ]);
        $this->assertDatabaseHas('customers', [
            'email' => 'legado@example.com',
            'remember_token' => null,
        ]);
    }
}
