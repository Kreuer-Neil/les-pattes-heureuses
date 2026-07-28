<?php

namespace App\Models;

use App\Enums\ContactMessageType;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'email', 'type', 'content',
    ];

    protected $casts = [
        'type' => ContactMessageType::class,
    ];
}
