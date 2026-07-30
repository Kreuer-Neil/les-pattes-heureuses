<?php

namespace App\Models;

use App\Enums\Animals\MovementType;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
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

    /**
     * Gets the animal movements occurring between $start and $end date.
     */
    public function scopeOccurredBetween(Builder $query, CarbonInterface $start, CarbonInterface $end): Builder
    {
        return $query->whereBetween('occurred_at', [$start->toDateString(), $end->toDateString()]);
    }

    /**
     * Gets the count of animals at a precise date
     */
    public static function presentCountAt(CarbonInterface $date): int
    {
        $latestPerAnimal = static::query()
            ->selectRaw('animal_id, type, ROW_NUMBER() OVER (PARTITION BY animal_id ORDER BY occurred_at DESC, id DESC) AS rn')
            ->where('occurred_at', '<=', $date->toDateString());

        return static::query()
            ->fromSub($latestPerAnimal, 'latest_movements')
            ->where('rn', 1)
            ->where('type', MovementType::Recovery->value)
            ->count();
    }
}
