<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'image', 'breed_id','fur_color_id','fur_pattern_id','secondary_fur_color_id','specie_id', 'animal_status_id'
    ];

    public function status(): BelongsTo
    {
        return $this->belongsTo(AnimalStatus::class, 'animal_status_id');
    }

    public function specie(): BelongsTo
    {
        return $this->belongsTo(Specie::class);
    }

    public function breed(): BelongsTo
    {
        return $this->belongsTo(Breed::class);
    }

    public function furColor(): BelongsTo
    {
        return $this->belongsTo(FurColor::class);
    }

    public function secondaryFurColor(): BelongsTo
    {
        return $this->belongsTo(FurColor::class, 'secondary_fur_color_id');
    }

    public function furSchema(): BelongsTo
    {
        return $this->belongsTo(FurPattern::class);
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
