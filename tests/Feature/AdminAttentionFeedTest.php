<?php

use App\Enums\Animals\Status;
use App\Enums\ContactMessageType;
use App\Enums\PendingApprobationStatus;
use App\Enums\PendingChanges;
use App\Enums\Roles;
use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\ContactMessage;
use App\Models\PendingAnimalChanges;
use App\Models\User;
use App\Models\UserRole;
use App\Services\AdminAttentionFeed;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();

    $this->animal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);

    $this->adopterProfile = AdopterProfile::create(['email' => 'a@example.com', 'first_name' => 'A', 'last_name' => 'B']);
});

test('items() includes unattended and pending adoption requests, excludes approved/rejected', function () {
    $unattended = AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Unattended,
    ]);
    $pending = AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Pending,
    ]);
    AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Approved,
    ]);
    AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Rejected,
    ]);

    $ids = AdminAttentionFeed::items()->pluck('id')->all();

    expect($ids)->toContain($unattended->id)
        ->toContain($pending->id)
        ->and(AdminAttentionFeed::items())->toHaveCount(2);
});

test('items() includes pending animal changes, excludes approved/rejected', function () {
    $pending = PendingAnimalChanges::create([
        'action' => PendingChanges::Update, 'status' => PendingApprobationStatus::Pending,
        'animal_id' => $this->animal->id, 'user_id' => $this->volunteer->id, 'payload' => ['name' => 'x'],
    ]);
    PendingAnimalChanges::create([
        'action' => PendingChanges::Update, 'status' => PendingApprobationStatus::Approved,
        'animal_id' => $this->animal->id, 'user_id' => $this->volunteer->id, 'payload' => ['name' => 'x'],
    ]);
    PendingAnimalChanges::create([
        'action' => PendingChanges::Update, 'status' => PendingApprobationStatus::Rejected,
        'animal_id' => $this->animal->id, 'user_id' => $this->volunteer->id, 'payload' => ['name' => 'x'],
    ]);

    $items = AdminAttentionFeed::items();

    expect($items)->toHaveCount(1)
        ->and($items->first()->id)->toBe($pending->id);
});

test('unreadMessageCount() counts only contact messages without read_at', function () {
    ContactMessage::create([
        'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com',
        'type' => ContactMessageType::Contact, 'content' => 'hi',
    ]);
    ContactMessage::create([
        'first_name' => 'A', 'last_name' => 'B', 'email' => 'a@example.com',
        'type' => ContactMessageType::Contact, 'content' => 'hi', 'read_at' => now(),
    ]);

    expect(AdminAttentionFeed::unreadMessageCount())->toBe(1);
});

test('volunteer gets an empty needs-attention widget on the dashboard', function () {
    AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Unattended,
    ]);

    $response = $this->actingAs($this->volunteer)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('needsAttention', [])
        ->where('unreadMessageCount', 0)
    );
});

test('admin sees the needs-attention item on the dashboard', function () {
    AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Unattended,
    ]);

    $response = $this->actingAs($this->admin)->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('needsAttention', 1)
    );
});

test('volunteer cannot view the notifications feed', function () {
    $this->actingAs($this->volunteer)
        ->get(route('notifications.index'))
        ->assertForbidden();
});

test('resolving an adoption request removes it from the feed', function () {
    $request = AdoptionRequest::create([
        'animal_id' => $this->animal->id, 'adopter_profile_id' => $this->adopterProfile->id,
        'content' => 'x', 'status' => PendingApprobationStatus::Unattended,
    ]);

    expect(AdminAttentionFeed::items())->toHaveCount(1);

    $this->actingAs($this->admin)->patch(route('adoption-requests.update-status', $request), [
        'status' => PendingApprobationStatus::Approved->value,
    ]);

    expect(AdminAttentionFeed::items())->toHaveCount(0);
});
