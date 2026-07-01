<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Usuario administrador raiz.
     * Email: admin@juristack.com.br
     * Senha: 12345678
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@juristack.com.br'],
            [
                'name' => 'Administrador JuriStack',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );
    }
}
