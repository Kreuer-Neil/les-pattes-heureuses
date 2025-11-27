<?php

namespace App\Models\Animals;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    protected $fillable = [
        'name'
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(AnimalStatus::class, 'animal_status_id');
    }

    public function specie(): BelongsTo
    {
        return $this->belongsTo(Specie::class);
    }

    public function subSpecie(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function peltColor(): BelongsTo
    {
        return $this->belongsTo(PeltColor::class);
    }

    public function secondaryPeltColor(): BelongsTo
    {
        return $this->belongsTo(PeltColor::class, 'secondary_pelt_color_id');
    }

    public function peltSchema(): BelongsTo
    {
        return $this->belongsTo(PeltSchema::class);
    }

    // More specific fn
    public function notes(): HasMany
    {
        return $this->hasMany(AnimalNote::class);
    }

    public function vaccines():BelongsToMany
    {
        return $this->belongsToMany(Vaccine::class, AnimalVaccine::class)->withPivotValue('vaccinated_at');
    }


    // Static fn to return specific lists of animals
    /*public static function available()
    {
        return Animal::all()->where('status_id', '=', 1);
    }

    public static function adopted()
    {
        return Animal::all()->where('status_id', '=', 3);
    }

    public static function healing()
    {
        return Animal::all()->where('status_id', '=', 4);
    }*/


}
