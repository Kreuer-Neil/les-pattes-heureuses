<?php

use App\Enums\Animals\MovementType;
use App\Enums\Animals\Status;
use App\Enums\Roles;
use App\Models\Animal;
use App\Models\AnimalMovement;
use App\Models\AnimalStatus;
use App\Models\User;
use App\Models\UserRole;
use App\Services\ShelterStatistics;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();
});

function createAnimalWithMovement(array $movement): Animal
{
    $animal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);

    AnimalMovement::create(['animal_id' => $animal->id, ...$movement]);

    return $animal;
}

test('presentCountAt counts only animals whose latest movement at-or-before the date is a recovery', function () {
    // Still present: recovered before the cutoff, no departure since.
    createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-06-01']);

    // Departed before the cutoff: recovered, then adopted.
    $departed = createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-06-01']);
    AnimalMovement::create(['animal_id' => $departed->id, 'type' => MovementType::AdoptedDeparture, 'occurred_at' => '2026-06-15']);

    // Recovered after the cutoff: shouldn't count as present as of the cutoff.
    createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-07-01']);

    // Recovered, departed, then recovered again (returned) before the cutoff.
    $returned = createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-05-01']);
    AnimalMovement::create(['animal_id' => $returned->id, 'type' => MovementType::AdoptedDeparture, 'occurred_at' => '2026-05-15']);
    AnimalMovement::create(['animal_id' => $returned->id, 'type' => MovementType::Recovery, 'occurred_at' => '2026-05-20']);

    expect(AnimalMovement::presentCountAt(Carbon::parse('2026-06-30')))->toBe(2);
});

test('ShelterStatistics::forPeriod counts movements within the period and ignores movements outside it', function () {
    createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-06-10']);
    createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-07-10']);

    $adopted = createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-05-01']);
    AnimalMovement::create(['animal_id' => $adopted->id, 'type' => MovementType::AdoptedDeparture, 'occurred_at' => '2026-06-20']);

    $stats = ShelterStatistics::forPeriod(Carbon::parse('2026-06-01'), Carbon::parse('2026-06-30'));

    expect($stats['animalsReceived'])->toBe(1)
        ->and($stats['successfulAdoptions'])->toBe(1)
        ->and($stats['animalsStillPresent'])->toBe(1);
});

test('ShelterStatistics::allTime matches the homepage stat semantics', function () {
    createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-01-01']);

    $adopted = createAnimalWithMovement(['type' => MovementType::Recovery, 'occurred_at' => '2026-01-01']);
    AnimalMovement::create(['animal_id' => $adopted->id, 'type' => MovementType::AdoptedDeparture, 'occurred_at' => '2026-02-01']);
    $adopted->update(['animal_status_id' => AnimalStatus::where('name', Status::Adopted->value)->value('id')]);

    $stats = ShelterStatistics::allTime();

    expect($stats['saved'])->toBe(2)
        ->and($stats['adopted'])->toBe(1)
        ->and($stats['searching'])->toBe(1);
});

test('admin sees statistics on the dashboard', function () {
    $this->actingAs($this->admin)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('statistics', fn ($statistics) => $statistics !== null));
});

test('volunteer does not see statistics on the dashboard', function () {
    $this->actingAs($this->volunteer)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->where('statistics', null));
});

test('admin can download the PDF report', function () {
    $response = $this->actingAs($this->admin)
        ->get(route('reports.export', ['start' => '2026-06-01', 'end' => '2026-06-30']));

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('volunteer cannot download the PDF report', function () {
    $this->actingAs($this->volunteer)
        ->get(route('reports.export', ['start' => '2026-06-01', 'end' => '2026-06-30']))
        ->assertForbidden();
});

test('guest cannot download the PDF report', function () {
    $this->get(route('reports.export', ['start' => '2026-06-01', 'end' => '2026-06-30']))
        ->assertRedirect(route('login'));
});
