<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ResetAllUserPasswords extends Command
{
    protected $signature = 'users:reset-passwords
        {password : Nova senha aplicada a todos os acessos}
        {--only-active : Atualiza apenas usuarios ativos em users}';

    protected $description = 'Reseta a senha em users e espelha o hash em tabelas legadas de usuario.';

    public function handle(): int
    {
        if (! Schema::hasTable('users')) {
            $this->error('Tabela users nao encontrada.');

            return self::FAILURE;
        }

        $passwordHash = Hash::make((string) $this->argument('password'));
        $timestamp = now();

        $usersQuery = $this->usersQuery((bool) $this->option('only-active'));
        $updatedUsers = (clone $usersQuery)->count();

        (clone $usersQuery)->update([
            'password' => $passwordHash,
            'remember_token' => null,
            'updated_at' => $timestamp,
        ]);

        $updatedCustomers = 0;
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'password')) {
            $customersQuery = $this->customersQuery((bool) $this->option('only-active'));
            $updatedCustomers = (clone $customersQuery)->count();

            (clone $customersQuery)->update([
                'password' => $passwordHash,
                'remember_token' => null,
                'updated_at' => $timestamp,
            ]);
        }

        $clearedTokens = 0;
        if (Schema::hasTable('password_reset_tokens')) {
            $clearedTokens = DB::table('password_reset_tokens')->count();
            DB::table('password_reset_tokens')->delete();
        }

        $this->info("Users atualizados: {$updatedUsers}");
        $this->info("Customers atualizados: {$updatedCustomers}");
        $this->info("Tokens removidos: {$clearedTokens}");

        return self::SUCCESS;
    }

    private function usersQuery(bool $onlyActive): Builder
    {
        $query = DB::table('users');

        if ($onlyActive && Schema::hasColumn('users', 'is_active')) {
            $query->where('is_active', true);
        }

        return $query;
    }

    private function customersQuery(bool $onlyActive): Builder
    {
        $query = DB::table('customers');

        if (! Schema::hasColumn('customers', 'user_id')) {
            return $query->where(function (Builder $builder): void {
                $builder->whereNotNull('password')
                    ->orWhereNotNull('email');
            });
        }

        if (! $onlyActive || ! Schema::hasColumn('users', 'is_active')) {
            return $query->where(function (Builder $builder): void {
                $builder->whereNotNull('user_id')
                    ->orWhereNotNull('password');
            });
        }

        return $query->where(function (Builder $builder): void {
            $builder->whereIn('user_id', function ($subquery): void {
                $subquery->select('id')
                    ->from('users')
                    ->where('is_active', true);
            })->orWhere(function (Builder $legacyBuilder): void {
                $legacyBuilder->whereNull('user_id')
                    ->whereNotNull('password');
            });
        });
    }
}
