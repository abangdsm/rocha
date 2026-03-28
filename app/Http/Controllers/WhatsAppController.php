<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;  // ← Perbaiki ini
use Illuminate\Support\Facades\Http;
use Illuminate\Routing\Controller;

class WhatsAppController extends Controller
{
    protected $nodeUrl = 'http://localhost:3000';

    public function connect(Request $request)
    {
        $accountId = $request->input('accountId') ?? $request->input('account_id');

        $response = Http::post($this->nodeUrl . '/api/connect', [
            'accountId' => $accountId
        ]);

        return $response->json();
    }

    public function send(Request $request)
    {
        $accountId = $request->input('accountId') ?? $request->input('account_id');
        $to = $request->input('to');
        $message = $request->input('message');

        if (!$accountId || !$to || !$message) {
            return response()->json([
                'error' => 'accountId, to, and message required'
            ], 400);
        }

        $response = Http::post($this->nodeUrl . '/api/send', [
            'accountId' => $accountId,
            'to' => $to,
            'message' => $message
        ]);

        return $response->json();
    }

    public function status($accountId)
    {
        $response = Http::get($this->nodeUrl . '/api/status/' . $accountId);
        return $response->json();
    }

    public function handleQR(Request $request)
    {
        $accountId = $request->accountId;
        $qr = $request->qr;

        // Simpan QR ke cache
        Cache::put('qr_' . $accountId, $qr, 300); // expire 5 menit
        
        \Log::info('QR received for ' . $accountId);

        return response()->json(['success' => true]);
    }

    public function handleStatusUpdate(Request $request)
    {
        $accountId = $request->accountId;
        $status = $request->status;
        $phoneNumber = $request->phoneNumber;

        // Update device status di database
        $device = Device::where('device_id', $accountId)->first();
        if ($device) {
            $device->update([
                'status' => $status,
                'phone_number' => $phoneNumber ?? $device->phone_number,
                'last_connected_at' => $status == 'connected' ? now() : null,
            ]);
        }

        // Hapus QR dari cache jika sudah connect
        if ($status == 'connected') {
            Cache::forget('qr_' . $accountId);
        }

        return response()->json(['success' => true]);
    }
}