<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataJudController;
use App\Http\Controllers\CustomerController;

// DataJud routes - require authentication
Route::middleware('auth')->group(function () {
    Route::get('/datajud/pesquisa', fn () => view('datajud.pesquisa'))->name('datajud.index');
    Route::get('/datajud/salvos', [DataJudController::class, 'salvos'])->name('datajud.salvos');
    Route::post('/datajud/salvar', [DataJudController::class, 'salvarProcesso'])->name('datajud.salvar');
    Route::get('/datajud/salvo/{id}', [DataJudController::class, 'showSaved'])->name('datajud.salvo.show');
    Route::post('/datajud/salvo/{id}/atualizar', [DataJudController::class, 'atualizarProcesso'])->name('datajud.salvo.atualizar');
    Route::delete('/datajud/salvo/{id}', [DataJudController::class, 'deleteSaved'])->name('datajud.salvo.delete');
    Route::post('/datajud/search', [DataJudController::class, 'apiSearch'])->name('datajud.api.search');

    // Usuários (CRUD) – rota /users
   // Route::resource('users', ClienteController::class)->parameters(['user' => 'cliente']);

    // Customers CRUD (cadastro completo + arquivos)
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/files', [CustomerController::class, 'uploadForCustomer'])->name('customers.files.store');
    Route::get('customers/{customer}/files/{file}/download', [CustomerController::class, 'downloadFile'])->name('customers.files.download');
    Route::delete('customers/{customer}/files/{file}', [CustomerController::class, 'destroyFile'])->name('customers.files.destroy');
});

Route::middleware('auth:customer')->group(function () {
    Route::post('/customers/upload', [CustomerController::class, 'uploadFiles'])
        ->name('customers.upload');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
