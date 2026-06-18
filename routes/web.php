<?php

use App\Http\Controllers\Api\LocationReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PhoneController;
use Illuminate\Support\Facades\Route;

// ---------------------------------------------------------------------------
// Guest (authentication)
// ---------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

// ---------------------------------------------------------------------------
// Authenticated application
// ---------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/phones', [PhoneController::class, 'index'])->name('phones.index');
    Route::get('/phones/create', [PhoneController::class, 'create'])->name('phones.create');
    Route::post('/phones', [PhoneController::class, 'store'])->name('phones.store');
    Route::get('/phones/{phone}', [PhoneController::class, 'show'])->name('phones.show');
    Route::get('/phones/{phone}/edit', [PhoneController::class, 'edit'])->name('phones.edit');
    Route::put('/phones/{phone}', [PhoneController::class, 'update'])->name('phones.update');
    Route::delete('/phones/{phone}', [PhoneController::class, 'destroy'])->name('phones.destroy');
    Route::post('/phones/{phone}/regenerate-key', [PhoneController::class, 'regenerateKey'])
        ->name('phones.regenerate-key');

    Route::post('/phones/{phone}/locations', [LocationController::class, 'store'])
        ->name('locations.store');
    Route::delete('/phones/{phone}/locations/{location}', [LocationController::class, 'destroy'])
        ->name('locations.destroy');
});

// ---------------------------------------------------------------------------
// Device reporting API (authenticated by per-phone API key, no session)
// ---------------------------------------------------------------------------
Route::post('/api/report-location', [LocationReportController::class, 'report'])
    ->name('api.report-location');
