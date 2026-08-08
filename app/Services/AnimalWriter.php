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
        $previousStatusId = $animal->animal_status_id;

        $animal->update($attributes);

        if ($vaccines !== null) {
            self::syncVaccines($animal, $vaccines);
        }

        self::recordMovementIfStatusChanged($animal, $previousStatusId);
    }

    private static function recordMovementIfStatusChanged(Animal $animal, int $previousStatusId): void
    {
        if (! $animal->wasChanged('animal_status_id')) {
            return;
        }

        $oldStatusName = AnimalStatus::where('id', $previousStatusId)->value('name');
        $newStatusName = AnimalStatus::where('id', $animal->animal_status_id)->value('name');

        // An animal leaving Adopted (e.g. a return after adoption) counts as a fresh
        // Recovery, the same as a first-time intake — see AnimalMovement::presentCountAt().
        $movementType = match (true) {
            $newStatusName === Status::Adopted->value => MovementType::AdoptedDeparture,
            $newStatusName === Status::Deceased->value => MovementType::DeceasedDeparture,
            $oldStatusName === Status::Adopted->value => MovementType::Recovery,
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
