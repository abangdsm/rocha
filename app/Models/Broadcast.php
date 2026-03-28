<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Broadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'device_id', 'name', 'message', 'type', 'media_path',
        'status', 'total_contacts', 'sent_count', 'failed_count',
        'scheduled_at', 'started_at', 'completed_at'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function contacts()
    {
        return $this->belongsToMany(Contact::class, 'broadcast_contacts')
                    ->withPivot('status', 'error', 'sent_at')
                    ->withTimestamps();
    }

    public function broadcastContacts()
    {
        return $this->hasMany(BroadcastContact::class);
    }
}