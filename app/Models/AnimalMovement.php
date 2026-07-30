<?php

namespace App\Models;

use App\Enums\Animals\MovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalMovement extends Model
{
    protected $fillable = [
        'animal_id',
        'type',
        'occurred_at',
    ];

    protected $casts = [
        'type' => MovementType::class,
        'occurred_at' => 'date',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}