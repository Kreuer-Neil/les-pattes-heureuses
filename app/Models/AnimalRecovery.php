<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalRecovery extends Model
{
    protected $fillable = [
        'animal_id',
        'recovered_at',
    ];

    protected $casts = [
        'recovered_at' => 'date',
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
