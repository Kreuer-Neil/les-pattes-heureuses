<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnimalNote extends Model
{
    protected $fillable = ['animal_id', 'user_id', 'title', 'text'];

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
