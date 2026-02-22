<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataJudController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\DocumentController;

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
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/user', [UserController::class, 'store'])->name('users.create');

    // Customers CRUD (cadastro completo + arquivos)
    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/files', [CustomerController::class, 'uploadForCustomer'])->name('customers.files.store');
    Route::get('customers/{customer}/files/{file}/download', [CustomerController::class, 'downloadFile'])->name('customers.files.download');
    Route::delete('customers/{customer}/files/{file}', [CustomerController::class, 'destroyFile'])->name('customers.files.destroy');

    // Documentos e modelos (requer auth)
    Route::get('/documents', [DocumentController::class, 'listDocuments'])->name('documents.index');
    Route::get('/documents/create', [DocumentController::class, 'createFromTemplate'])->name('documents.create');
    Route::post('/documents', [DocumentController::class, 'createDocument'])->name('documents.store');
    Route::get('/documents/{id}', [DocumentController::class, 'showDocument'])->name('documents.show');
    Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::put('/documents/{id}', [DocumentController::class, 'updateDocument'])->name('documents.update');
    Route::patch('/documents/{id}', [DocumentController::class, 'updateDocument']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroyDocument'])->name('documents.destroy');
    Route::post('/documents/{id}/generate', [DocumentController::class, 'generateDocument'])->name('documents.generate');
    Route::post('/documents/{id}/form', [DocumentController::class, 'createForm']);
    Route::get('/documents/{id}/form', [DocumentController::class, 'showForm']);
    Route::put('/documents/{id}/form', [DocumentController::class, 'updateForm']);
    Route::patch('/documents/{id}/form', [DocumentController::class, 'updateForm']);

    Route::get('/document-templates', [DocumentTemplateController::class, 'index'])->name('document-templates.index');
    Route::get('/document-templates/create', [DocumentTemplateController::class, 'create'])->name('document-templates.create');
    Route::post('/document-templates', [DocumentTemplateController::class, 'store'])->name('document-templates.store');
    Route::get('/document-templates/{id}', [DocumentTemplateController::class, 'show'])->name('document-templates.show');
    Route::get('/document-templates/{id}/preencher', [DocumentTemplateController::class, 'showFillForm'])->name('document-templates.fill');
    Route::post('/document-templates/{id}/gerar', [DocumentTemplateController::class, 'generateDocument'])->name('document-templates.generate');
    Route::get('/document-templates/{id}/edit', [DocumentTemplateController::class, 'edit'])->name('document-templates.edit');
    Route::put('/document-templates/{id}', [DocumentTemplateController::class, 'update'])->name('document-templates.update');
    Route::patch('/document-templates/{id}', [DocumentTemplateController::class, 'update']);
    Route::delete('/document-templates/{id}', [DocumentTemplateController::class, 'destroy'])->name('document-templates.destroy');
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
