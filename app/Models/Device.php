<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_id',
        'device_id',
        'phone_number',
        'status',
        'last_connected_at'
    ];

    protected $casts = [
        'last_connected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function broadcasts()
    {
        return $this->hasMany(Broadcast::class);
    }
}
