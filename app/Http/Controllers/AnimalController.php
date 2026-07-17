<?php

namespace App\Http\Controllers;

use App\Enums\Animals\Gender;
use App\Http\Resources\AnimalMiniatureResource;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\Specie;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
            'taxonomy' => $this->taxonomy(),
        ]);
    }

    private function taxonomy(): array
    {
        return [
            'species' => Specie::query()->select('id', 'name')->get(),
            'breeds' => Breed::query()->select('id', 'name', 'specie_id as specieId')->get(),
            'statuses' => AnimalStatus::query()->select('id', 'name')->get(),
            'furColors' => FurColor::query()->select('id', 'name', 'color')->get(),
            'furPatterns' => FurPattern::query()->select('id', 'name')->get(),
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:255',
            'image' => 'nullable|string',
            'gender' => ['required', Rule::enum(Gender::class)],
            'chip' => 'required|string|unique:animals,chip',
            'animal_status_id' => 'required|exists:animal_statuses,id',
            'specie_id' => 'nullable|exists:species,id',
            'breed_id' => 'nullable|exists:breeds,id',
            'fur_color_id' => 'nullable|exists:fur_colors,id',
            'secondary_fur_color_id' => 'nullable|exists:fur_colors,id',
            'fur_pattern_id' => 'nullable|exists:fur_patterns,id',
            'personality' => 'required|string|min:2',
            'born_at' => 'date|before_or_equal:today',
            'vaccines' => 'nullable|array', // null = "unknown"
            'vaccines.*.date' => 'required|date',
            'vaccines.*.id' => 'required|exists:vaccines,id',
        ]);

        $currentUser = $request->user();
    }
}
