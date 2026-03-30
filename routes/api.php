<?php

use App\Http\Controllers\Api\ExceptionIngestController;
use App\Http\Controllers\Api\LogIngestController;
use App\Http\Middleware\ValidateProjectApiKey;
use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:api-ingest', ValidateProjectApiKey::class])->group(function () {
    Route::post('/exceptions', [ExceptionIngestController::class, 'store']);
    Route::post('/logs', [LogIngestController::class, 'store']);
});
