<?php

namespace App\Http\Controllers;

use App\Enums\Animals\MovementType;
use App\Http\Resources\AdminAttentionCollection;
use App\Http\Resources\AnimalDashboardResource;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Services\AdminAttentionFeed;
use App\Services\ShelterStatistics;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function render(Request $request)
    {
        $needsAttention = [];
        $unreadMessageCount = 0;

        if (Gate::allows('viewAny', AdoptionRequest::class)) {
            $items = AdminAttentionFeed::items()->take(5);
            $needsAttention = (new AdminAttentionCollection($items))->toArray($request);
            $unreadMessageCount = AdminAttentionFeed::unreadMessageCount();
        }

        $statistics = null;

        if (Gate::allows('viewStatistics', Animal::class)) {
            [$start, $end] = $this->statisticsPeriod($request);
            $statistics = ShelterStatistics::forPeriod($start, $end);
        }

        return Inertia::render('dashboard', [
            'needsAttention' => $needsAttention,
            'unreadMessageCount' => $unreadMessageCount,
            'statistics' => $statistics,
            'recentlyAddedAnimals' => $this->recentlyAddedAnimals($request),
        ]);
    }

    // Latest distinct animal per Recovery movement — covers both first intake and an
    // animal returning after a since-reverted adoption (see AnimalWriter::update()).
    private function recentlyAddedAnimals(Request $request): array
    {
        // Doesn't work since join tries to "merge" both IDs (apparently, even if it should be animal_movement.id)
        // and no duplicate animal check.
        // $animals = Animal::join('animal_movements')
        // ->where('animal_movements.type', MovementType::Recovery)
        // ->where('animal_movements.occurred_at', '>=', now()->subMonth())
        // ->orderBy('animal_movements.occurred_at')
        // ->take(4);

        $animals = AnimalMovement::query()
            ->where('type', MovementType::Recovery)
            ->where('occurred_at', '>=', now()->subMonth())
            ->orderByDesc('occurred_at')
            ->orderByDesc('id')
            ->with(['animal.status', 'animal.specie'])
            ->get()
            ->pluck('animal')
            ->unique('id')
            ->take(4)
            ->values();

        return AnimalDashboardResource::collection($animals)->toArray($request);
    }

    // simple security check to avoid invalid values and sets them back to start/end of month
    private function statisticsPeriod(Request $request): array
    {
        try {
            $start = $request->filled('start') ? Carbon::parse($request->query('start'))->startOfDay() : now()->startOfMonth();
            $end = $request->filled('end') ? Carbon::parse($request->query('end'))->endOfDay() : now()->endOfMonth();
        } catch (\Exception) {
            $start = now()->startOfMonth();
            $end = now()->endOfMonth();
        }

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }
}
