<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
}