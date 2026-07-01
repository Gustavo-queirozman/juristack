<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Billing\StripeWebhookController;
use App\Http\Controllers\DataJudController;
use App\Http\Controllers\WhatsAppWebhookController;

Route::middleware('api')->group(function () {
    // API route for DataJud without web CSRF (uses API token auth if needed)
    Route::post('/datajud/search', [DataJudController::class, 'apiSearch'])->name('api.datajud.search');
    Route::post('/whatsapp/webhook', WhatsAppWebhookController::class)->name('api.whatsapp.webhook');
    Route::post('/stripe/webhook', StripeWebhookController::class)->name('api.stripe.webhook');
    // Debug route to run quick searches
    //Route::get('/datajud/debug', [DataJudController::class, 'debug'])->name('api.datajud.debug');
});
