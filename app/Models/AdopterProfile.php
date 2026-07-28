<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdopterProfile extends Model
{
    protected $fillable = [
        'email', 'first_name', 'last_name', 'details',
    ];

    public function adoptionRequests(): HasMany
    {
        return $this->hasMany(AdoptionRequest::class);
    }
}
