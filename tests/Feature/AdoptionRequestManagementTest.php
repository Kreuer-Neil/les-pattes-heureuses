<?php

use App\Enums\Animals\MovementType;
use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use App\Enums\Roles;
use App\Mail\AdoptionRequestReplyMail;
use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Mail;

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

test('accepting a request sets the animal status to adopted and records a departure movement', function () {
    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.update-status', $this->adoptionRequest), [
            'status' => PendingApprobationStatus::Approved->value,
        ])
        ->assertRedirect();

    $animal = $this->animal->fresh();

    expect($animal->status->name)->toBe(Status::Adopted->value)
        ->and($animal->movements()->where('type', MovementType::AdoptedDeparture->value)->count())->toBe(1);
});

// Refusing should check if another adoptionRequest on this animal is pending and set animal status to available if none is.
test('rejecting a request does not touch the animal status', function () {
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

test('admin can update an adopter profile\'s details', function () {
    $this->actingAs($this->admin)
        ->patch(route('adopter-profile.update', $this->adopterProfile), [
            'details' => 'Has a large fenced garden.',
        ])
        ->assertRedirect();

    expect($this->adopterProfile->fresh()->details)->toBe('Has a large fenced garden.');
});

test('volunteer cannot update an adopter profile\'s details', function () {
    $this->actingAs($this->volunteer)
        ->patch(route('adopter-profile.update', $this->adopterProfile), [
            'details' => 'Has a large fenced garden.',
        ])
        ->assertForbidden();

    expect($this->adopterProfile->fresh()->details)->toBeNull();
});

test('admin can manually log an adoption request with only an email', function () {
    $this->actingAs($this->admin)
        ->post(route('adoption-requests.store'), [
            'animal_id' => $this->animal->id,
            'first_name' => 'Marc',
            'last_name' => 'Petit',
            'email' => 'marc@example.com',
            'content' => 'Called about Moka.',
        ])
        ->assertRedirect();

    expect(AdoptionRequest::whereHas('adopterProfile', fn ($q) => $q->where('email', 'marc@example.com'))->exists())->toBeTrue();
});

test('admin can manually log an adoption request with only another contact method', function () {
    $this->actingAs($this->admin)
        ->post(route('adoption-requests.store'), [
            'animal_id' => $this->animal->id,
            'first_name' => 'Marc',
            'last_name' => 'Petit',
            'other_contact' => '06 12 34 56 78',
            'content' => 'Called about Moka.',
        ])
        ->assertRedirect();

    expect(AdopterProfile::where('other_contact', '06 12 34 56 78')->exists())->toBeTrue();
});

test('manually logging an adoption request fails without an email or another contact method', function () {
    $this->actingAs($this->admin)
        ->post(route('adoption-requests.store'), [
            'animal_id' => $this->animal->id,
            'first_name' => 'Marc',
            'last_name' => 'Petit',
            'content' => 'Called about Moka.',
        ])
        ->assertSessionHasErrors(['email', 'other_contact']);
});

test('volunteer cannot manually log an adoption request', function () {
    $this->actingAs($this->volunteer)
        ->post(route('adoption-requests.store'), [
            'animal_id' => $this->animal->id,
            'first_name' => 'Marc',
            'last_name' => 'Petit',
            'email' => 'marc@example.com',
            'content' => 'Called about Moka.',
        ])
        ->assertForbidden();
});

test('a positive reply queues the reply mail and puts the animal on hold', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.reply', $this->adoptionRequest), [
            'message' => 'We would love to meet you!',
            'signature' => 'Élise, administratrice',
            'outcome' => 'positive',
        ])
        ->assertRedirect();

    expect($this->adoptionRequest->fresh()->status)->toBe(PendingApprobationStatus::Pending)
        ->and($this->animal->fresh()->status->name)->toBe(Status::Pending->value);

    Mail::assertQueued(
        AdoptionRequestReplyMail::class,
        fn ($mail) => $mail->hasTo('sarah@example.com') && $mail->message === 'We would love to meet you!',
    );
});

test('a negative reply rejects the request without touching the animal status', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.reply', $this->adoptionRequest), [
            'message' => 'Unfortunately Moka has already been adopted.',
            'signature' => 'Élise, administratrice',
            'outcome' => 'negative',
        ])
        ->assertRedirect();

    expect($this->adoptionRequest->fresh()->status)->toBe(PendingApprobationStatus::Rejected)
        ->and($this->animal->fresh()->status->name)->toBe(Status::Available->value);
});

test('replying requires an explicit outcome with no default', function () {
    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.reply', $this->adoptionRequest), [
            'message' => 'Hello!',
            'signature' => 'Élise, administratrice',
        ])
        ->assertSessionHasErrors('outcome');
});

test('cannot reply to a request whose adopter has no email on file', function () {
    $phoneOnlyProfile = AdopterProfile::create([
        'first_name' => 'Marc',
        'last_name' => 'Petit',
        'other_contact' => '06 12 34 56 78',
    ]);

    $request = AdoptionRequest::create([
        'animal_id' => $this->animal->id,
        'adopter_profile_id' => $phoneOnlyProfile->id,
        'content' => 'Called about Moka.',
        'status' => PendingApprobationStatus::Unattended,
    ]);

    $this->actingAs($this->admin)
        ->patch(route('adoption-requests.reply', $request), [
            'message' => 'Hello!',
            'signature' => 'Élise, administratrice',
            'outcome' => 'positive',
        ])
        ->assertSessionHasErrors('message');
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
