<?php

namespace App\Services;

use App\Enums\Animals\MovementType;
use App\Enums\Animals\Status;
use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Models\AnimalStatus;
use App\Models\AnimalVaccine;

// Moved methods here because AnimalController is already overloaded.
// Also easier since animals have vaccines + movements to handle on every change.
class AnimalWriter
{
    public static function create(Animal $animal, array $vaccines, ?string $recoveredAt = null): void
    {
        $animal->save();

        AnimalMovement::create([
            'animal_id' => $animal->id,
            'type' => MovementType::Recovery,
            'occurred_at' => $recoveredAt ?? now(),
        ]);

        if ($vaccines && $vaccines !== []) {
            self::syncVaccines($animal, $vaccines);
        }

        // Same for notes?
    }

    public static function update(Animal $animal, array $attributes, ?array $vaccines): void
    {
        $animal->update($attributes);

        if ($vaccines !== null) {
            self::syncVaccines($animal, $vaccines);
        }

        self::recordMovementIfStatusChanged($animal);
    }

    private static function recordMovementIfStatusChanged(Animal $animal): void
    {
        if (! $animal->wasChanged('animal_status_id')) {
            return;
        }

        $statusName = AnimalStatus::where('id', $animal->animal_status_id)->value('name');

        $movementType = match ($statusName) {
            Status::Adopted->value => MovementType::AdoptedDeparture,
            Status::Deceased->value => MovementType::DeceasedDeparture,
            default => null,
        };

        if ($movementType === null) {
            return;
        }

        AnimalMovement::create([
            'animal_id' => $animal->id,
            'type' => $movementType,
            'occurred_at' => now(),
        ]);
    }

    private static function syncVaccines(Animal $animal, array $vaccines): void
    {
        $animal->vaccines()->detach();

        foreach ($vaccines as $vaccine) {
            AnimalVaccine::create([
                'animal_id' => $animal->id,
                'vaccine_id' => $vaccine['vaccine_id'],
                'vaccinated_at' => $vaccine['vaccinated_at'],
            ]);
        }
    }
}
