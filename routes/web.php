<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Device routes
    Route::resource('devices', DeviceController::class);
    Route::get('/devices/{device}/refresh-status', [DeviceController::class, 'refreshStatus'])->name('devices.refresh-status');
});

Route::get('/send-message', function () {
    return view('send-message');
})->middleware(['auth'])->name('send-message');

require __DIR__ . '/auth.php';
