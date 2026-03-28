<?php

use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TagController;
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

    // Contact routes
    Route::resource('contacts', ContactController::class);
    Route::post('contacts/import', [ContactController::class, 'import'])->name('contacts.import');
    Route::get('contacts/export', [ContactController::class, 'export'])->name('contacts.export');

    // Group routes
    Route::resource('groups', GroupController::class);
    
    // Tag routes
    Route::resource('tags', TagController::class);

    // Broadcast routes
    Route::resource('broadcasts', BroadcastController::class);
    Route::post('/broadcasts/{broadcast}/cancel', [BroadcastController::class, 'cancel'])->name('broadcasts.cancel');
    Route::post('/broadcasts/{broadcast}/retry', [BroadcastController::class, 'retry'])->name('broadcasts.retry');
});

Route::get('/send-message', function () {
    return view('send-message');
})->middleware(['auth'])->name('send-message');

Route::post('/devices/{device}/disconnect', [DeviceController::class, 'disconnect'])->name('devices.disconnect');

require __DIR__ . '/auth.php';
