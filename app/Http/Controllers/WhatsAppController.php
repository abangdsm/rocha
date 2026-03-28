<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    protected $nodeUrl = 'http://localhost:3000';
    
    public function connect(Request $request)
    {
        $response = Http::post($this->nodeUrl . '/api/connect', [
            'accountId' => $request->account_id
        ]);
        
        return $response->json();
    }
    
    public function send(Request $request)
    {
        $response = Http::post($this->nodeUrl . '/api/send', [
            'accountId' => $request->account_id,
            'to' => $request->to,
            'message' => $request->message
        ]);
        
        return $response->json();
    }
    
    public function status($accountId)
    {
        $response = Http::get($this->nodeUrl . '/api/status/' . $accountId);
        return $response->json();
    }
}