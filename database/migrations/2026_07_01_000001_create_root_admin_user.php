<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $existingUser = DB::table('users')
            ->where('email', 'admin@juristack.com.br')
            ->first();

        $payload = [
            'name' => 'Administrador JuriStack',
            'enterprise_id' => null,
            'email_verified_at' => now(),
            'password' => Hash::make('12345678'),
            'remember_token' => $existingUser?->remember_token ?: Str::random(10),
            'role' => User::ROLE_ADMIN,
            'is_active' => true,
            'updated_at' => now(),
        ];

        if ($existingUser) {
            DB::table('users')
                ->where('id', $existingUser->id)
                ->update($payload);

            return;
        }

        DB::table('users')->insert($payload + [
            'email' => 'admin@juristack.com.br',
            'created_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('email', 'admin@juristack.com.br')
            ->delete();
    }
};
