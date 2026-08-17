<?php

namespace App\Http\Controllers;

use App\Enums\Animals\Gender;
use App\Models\Animal;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\Specie;
use App\Services\AnimalSearch;
use Illuminate\Http\Request;

class AdoptionController extends Controller
{
    public function index(Request $request)
    {
        $results = AnimalSearch::search(
            $request->only(['q', 'specie', 'breed', 'color', 'gender', 'age']),
            Animal::available(),
        );

        return view('client.adoption', [
            'exactAnimals' => $results->exact,
            'closeAnimals' => $results->close,
            'hasCriteria' => $results->hasCriteria,
            'speciesOptions' => Specie::orderBy('name')->pluck('name')->map(fn (string $name) => __('client.species.'.$name)),
            'breedsOptions' => Breed::orderBy('name')->get()->map(fn (Breed $breed) => $breed->label),
            'colorsOptions' => FurColor::orderBy('name')->pluck('name')->map(fn (string $name) => __('client.colors.'.$name)),
            'gendersOptions' => collect(Gender::cases())->map(fn (Gender $gender) => __('client.animal.genders.'.$gender->value)),
        ]);
    }

    public function show(Animal $animal)
    {
        // TODO replace with a ClientAnimalPolicy (view)
        abort_unless(Animal::visible()->whereKey($animal->id)->exists(), 404);

        $animal->load(['specie', 'breed', 'furColor', 'secondaryFurColor', 'furSchema', 'status']);

        return view('client.animal', compact('animal'));
    }
}
