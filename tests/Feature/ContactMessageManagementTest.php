<?php

use App\Enums\ContactMessageStatus;
use App\Enums\ContactMessageType;
use App\Enums\Roles;
use App\Mail\ContactMessageReplyMail;
use App\Models\ContactMessage;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->contactMessage = ContactMessage::create([
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean@example.com',
        'type' => ContactMessageType::Contact,
        'content' => 'Do you have any cats available?',
    ]);

    $this->admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();
});

test('admin can view the contact messages list', function () {
    $this->actingAs($this->admin)
        ->get(route('contact-messages.index'))
        ->assertOk();
});

test('volunteer cannot view the contact messages list', function () {
    $this->actingAs($this->volunteer)
        ->get(route('contact-messages.index'))
        ->assertForbidden();
});

test('replying sets the message status to answered and marks it read', function () {
    Mail::fake();

    $this->actingAs($this->admin)
        ->patch(route('contact-messages.reply', $this->contactMessage), [
            'message' => 'Yes, we have three cats available!',
            'signature' => 'Élise, administratrice',
        ])
        ->assertRedirect();

    $fresh = $this->contactMessage->fresh();

    expect($fresh->status)->toBe(ContactMessageStatus::Answered)
        ->and($fresh->read_at)->not->toBeNull();

    Mail::assertQueued(
        ContactMessageReplyMail::class,
        fn ($mail) => $mail->hasTo('jean@example.com') && $mail->message === 'Yes, we have three cats available!',
    );
});

test('marking a message as ignored sets its status and marks it read', function () {
    $this->actingAs($this->admin)
        ->patch(route('contact-messages.mark-ignored', $this->contactMessage))
        ->assertRedirect();

    $fresh = $this->contactMessage->fresh();

    expect($fresh->status)->toBe(ContactMessageStatus::Ignored)
        ->and($fresh->read_at)->not->toBeNull();
});

test('volunteer cannot reply to or ignore a contact message', function () {
    $this->actingAs($this->volunteer)
        ->patch(route('contact-messages.reply', $this->contactMessage), [
            'message' => 'Yes!',
            'signature' => 'Élise, administratrice',
        ])
        ->assertForbidden();

    $this->actingAs($this->volunteer)
        ->patch(route('contact-messages.mark-ignored', $this->contactMessage))
        ->assertForbidden();

    expect($this->contactMessage->fresh()->status)->toBeNull();
});
