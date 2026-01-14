<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataJudController;

Route::middleware('api')->group(function () {
    // API route for DataJud without web CSRF (uses API token auth if needed)
    Route::post('/datajud/search', [DataJudController::class, 'apiSearch'])->name('api.datajud.search');
    // Debug route to run quick searches
    Route::get('/datajud/debug', [DataJudController::class, 'debug'])->name('api.datajud.debug');
});
