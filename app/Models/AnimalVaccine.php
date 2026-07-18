<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnimalVaccine extends Model
{
    //
    protected $fillable = [
        'vaccinated_at',
        'vaccine_id',
    ];
}
