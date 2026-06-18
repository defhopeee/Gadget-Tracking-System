<?php

use App\Http\Controllers\Api\LocationReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PhoneController;
use Illuminate\Support\Facades\Route;

// Dashboard is the landing page (no authentication for now).
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/gadgets', [PhoneController::class, 'index'])->name('phones.index');
Route::get('/gadgets/create', [PhoneController::class, 'create'])->name('phones.create');
Route::post('/gadgets', [PhoneController::class, 'store'])->name('phones.store');
Route::get('/gadgets/{phone}', [PhoneController::class, 'show'])->name('phones.show');
Route::get('/gadgets/{phone}/edit', [PhoneController::class, 'edit'])->name('phones.edit');
Route::put('/gadgets/{phone}', [PhoneController::class, 'update'])->name('phones.update');
Route::delete('/gadgets/{phone}', [PhoneController::class, 'destroy'])->name('phones.destroy');
Route::post('/gadgets/{phone}/regeneratekey', [PhoneController::class, 'regenerateKey'])->name('phones.regeneratekey');

Route::post('/gadgets/{phone}/locations', [LocationController::class, 'store'])->name('locations.store');
Route::delete('/gadgets/{phone}/locations/{location}', [LocationController::class, 'destroy'])->name('locations.destroy');

// Device reporting API (authenticated by the per-gadget API key, no session).
Route::post('/api/reportlocation', [LocationReportController::class, 'report'])->name('api.reportlocation');
