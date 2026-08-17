<?php

use App\Enums\Animals\Specie as SpecieEnum;
use App\Enums\Animals\Status;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\Breed;
use App\Models\Specie;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();

    $this->animalsByStatus = collect(Status::cases())->mapWithKeys(
        fn (Status $status) => [
            $status->value => Animal::factory()->create([
                'animal_status_id' => AnimalStatus::where('name', $status->value)->value('id'),
            ]),
        ],
    );
});

/**
 * Runs the request and returns the ids from the Inertia `animals` prop,
 * without relying on the response's internal representation.
 */
function requestAnimalIds($user, array $query = []): array
{
    $ids = null;

    test()->actingAs($user)
        ->get(route('animals.index', $query))
        ->assertOk()
        // assertInertia()'s where() only reports pass/fail (it's a JSON assertion,
        // not a data getter), so the only way to pull the actual `animals` value
        // back out is to capture it by reference from inside the callback.
        ->assertInertia(function ($page) use (&$ids) {
            $page->where('animals', function ($animals) use (&$ids) {
                $ids = collect($animals)->pluck('id')->all();

                return true;
            });
        });

    return $ids;
}

test('the default view only shows active-status animals', function () {
    $ids = requestAnimalIds($this->user);

    expect($ids)->toContain($this->animalsByStatus[Status::Available->value]->id)
        ->toContain($this->animalsByStatus[Status::Healing->value]->id)
        ->toContain($this->animalsByStatus[Status::Pending->value]->id)
        ->not->toContain($this->animalsByStatus[Status::Adopted->value]->id)
        ->not->toContain($this->animalsByStatus[Status::Deceased->value]->id)
        ->not->toContain($this->animalsByStatus[Status::Unknown->value]->id);
});

test('the gone status filter shows adopted, deceased, and unknown animals', function () {
    $ids = requestAnimalIds($this->user, ['status_filter' => 'gone']);

    expect($ids)->toContain($this->animalsByStatus[Status::Adopted->value]->id)
        ->toContain($this->animalsByStatus[Status::Deceased->value]->id)
        ->toContain($this->animalsByStatus[Status::Unknown->value]->id)
        ->not->toContain($this->animalsByStatus[Status::Available->value]->id)
        ->not->toContain($this->animalsByStatus[Status::Healing->value]->id)
        ->not->toContain($this->animalsByStatus[Status::Pending->value]->id);
});

test('an individual status filter narrows the list to just that status', function () {
    $ids = requestAnimalIds($this->user, ['status_filter' => Status::Deceased->value]);

    expect($ids)->toBe([$this->animalsByStatus[Status::Deceased->value]->id]);
});

test('an invalid status filter value is rejected', function () {
    $this->actingAs($this->user)
        ->get(route('animals.index', ['status_filter' => 'not-a-real-status']))
        ->assertInvalid(['status_filter']);
});

test('the specie filter narrows the list', function () {
    $dog = Specie::where('name', SpecieEnum::Dog->value)->firstOrFail();
    $cat = Specie::where('name', SpecieEnum::Cat->value)->firstOrFail();

    $dogAnimal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'specie_id' => $dog->id,
    ]);
    $catAnimal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'specie_id' => $cat->id,
    ]);

    $ids = requestAnimalIds($this->user, ['specie' => $dog->id]);

    expect($ids)->toContain($dogAnimal->id)->not->toContain($catAnimal->id);
});

test('the breed filter narrows the list', function () {
    $dog = Specie::where('name', SpecieEnum::Dog->value)->firstOrFail();
    $corgi = Breed::where('name', 'corgi')->firstOrFail();
    $chihuahua = Breed::where('name', 'chihuahua')->firstOrFail();

    $corgiAnimal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'specie_id' => $dog->id,
        'breed_id' => $corgi->id,
    ]);
    $chihuahuaAnimal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'specie_id' => $dog->id,
        'breed_id' => $chihuahua->id,
    ]);

    $ids = requestAnimalIds($this->user, ['breed' => $corgi->id]);

    expect($ids)->toContain($corgiAnimal->id)->not->toContain($chihuahuaAnimal->id);
});

test('the gender filter narrows the list', function () {
    $male = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'gender' => 'M',
    ]);
    $female = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'gender' => 'F',
    ]);

    $ids = requestAnimalIds($this->user, ['gender' => 'F']);

    expect($ids)->toContain($female->id)->not->toContain($male->id);
});

test('the name search filters by a partial, case-insensitive match', function () {
    $fluffy = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'name' => 'Fluffy',
    ]);
    $rex = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
        'name' => 'Rex',
    ]);

    $ids = requestAnimalIds($this->user, ['q' => 'flu']);

    expect($ids)->toContain($fluffy->id)->not->toContain($rex->id);
});

test('the resolved filters are echoed back in the response', function () {
    $this->actingAs($this->user)
        ->get(route('animals.index', ['status_filter' => 'gone', 'gender' => 'F', 'q' => 'flu']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('filters.status', 'gone')
            ->where('filters.gender', 'F')
            ->where('filters.q', 'flu')
        );
});
