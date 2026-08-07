<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use App\Enums\ContactMessageType;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'type', 'content', 'status', 'read_at',
    ];

    protected $casts = [
        'type' => ContactMessageType::class,
        'status' => ContactMessageStatus::class,
        'read_at' => 'datetime',
    ];
}
