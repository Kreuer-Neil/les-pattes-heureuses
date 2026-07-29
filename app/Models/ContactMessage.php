<?php

namespace App\Models;

use App\Enums\ContactMessageType;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'type', 'content', 'read_at',
    ];

    protected $casts = [
        'type' => ContactMessageType::class,
        'read_at' => 'datetime',
    ];
}
