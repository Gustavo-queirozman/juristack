<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataJudController;


// DataJud routes - require authentication
Route::middleware('auth')->group(function () {
    Route::get('/datajud/pesquisa', fn () => view('datajud.pesquisa'))->name('datajud.index');
    Route::get('/datajud/salvos', [DataJudController::class, 'salvos'])->name('datajud.salvos');
    Route::post('/datajud/salvar', [DataJudController::class, 'salvarProcesso'])->name('datajud.salvar');
    Route::get('/datajud/salvo/{id}', [DataJudController::class, 'showSaved'])->name('datajud.salvo.show');
    Route::post('/datajud/salvo/{id}/atualizar', [DataJudController::class, 'atualizarProcesso'])->name('datajud.salvo.atualizar');
    Route::delete('/datajud/salvo/{id}', [DataJudController::class, 'deleteSaved'])->name('datajud.salvo.delete');
    Route::post('/datajud/search', [DataJudController::class, 'apiSearch'])->name('datajud.api.search');

    // Clientes CRUD
    Route::resource('clientes', ClienteController::class);
});




Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
