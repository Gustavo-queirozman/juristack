<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

//use App\Http\Controllers\DataJudController;


use App\Http\Controllers\DataJudController;

Route::get('/datajud/pesquisa', fn () => view('datajud.pesquisa'));

// API route for AJAX/internal requests
Route::post('/datajud/search', [DataJudController::class, 'apiSearch'])
    ->name('datajud.api.search');


Route::get('/', function () {
    return redirect('/login');
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
