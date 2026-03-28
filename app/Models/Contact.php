<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_contacts';  // ← Tambahkan ini

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'address', 'notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'contact_whatsapp_group', 'contact_id', 'group_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'contact_whatsapp_tag', 'contact_id', 'tag_id');
    }
}