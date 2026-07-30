<?php

namespace App\Services;

use App\Enums\Animals\MovementType;
use App\Models\Animal;
use App\Models\AnimalMovement;
use Carbon\CarbonInterface;

// Stat-computing logic reused by both the admin dashboard (period-scoped) and the
// public homepage (all-time) — see Feature Priority List #5 in CLAUDE.md.
class ShelterStatistics
{
    public static function forPeriod(CarbonInterface $start, CarbonInterface $end): array
    {
        return [
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'animalsReceived' => AnimalMovement::occurredBetween($start, $end)
                ->where('type', MovementType::Recovery)
                ->count(),
            'successfulAdoptions' => AnimalMovement::occurredBetween($start, $end)
                ->where('type', MovementType::AdoptedDeparture)
                ->count(),
            'animalsStillPresent' => AnimalMovement::presentCountAt($end),
        ];
    }

    public static function allTime(): array
    {
        return [
            'saved' => AnimalMovement::where('type', MovementType::Recovery)->count(),
            'searching' => Animal::available()->count(),
            'adopted' => AnimalMovement::where('type', MovementType::AdoptedDeparture)->count(),
        ];
    }
}
