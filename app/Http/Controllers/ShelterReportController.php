<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Services\ShelterStatistics;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;

class ShelterReportController extends Controller
{
    public function export(Request $request)
    {
        Gate::authorize('viewStatistics', Animal::class);

        $validated = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
        ]);

        $start = Carbon::parse($validated['start'])->startOfDay();
        $end = Carbon::parse($validated['end'])->endOfDay();

        $statistics = ShelterStatistics::forPeriod($start, $end);

        $pdf = Pdf::loadView('pdf.monthly-report', [
            'statistics' => $statistics,
            'start' => $start,
            'end' => $end,
        ]);

        return $pdf->download("rapport-{$start->format('Y-m-d')}-{$end->format('Y-m-d')}.pdf");
    }
}
