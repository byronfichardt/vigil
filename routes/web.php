<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureSetupComplete;
use App\Http\Middleware\EnsureSetupNotComplete;
use App\Livewire\Dashboard;
use App\Livewire\ExceptionDetail;
use App\Livewire\ProjectExceptions;
use App\Livewire\ProjectIndex;
use App\Livewire\ProjectCreate;
use App\Livewire\ProjectSettings;
use App\Livewire\Setup;
use Illuminate\Support\Facades\Route;

Route::get('/setup', Setup::class)
    ->middleware(EnsureSetupNotComplete::class)
    ->name('setup');

Route::middleware(EnsureSetupComplete::class)->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/projects', ProjectIndex::class)->name('projects.index');
        Route::get('/projects/create', ProjectCreate::class)->name('projects.create');
        Route::get('/projects/{project}', ProjectExceptions::class)->name('projects.show');
        Route::get('/projects/{project}/settings', ProjectSettings::class)->name('projects.settings');
        Route::get('/exceptions/{exceptionGroup}', ExceptionDetail::class)->name('exceptions.show');
    });
});
