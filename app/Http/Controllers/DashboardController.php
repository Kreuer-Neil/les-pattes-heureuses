<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminAttentionCollection;
use App\Models\AdoptionRequest;
use App\Models\Animal;
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
        ]);
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
