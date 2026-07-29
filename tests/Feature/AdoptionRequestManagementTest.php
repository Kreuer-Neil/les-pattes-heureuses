<?php

use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use App\Enums\Roles;
use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\User;
use App\Models\UserRole;

beforeEach(function () {
    $this->animal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);

    $this->adopterProfile = AdopterProfile::create([
        'email' => 'sarah@example.com',
        'first_name' => 'Sarah',
        'last_name' => 'Dupont',
    ]);

    $this->adoptionRequest = AdoptionRequest::create([
        'animal_id' => $this->animal->id,
        'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'I would love to meet Moka.',
        'status' => PendingApprobationStatus::Unattended,
    ]);

    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();
});

test('admin can view the adoption requests list', function () {
    $this->actingAs($this->admin)
        ->get(route('adoption-requests.index'))
        ->assertOk();
});

test('volunteer cannot view the adoption requests list', function () {
    $this->actingAs($this->volunteer)
        ->get(route('adoption-requests.index'))
        ->assertForbidden();
});

test('guest cannot view the adoption requests list', function () {
    $this->get(route('adoption-requests.index'))
        ->assertRedirect(route('login'));
});

test('admin can transition an adoption request status', function () {
    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.update-status', $this->adoptionRequest), [
            'status' => PendingApprobationStatus::Pending->value,
        ])
        ->assertRedirect();

    expect($this->adoptionRequest->fresh()->status)->toBe(PendingApprobationStatus::Pending);
});

test('marking a request as contacted (pending) also puts the animal on hold', function () {
    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.update-status', $this->adoptionRequest), [
            'status' => PendingApprobationStatus::Pending->value,
        ])
        ->assertRedirect();

    expect($this->animal->fresh()->status->name)->toBe(Status::Pending->value);
});

// Accepting (validating after contacting back and doing paper stuff IRL) should set status to adopted, refusing should check if another adoptionRequest on this animal is pending and set animal status to available if none is.
test('accepting or rejecting a request does not touch the animal status', function () {
    $this->animal->update([
        'animal_status_id' => AnimalStatus::where('name', Status::Pending->value)->value('id'),
    ]);

    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.update-status', $this->adoptionRequest), [
            'status' => PendingApprobationStatus::Rejected->value,
        ])
        ->assertRedirect();

    expect($this->animal->fresh()->status->name)->toBe(Status::Pending->value);
});

test('volunteer cannot transition an adoption request status', function () {
    $this->actingAs($this->volunteer)
        ->patch(route('adoption-requests.update-status', $this->adoptionRequest), [
            'status' => PendingApprobationStatus::Approved->value,
        ])
        ->assertForbidden();

    expect($this->adoptionRequest->fresh()->status)->toBe(PendingApprobationStatus::Unattended);
});

test('a submitted adoption request appears in the admin attention feed', function () {
    $this->post(route('client.adoption.request', $this->animal), [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean@example.com',
        'message' => 'Interested!',
    ])->assertRedirect();

    $response = $this->actingAs($this->admin)->get(route('notifications.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('items', fn ($items) => collect($items)->contains(
            fn ($item) => $item['type'] === 'adoption_request' && $item['adopterProfile']['email'] === 'jean@example.com',
        ))
    );
});
