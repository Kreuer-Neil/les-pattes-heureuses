<?php

namespace App\Models;

use App\Enums\PendingChanges;
use App\Enums\PendingChangeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingAnimalChanges extends Model
{
    protected $fillable = [
        'action', 'status', 'animal_id', 'user_id', 'payload',
    ];

    protected $casts = [
        'action' => PendingChanges::class,
        'status' => PendingChangeStatus::class,
        'payload' => 'array',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
