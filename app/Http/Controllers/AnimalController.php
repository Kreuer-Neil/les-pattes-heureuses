<?php

namespace App\Http\Controllers;

use App\Http\Resources\AnimalMiniatureResource;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\Specie;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnimalController extends Controller
{
    public function index(Request $request)
    {
        $hasAccess = (auth()->check()); // TODO Gate checking?

        // TODO add filtering later
        $animals = Animal::all();

        return Inertia::render('animals/index', [
            'hasAccess' => $hasAccess,
            'animals' => AnimalMiniatureResource::collection($animals)->toArray($request),
            'filters' => $this->filters(),
        ]);
    }

    private function filters(): array
    {
        return [
            'species' => Specie::query()->select('id', 'name')->get(),
            'breeds' => Breed::query()->select('id', 'name')->get(),
            'statuses' => AnimalStatus::query()->select('id', 'name')->get(),
            'furColors' => FurColor::query()->select('id', 'name', 'color')->get(),
            'furPatterns' => FurPattern::query()->select('id', 'name')->get(),
        ];
    }
}
