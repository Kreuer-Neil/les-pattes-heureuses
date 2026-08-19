<?php

use App\Enums\Roles;
use App\Models\User;
use App\Models\UserRole;

test('an admin can view and update their notification preferences', function () {
    $admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->actingAs($admin)
        ->get(route('notification-preferences.edit'))
        ->assertOk();

    $response = $this->actingAs($admin)
        ->patch(route('notification-preferences.update'), [
            'notify_adoption_requests' => false,
            'notify_contact_messages' => true,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('notification-preferences.edit'));

    $admin->refresh();

    expect($admin->notify_adoption_requests)->toBeFalse();
    expect($admin->notify_contact_messages)->toBeTrue();
});

test('a volunteer cannot access notification preferences', function () {
    $volunteer = User::factory()->create();

    $this->actingAs($volunteer)
        ->get(route('notification-preferences.edit'))
        ->assertForbidden();

    $this->actingAs($volunteer)
        ->patch(route('notification-preferences.update'), [
            'notify_adoption_requests' => false,
            'notify_contact_messages' => false,
        ])
        ->assertForbidden();
});