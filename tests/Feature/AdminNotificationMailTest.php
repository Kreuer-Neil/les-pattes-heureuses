<?php

use App\Enums\Animals\Status;
use App\Enums\ContactMessageType;
use App\Enums\Roles;
use App\Mail\NewAdoptionRequestMail;
use App\Mail\NewContactMessageMail;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);
});

test('submitting an adoption request queues a notification mail to admins', function () {
    Mail::fake();

    $animal = Animal::factory()->create([
        'animal_status_id' => AnimalStatus::where('name', Status::Available->value)->value('id'),
    ]);

    $this->post(route('client.adoption.request', $animal), [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean@example.com',
        'message' => 'Interested!',
    ])->assertRedirect();

    Mail::assertQueued(
        NewAdoptionRequestMail::class,
        fn ($mail) => $mail->hasTo($this->admin->email)
            && $mail->adoptionRequest->animal->is($animal),
    );
});

test('submitting a contact message queues a notification mail to admins', function () {
    Mail::fake();

    $this->post(route('client.contact'), [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean@example.com',
        'type' => ContactMessageType::Contact->value,
        'message' => 'Hello!',
    ])->assertRedirect();

    Mail::assertQueued(
        NewContactMessageMail::class,
        fn ($mail) => $mail->hasTo($this->admin->email)
            && $mail->contactMessage->email === 'jean@example.com',
    );
});
