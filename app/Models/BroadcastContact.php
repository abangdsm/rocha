<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BroadcastContact extends Model
{
    use HasFactory;

    protected $table = 'broadcast_contacts';

    protected $fillable = [
        'broadcast_id', 'contact_id', 'status', 'error', 'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
}