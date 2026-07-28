<?php

namespace App\Http\Controllers;

use App\Enums\Animals\Gender;
use App\Enums\PendingApprobationStatus;
use App\Enums\PendingChanges;
use App\Http\Resources\AnimalMiniatureResource;
use App\Http\Resources\AnimalResource;
use App\Jobs\HandleAnimalsImageUploads;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\AnimalVaccine;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\PendingAnimalChanges;
use App\Models\Specie;
use App\Models\Vaccine;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Str;

class AnimalController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('animals/index', $this->indexProps($request));
    }

    public function show(Request $request, Animal $animal)
    {
        $animalData = (new AnimalResource($animal))->toArray($request);

        if ($request->wantsJson()) {
            // return $animalData;
            return response()->json($animalData);
        }

        return Inertia::render('animals/index', array_merge(
            $this->indexProps($request),
            ['selectedAnimal' => $animalData],
        ));
    }

    private function indexProps(Request $request): array
    {
        $hasAccess = (auth()->check()); // TODO Gate checking?

        // TODO add filtering later
        $animals = Animal::all();

        return [
            'hasAccess' => $hasAccess,
            'animals' => AnimalMiniatureResource::collection($animals)->toArray($request),
            'taxonomy' => $this->taxonomy(),
        ];
    }

    private function taxonomy(): array
    {
        return [
            'species' => Specie::query()->select('id', 'name')->get(),
            'breeds' => Breed::query()->select('id', 'name', 'specie_id as specieId')->get(),
            'statuses' => AnimalStatus::query()->select('id', 'name')->get(),
            'furColors' => FurColor::query()->select('id', 'name', 'color')->get(),
            'furPatterns' => FurPattern::query()->select('id', 'name')->get(),
            'vaccines' => Vaccine::query()->select('id', 'name')->get(),
        ];
    }

    public function store(Request $request)
    {
        abort_unless(Gate::any(['create', 'suggest'], Animal::class), 403);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'image' => 'nullable|file|image|extensions:jpg,jpeg,png,webp,gif|max:5120',
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
            // Vaccines
            'vaccines' => 'nullable|array', // null = "unknown"
            'vaccines.*.date' => 'required|date',
            'vaccines.*.id' => 'required|exists:vaccines,id',
        ]);

        // $currentUser = $request->user();

        $animal = new Animal(collect($validated)->except(['image', 'vaccines'])->all());

        // Adding vaccines
        $vaccines = [];
        if (array_key_exists('vaccines', $validated)) {
            foreach ($validated['vaccines'] as $vaccine) {
                $vaccines[] = new AnimalVaccine(
                    [
                        'vaccinated_at' => $vaccine['date'],
                        'vaccine_id' => $vaccine['id'],
                    ]
                );
            }
        }

        // Image handling
        if (array_key_exists('image', $validated)) {

            $imagePath = $validated['image']
                ->store('images/animals', 'public');

            $imageName = Str::beforeLast(Str::afterLast($imagePath, '/'), '.');

            $directory = 'animals';
            HandleAnimalsImageUploads::dispatch($imageName, null, $imagePath, $directory);

            $animal->image = $imageName;
        }

        if (Gate::allows('create', Animal::class)) {
            $this->createAnimal($animal, $vaccines);
        } else {
            Gate::authorize('suggest', Animal::class);
            PendingAnimalChanges::create([
                'action' => PendingChanges::Store,
                'status' => PendingApprobationStatus::Pending,
                'user_id' => $request->user()->id,
                'payload' => [
                    'name' => $animal->name,
                    'image' => $animal->image,
                    'gender' => $animal->gender,
                    'chip' => $animal->chip,
                    'animal_status_id' => $animal->animal_status_id,
                    'specie_id' => $animal->specie_id,
                    'breed_id' => $animal->breed_id,
                    'fur_color_id' => $animal->fur_color_id,
                    'secondary_fur_color_id' => $animal->secondary_fur_color_id,
                    'fur_pattern_id' => $animal->fur_pattern_id,
                    'personality' => $animal->personality,
                    'born_at' => $animal->born_at,
                    // Notes?
                    'vaccines' => $vaccines,
                ],
            ]);
        }

        return redirect()->back();
    }

    public function update(Request $request, Animal $animal)
    {
        abort_unless(Gate::any(['update', 'suggest'], $animal), 403);

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'image' => 'nullable|file|image|extensions:jpg,jpeg,png,webp,gif|max:5120',
            'gender' => ['required', Rule::enum(Gender::class)],
            'chip' => ['required', 'string', Rule::unique('animals', 'chip')->ignore($animal->id)],
            'animal_status_id' => 'required|exists:animal_statuses,id',
            'specie_id' => 'nullable|exists:species,id',
            'breed_id' => 'nullable|exists:breeds,id',
            'fur_color_id' => 'nullable|exists:fur_colors,id',
            'secondary_fur_color_id' => 'nullable|exists:fur_colors,id',
            'fur_pattern_id' => 'nullable|exists:fur_patterns,id',
            'personality' => 'required|string|min:2',
            'born_at' => 'date|before_or_equal:today',
            // Vaccines
            'vaccines' => 'nullable|array', // null = "unknown"
            'vaccines.*.date' => 'required|date',
            'vaccines.*.id' => 'required|exists:vaccines,id',
        ]);

        $attributes = collect($validated)->except(['image', 'vaccines'])->all();

        // Vaccines
        $vaccines = [];
        if (array_key_exists('vaccines', $validated)) {
            foreach ($validated['vaccines'] as $vaccine) {
                $vaccines[] = [
                    'vaccinated_at' => $vaccine['date'],
                    'vaccine_id' => $vaccine['id'],
                ];
            }
        }

        // Image handling
        if (array_key_exists('image', $validated)) {
            $imagePath = $validated['image']
                ->store('images/animals', 'public');

            $imageName = Str::beforeLast(Str::afterLast($imagePath, '/'), '.');

            $directory = 'animals';
            HandleAnimalsImageUploads::dispatch($imageName, null, $imagePath, $directory);

            $attributes['image'] = $imageName;
        }

        if (Gate::allows('update', $animal)) {
            $animal->update($attributes);

            if (array_key_exists('vaccines', $validated)) {
                $animal->vaccines()->detach();

                foreach ($vaccines as $vaccine) {
                    AnimalVaccine::create([
                        'animal_id' => $animal->id,
                        'vaccine_id' => $vaccine['vaccine_id'],
                        'vaccinated_at' => $vaccine['vaccinated_at'],
                    ]);
                }
            }
        } else {
            Gate::authorize('suggest', $animal);
            PendingAnimalChanges::create([
                'action' => PendingChanges::Update,
                'status' => PendingApprobationStatus::Pending,
                'animal_id' => $animal->id,
                'user_id' => $request->user()->id,
                'payload' => array_merge($attributes, [
                    // Notes?
                    'vaccines' => $vaccines,
                ]),
            ]);
        }

        return redirect()->back();
    }

    private function createAnimal(Animal $animal, array $vaccines)
    {
        $animal->save();

        // Vaccinations
        if ($vaccines && $vaccines !== []) {
            foreach ($vaccines as $vaccine) {
                AnimalVaccine::create([
                    'animal_id' => $animal->id,
                    'vaccine_id' => $vaccine['vaccine_id'],
                    'vaccinated_at' => $vaccine['vaccinated_at'],
                ]);
            }
        }

        // Same for notes?
    }
}
