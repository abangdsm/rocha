<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class DeviceController extends Controller
{
    protected $nodeUrl = 'http://localhost:3000';

    public function index()
    {
        $devices = Auth::user()->devices()->latest()->get();
        return view('devices.index', compact('devices'));
    }

    public function create()
    {
        return view('devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_id' => 'required|unique:devices,device_id',
            'phone_number' => 'nullable',
        ]);

        $device = Device::create([
            'user_id' => Auth::id(),
            'store_id' => 1, // Sementara, nanti kita buat store
            'device_id' => $request->device_id,
            'phone_number' => $request->phone_number,
            'status' => 'disconnected',
        ]);

        // Trigger connect ke Node.js
        try {
            Http::post($this->nodeUrl . '/api/connect', [
                'accountId' => $device->device_id
            ]);
        } catch (\Exception $e) {
            // Node.js mungkin belum jalan
        }

        return redirect()->route('devices.index')->with('success', 'Device created. Please check terminal for QR code.');
    }

    public function show(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }
        return view('devices.show', compact('device'));
    }

    public function refreshStatus(Device $device)
    {
        try {
            $response = Http::get($this->nodeUrl . '/api/status/' . $device->device_id);
            $status = $response->json();

            if ($status['connected']) {
                $device->update([
                    'status' => 'connected',
                    'last_connected_at' => now(),
                ]);
            } else {
                $device->update(['status' => 'disconnected']);
            }

            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['connected' => false, 'error' => $e->getMessage()]);
        }
    }

    public function destroy(Device $device)
    {
        if ($device->user_id !== Auth::id()) {
            abort(403);
        }

        // Hapus session folder nanti
        $device->delete();

        return redirect()->route('devices.index')->with('success', 'Device deleted.');
    }
}