<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataJudController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTemplateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinancialEntryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\EnterpriseController as AdminEnterpriseController;
use App\Http\Controllers\OfficeAccessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\WhatsAppConnectionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin,enterprise_admin,lawyer'])->group(function () {
    Route::get('/agenda', fn () => view('events.agenda'))->name('agenda.index');
    Route::resource('events', EventController::class)->only([
        'index', 'store', 'show', 'update', 'destroy',
    ]);

    Route::get('/datajud/pesquisa', [DataJudController::class, 'index'])->name('datajud.index');
    Route::get('/datajud/salvos', [DataJudController::class, 'salvos'])->name('datajud.salvos');
    Route::post('/datajud/salvar', [DataJudController::class, 'salvarProcesso'])->name('datajud.salvar');
    Route::get('/datajud/salvo/{id}', [DataJudController::class, 'showSaved'])->name('datajud.salvo.show');
    Route::post('/datajud/salvo/{id}/atualizar', [DataJudController::class, 'atualizarProcesso'])->name('datajud.salvo.atualizar');
    Route::delete('/datajud/salvo/{id}', [DataJudController::class, 'deleteSaved'])->name('datajud.salvo.delete');
    Route::post('/datajud/search', [DataJudController::class, 'apiSearch'])->name('datajud.api.search');

    Route::resource('customers', CustomerController::class);
    Route::post('customers/{customer}/files', [CustomerController::class, 'uploadForCustomer'])->name('customers.files.store');
    Route::post('customers/{customer}/document-requests', [CustomerController::class, 'storeDocumentRequest'])->name('customers.document-requests.store');
    Route::post('customers/{customer}/service-contract', [CustomerController::class, 'sendServiceContract'])->name('customers.service-contract.send');
    Route::get('customers/{customer}/files/{file}/download', [CustomerController::class, 'downloadFile'])->name('customers.files.download');
    Route::delete('customers/{customer}/files/{file}', [CustomerController::class, 'destroyFile'])->name('customers.files.destroy');

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

    Route::middleware('auth')->prefix('tarefas')->name('tasks.')->group(function () {
        Route::get('/kanban', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::patch('/{task}', [TaskController::class, 'update'])->name('update');
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{task}/assignee', [TaskController::class, 'updateAssignee'])->name('update-assignee');
        Route::get('/{task}/users', [TaskController::class, 'users'])->name('users');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('financeiro')->name('financial-entries.')->group(function () {
        Route::get('/', [FinancialEntryController::class, 'index'])->name('index');
        Route::get('/create', [FinancialEntryController::class, 'create'])->name('create');
        Route::post('/', [FinancialEntryController::class, 'store'])->name('store');
        Route::post('/import-bank-file', [FinancialEntryController::class, 'importBankFile'])->name('import-bank-file');
        Route::get('/{financialEntry}/edit', [FinancialEntryController::class, 'edit'])->name('edit');
        Route::put('/{financialEntry}', [FinancialEntryController::class, 'update'])->name('update');
        Route::post('/{financialEntry}/payments', [FinancialEntryController::class, 'storePayment'])->name('payments.store');
        Route::post('/{financialEntry}/whatsapp-reminder', [FinancialEntryController::class, 'sendWhatsAppReminder'])->name('whatsapp-reminder');
        Route::delete('/{financialEntry}', [FinancialEntryController::class, 'destroy'])->name('destroy');
    });
});

Route::middleware(['auth', 'role:admin,enterprise_admin'])->prefix('acessos-escritorio')->name('office-access.')->group(function () {
    Route::get('/', [OfficeAccessController::class, 'index'])->name('index');
    Route::get('/create', [OfficeAccessController::class, 'create'])->name('create');
    Route::post('/', [OfficeAccessController::class, 'store'])->name('store');
    Route::get('/{user}/edit', [OfficeAccessController::class, 'edit'])->name('edit');
    Route::put('/{user}', [OfficeAccessController::class, 'update'])->name('update');
    Route::delete('/{user}', [OfficeAccessController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'role:admin,enterprise_admin'])->prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('/conexao', [WhatsAppConnectionController::class, 'show'])->name('connection.show');
    Route::post('/conexao/conectar', [WhatsAppConnectionController::class, 'connect'])->name('connection.connect');
    Route::post('/conexao/atualizar', [WhatsAppConnectionController::class, 'refresh'])->name('connection.refresh');
    Route::delete('/conexao/desconectar', [WhatsAppConnectionController::class, 'disconnect'])->name('connection.disconnect');
});

Route::middleware(['auth', 'role:admin'])->prefix('painel-administrativo')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('escritorios')->name('enterprises.')->group(function () {
        Route::get('/', [AdminEnterpriseController::class, 'index'])->name('index');
        Route::get('/create', [AdminEnterpriseController::class, 'create'])->name('create');
        Route::post('/', [AdminEnterpriseController::class, 'store'])->name('store');
        Route::get('/{enterprise}/edit', [AdminEnterpriseController::class, 'edit'])->name('edit');
        Route::put('/{enterprise}', [AdminEnterpriseController::class, 'update'])->name('update');
    });
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::post('/customers/upload', [CustomerController::class, 'uploadFiles'])
        ->name('customers.upload');
    Route::get('/portal/arquivos/{file}/download', [CustomerController::class, 'downloadOwnFile'])
        ->name('client.files.download');
    Route::get('/portal/documentos/{id}/download', [DocumentController::class, 'downloadOwn'])
        ->name('client.documents.download');
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

require __DIR__ . '/auth.php';
