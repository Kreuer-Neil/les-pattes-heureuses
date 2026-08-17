<?php

namespace App\Http\Controllers;

use App\Enums\Animals\Gender;
use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use App\Enums\PendingChanges;
use App\Http\Resources\AnimalMiniatureResource;
use App\Http\Resources\AnimalResource;
use App\Jobs\HandleImagesUploads;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\AnimalVaccine;
use App\Models\Breed;
use App\Models\FurColor;
use App\Models\FurPattern;
use App\Models\PendingAnimalChanges;
use App\Models\Specie;
use App\Models\Vaccine;
use App\Services\AnimalWriter;
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

        // Gets an array of Status's values
        $validStatuses = array_column(Status::cases(), 'value');

        $validated = $request->validate([
            'status_filter' => ['sometimes', 'string', Rule::in(array_merge(['active', 'gone'], $validStatuses))],
        ]);
        $statusFilter = $validated['status_filter'] ?? 'active';
        $statuses = $this->resolveStatusFilter($statusFilter);

        $specieId = $request->integer('specie') ?: null;
        $breedId = $request->integer('breed') ?: null;
        $gender = in_array($request->query('gender'), ['M', 'F'], true) ? $request->query('gender') : null;
        $search = trim((string) $request->query('q', ''));

        $query = Animal::query()->whereHas('status', fn ($q) => $q->whereIn('name', $statuses));

        if ($specieId) {
            $query->where('specie_id', $specieId);
        }

        if ($breedId) {
            $query->where('breed_id', $breedId);
        }

        if ($gender) {
            $query->where('gender', $gender);
        }

        if ($search !== '') {
            $query->where('name', 'like', '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%');
        }

        $animals = $query->get();

        return [
            'hasAccess' => $hasAccess,
            'animals' => AnimalMiniatureResource::collection($animals)->toArray($request),
            'taxonomy' => $this->taxonomy(),
            'filters' => [
                'status' => $statusFilter,
                'specie' => $specieId,
                'breed' => $breedId,
                'gender' => $gender,
                'q' => $search,
            ],
        ];
    }

    private function resolveStatusFilter(string $statusFilter): array
    {
        return match ($statusFilter) {
            'active' => [Status::Available->value, Status::Healing->value, Status::Pending->value],
            'gone' => [Status::Adopted->value, Status::Deceased->value, Status::Unknown->value],
            default => [$statusFilter],
        };
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
            'recovered_at' => 'required|date|before_or_equal:today',
            // Vaccines
            'vaccines' => 'nullable|array', // null = "unknown"
            'vaccines.*.date' => 'required|date',
            'vaccines.*.id' => 'required|exists:vaccines,id',
        ]);

        // $currentUser = $request->user();

        $animal = new Animal(collect($validated)->except(['image', 'vaccines', 'recovered_at'])->all());

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
            HandleImagesUploads::dispatch($imageName, null, $imagePath, $directory);

            $animal->image = $imageName;
        }

        if (Gate::allows('create', Animal::class)) {
            AnimalWriter::create($animal, $vaccines, $validated['recovered_at']);
        } else {
            Gate::authorize('suggest', Animal::class);
            $pendingAnimalChange = PendingAnimalChanges::create([
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
                    'recovered_at' => $validated['recovered_at'],
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
            HandleImagesUploads::dispatch($imageName, null, $imagePath, $directory);

            $attributes['image'] = $imageName;
        }

        if (Gate::allows('update', $animal)) {
            AnimalWriter::update(
                $animal,
                $attributes,
                array_key_exists('vaccines', $validated) ? $vaccines : null,
            );
        } else {
            Gate::authorize('suggest', $animal);
            $pendingAnimalChange = PendingAnimalChanges::create([
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

    public function markDeceased(Animal $animal)
    {
        Gate::authorize('setAnimalDeceased', Animal::class);

        AnimalWriter::update($animal, [
            'animal_status_id' => AnimalStatus::where('name', Status::Deceased->value)->value('id'),
        ], null);

        return redirect()->back();
    }

    public function recoverAnimal(Request $request, Animal $animal)
    {
        Gate::authorize('changeStatus', $animal);

        $activeStatusIds = AnimalStatus::whereIn('name', [
            Status::Available->value,
            Status::Healing->value,
            Status::Pending->value,
        ])->pluck('id');

        $validated = $request->validate([
            'animal_status_id' => ['required', Rule::in($activeStatusIds)],
        ]);

        if (Gate::allows('update', $animal)) {
            AnimalWriter::update($animal, $validated, null);
        } else {
            Gate::authorize('suggest', $animal);
            PendingAnimalChanges::create([
                'action' => PendingChanges::Update,
                'status' => PendingApprobationStatus::Pending,
                'animal_id' => $animal->id,
                'user_id' => $request->user()->id,
                'payload' => $validated,
            ]);
        }

        return redirect()->back();
    }
}
