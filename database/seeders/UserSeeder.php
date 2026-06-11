<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Usuário para login em desenvolvimento.
     * Email: admin@juristack.local
     * Senha: password
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@juristack.local'],
            [
                'name' => 'Admin JuriStack',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );
    }
}
