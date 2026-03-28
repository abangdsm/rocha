<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Models\BroadcastContact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendBroadcastJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $broadcast;
    protected $contact;

    public function __construct(Broadcast $broadcast, BroadcastContact $contact)
    {
        $this->broadcast = $broadcast;
        $this->contact = $contact;
    }

    public function handle()
    {
        try {
            // Kirim pesan ke Node.js
            $response = Http::post('http://localhost:3000/api/send', [
                'accountId' => $this->broadcast->device->device_id,
                'to' => $this->contact->contact->phone,
                'message' => $this->broadcast->message
            ]);

            $result = $response->json();

            if ($result['success']) {
                $this->contact->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                // Update broadcast stats
                $this->broadcast->increment('sent_count');
            } else {
                $this->contact->update([
                    'status' => 'failed',
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
                $this->broadcast->increment('failed_count');
            }

        } catch (\Exception $e) {
            $this->contact->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
            $this->broadcast->increment('failed_count');
        }

        // Update broadcast status if completed
        $completed = $this->broadcast->sent_count + $this->broadcast->failed_count;
        if ($completed >= $this->broadcast->total_contacts) {
            $this->broadcast->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }
    }
}