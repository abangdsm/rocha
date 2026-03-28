<?php
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::post('/whatsapp/connect', [WhatsAppController::class, 'connect']);
Route::post('/whatsapp/send', [WhatsAppController::class, 'send']);
Route::get('/whatsapp/status/{accountId}', [WhatsAppController::class, 'status']);
Route::post('/whatsapp/qr', [WhatsAppController::class, 'handleQR']);
Route::post('/whatsapp/status-update', [WhatsAppController::class, 'handleStatusUpdate']);
Route::get('/whatsapp/get-qr/{deviceId}', function($deviceId) {
    $qr = Cache::get('qr_' . $deviceId);
    return response()->json(['qr' => $qr]);
});