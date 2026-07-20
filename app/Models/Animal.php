<?php

namespace App\Models;

use App\Enums\Animals\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'gender', 'chip', 'personality', 'born_at',
        'breed_id', 'fur_color_id', 'fur_pattern_id', 'secondary_fur_color_id', 'specie_id', 'animal_status_id',
    ];

    protected $casts = [
        'born_at' => 'date',
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

    public function vaccines(): BelongsToMany
    {
        return $this->belongsToMany(Vaccine::class, AnimalVaccine::class)->withPivotValue('vaccinated_at');
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereHas('status', fn (Builder $status) => $status->where('name', Status::Available->value));
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereHas('status', fn (Builder $status) => $status->whereIn('name', [
            Status::Available->value,
            Status::Healing->value,
            // Status::Pending->value,
        ]));
    }
}
