<?php

use App\Http\Controllers\Api\ExceptionIngestController;
use App\Http\Middleware\ValidateProjectApiKey;
use Illuminate\Support\Facades\Route;

Route::post('/exceptions', [ExceptionIngestController::class, 'store'])
    ->middleware(['throttle:api-ingest', ValidateProjectApiKey::class]);
