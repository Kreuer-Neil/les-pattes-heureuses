<?php

use App\Enums\Animals\Gender;
use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use App\Enums\PendingChanges;
use App\Enums\Roles;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\PendingAnimalChanges;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Vaccine;

beforeEach(function () {
    $this->availableStatusId = AnimalStatus::where('name', Status::Available->value)->value('id');

    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();

    $this->vaccine = Vaccine::create(['name' => 'Rabies']);
});

test('accepting a Store change creates the animal and its vaccines', function () {
    $change = PendingAnimalChanges::create([
        'action' => PendingChanges::Store,
        'status' => PendingApprobationStatus::Pending,
        'user_id' => $this->volunteer->id,
        'payload' => [
            'name' => 'Moka',
            'image' => null,
            'gender' => Gender::Male->value,
            'chip' => 'chip-123',
            'animal_status_id' => $this->availableStatusId,
            'specie_id' => null,
            'breed_id' => null,
            'fur_color_id' => null,
            'secondary_fur_color_id' => null,
            'fur_pattern_id' => null,
            'personality' => 'Playful',
            'born_at' => '2020-01-01',
            'vaccines' => [
                ['vaccine_id' => $this->vaccine->id, 'vaccinated_at' => '2023-01-01'],
            ],
        ],
    ]);

    $this->actingAs($this->admin)
        ->patch(route('animal-changes.accept', $change))
        ->assertRedirect();

    $animal = Animal::where('chip', 'chip-123')->first();

    expect($animal)->not->toBeNull()
        ->and($animal->name)->toBe('Moka')
        ->and($animal->vaccines()->count())->toBe(1)
        ->and($change->fresh()->status)->toBe(PendingApprobationStatus::Approved);
});

test('accepting an Update change applies the payload to the existing animal', function () {
    $animal = Animal::factory()->create([
        'animal_status_id' => $this->availableStatusId,
        'name' => 'Old Name',
    ]);

    $change = PendingAnimalChanges::create([
        'action' => PendingChanges::Update,
        'status' => PendingApprobationStatus::Pending,
        'animal_id' => $animal->id,
        'user_id' => $this->volunteer->id,
        'payload' => [
            'name' => 'New Name',
            'gender' => $animal->gender,
            'chip' => $animal->chip,
            'animal_status_id' => $this->availableStatusId,
            'specie_id' => null,
            'breed_id' => null,
            'fur_color_id' => null,
            'secondary_fur_color_id' => null,
            'fur_pattern_id' => null,
            'personality' => $animal->personality,
            'born_at' => (string) $animal->born_at,
            'vaccines' => [
                ['vaccine_id' => $this->vaccine->id, 'vaccinated_at' => '2023-06-01'],
            ],
        ],
    ]);

    $this->actingAs($this->admin)
        ->patch(route('animal-changes.accept', $change))
        ->assertRedirect();

    expect($animal->fresh()->name)->toBe('New Name')
        ->and($animal->vaccines()->count())->toBe(1)
        ->and($change->fresh()->status)->toBe(PendingApprobationStatus::Approved);
});

test('denying a change discards it without touching the animal', function () {
    $animal = Animal::factory()->create([
        'animal_status_id' => $this->availableStatusId,
        'name' => 'Untouched',
    ]);

    $change = PendingAnimalChanges::create([
        'action' => PendingChanges::Update,
        'status' => PendingApprobationStatus::Pending,
        'animal_id' => $animal->id,
        'user_id' => $this->volunteer->id,
        'payload' => ['name' => 'Should Not Apply'],
    ]);

    $this->actingAs($this->admin)
        ->patch(route('animal-changes.deny', $change))
        ->assertRedirect();

    expect($animal->fresh()->name)->toBe('Untouched')
        ->and($change->fresh()->status)->toBe(PendingApprobationStatus::Rejected);
});

test('volunteer cannot accept or deny changes', function () {
    $change = PendingAnimalChanges::create([
        'action' => PendingChanges::Update,
        'status' => PendingApprobationStatus::Pending,
        'user_id' => $this->volunteer->id,
        'payload' => ['name' => 'Whatever'],
    ]);

    $this->actingAs($this->volunteer)
        ->patch(route('animal-changes.accept', $change))
        ->assertForbidden();

    $this->actingAs($this->volunteer)
        ->patch(route('animal-changes.deny', $change))
        ->assertForbidden();
});

test('a proposed animal change appears in the admin attention feed', function () {
    $this->actingAs($this->volunteer)
        ->post(route('animals.store'), [
            'name' => 'Proposed Pet',
            'gender' => Gender::Male->value,
            'chip' => 'chip-proposed',
            'animal_status_id' => $this->availableStatusId,
            'personality' => 'Shy',
            'born_at' => '2021-01-01',
        ])
        ->assertRedirect();

    $response = $this->actingAs($this->admin)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('items', fn ($items) => collect($items)->contains(
            fn ($item) => $item['type'] === 'animal_change' && $item['animalName'] === 'Proposed Pet',
        ))
    );
});
