<?php

use App\Enums\Animals\MovementType;
use App\Enums\Animals\Status;
use App\Enums\Roles;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\User;
use App\Models\UserRole;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();

    $this->animal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);
});

test('creating an animal records a recovery movement', function () {
    $this->actingAs($this->admin)
        ->post(route('animals.store'), [
            'name' => 'Moka',
            'gender' => 'M',
            'chip' => 'chip-movement-test',
            'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
            'personality' => 'Playful',
            'born_at' => '2020-01-01',
            'recovered_at' => '2026-07-01',
        ])
        ->assertRedirect();

    $animal = Animal::where('chip', 'chip-movement-test')->first();

    expect($animal->movements()->where('type', MovementType::Recovery->value)->count())->toBe(1)
        ->and($animal->movements()->first()->occurred_at->toDateString())->toBe('2026-07-01');
});

test('admin can mark an animal as deceased, which records a departure movement', function () {
    $this->actingAs($this->admin)
        ->patch(route('animals.mark-deceased', $this->animal))
        ->assertRedirect();

    $animal = $this->animal->fresh();

    expect($animal->status->name)->toBe(Status::Deceased->value)
        ->and($animal->movements()->where('type', MovementType::DeceasedDeparture->value)->count())->toBe(1);
});

test('volunteer cannot mark an animal as deceased', function () {
    $this->actingAs($this->volunteer)
        ->patch(route('animals.mark-deceased', $this->animal))
        ->assertForbidden();

    expect($this->animal->fresh()->status->name)->toBe(Status::Available->value);
});

test('excludingDeceased scope hides deceased animals', function () {
    $this->animal->update([
        'animal_status_id' => AnimalStatus::where('name', Status::Deceased->value)->value('id'),
    ]);

    $available = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);

    $visibleIds = Animal::excludingDeceased()->pluck('id');

    expect($visibleIds)->not->toContain($this->animal->id)
        ->and($visibleIds)->toContain($available->id);
});
