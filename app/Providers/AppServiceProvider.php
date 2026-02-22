<?php

namespace App\Providers;

use App\Models\Cliente;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rota /users usa o recurso "users" mas o model é Cliente (usuários vinculados à conta).
        // O parâmetro {user} na URL deve resolver para Cliente, não para o model User.
        Route::bind('user', function (string $value) {
            return Cliente::findOrFail($value);
        });
    }
}
