<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard;
use App\Livewire\ExceptionDetail;
use App\Livewire\ProjectExceptions;
use App\Livewire\ProjectIndex;
use App\Livewire\ProjectCreate;
use App\Livewire\ProjectSettings;
use Illuminate\Support\Facades\Route;

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
