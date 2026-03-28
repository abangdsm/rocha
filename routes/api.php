<?php

use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/connect', [WhatsAppController::class, 'connect']);
Route::post('/whatsapp/send', [WhatsAppController::class, 'send']);
Route::get('/whatsapp/status/{accountId}', [WhatsAppController::class, 'status']);