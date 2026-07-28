<?php

namespace App\Models;

use App\Enums\PendingApprobationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdoptionRequest extends Model
{
    protected $fillable = [
        'animal_id', 'adopter_profile_id', 'content', 'status',
    ];

    protected $casts = [
        'status' => PendingApprobationStatus::class,
    ];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function adopterProfile(): BelongsTo
    {
        return $this->belongsTo(AdopterProfile::class);
    }
}
