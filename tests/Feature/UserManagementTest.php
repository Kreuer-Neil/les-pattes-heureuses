<?php

use App\Enums\Roles;
use App\Mail\VolunteerPasswordMail;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    $this->superadmin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $this->volunteer = User::factory()->create();
});

test('admin can view the users list', function () {
    $this->actingAs($this->superadmin)
        ->get(route('users.index'))
        ->assertOk();
});

test('volunteer cannot view the users list', function () {
    $this->actingAs($this->volunteer)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('admin can create a volunteer account, which defaults to the Volunteer role and gets a temporary password by email', function () {
    Mail::fake();

    $this->actingAs($this->superadmin)
        ->post(route('users.store'), [
            'name' => 'Jean Volunteer',
            'email' => 'jean@example.com',
        ])
        ->assertRedirect();

    $user = User::where('email', 'jean@example.com')->first();
    $volunteerRoleId = UserRole::firstOrCreate(['name' => Roles::Volunteer->value])->id;

    expect($user)->not->toBeNull()
        ->and($user->must_change_password)->toBeTrue()
        ->and($user->user_role_id)->toBe($volunteerRoleId);

    Mail::assertSent(VolunteerPasswordMail::class, fn ($mail) => $mail->hasTo($user->email) && $mail->isNewAccount);
});

test('volunteer cannot create a user account', function () {
    $this->actingAs($this->volunteer)
        ->post(route('users.store'), [
            'name' => 'Jean Volunteer',
            'email' => 'jean@example.com',
        ])
        ->assertForbidden();
});

test('admin can update another user\'s name without touching credentials or changing role', function () {
    Mail::fake();

    $adminRoleId = UserRole::firstOrCreate(['name' => Roles::Admin->value])->id;
    $originalRoleId = $this->volunteer->user_role_id;

    $this->actingAs($this->superadmin)
        ->put(route('users.update', $this->volunteer), [
            'name' => 'Renamed Volunteer',
            'email' => $this->volunteer->email,
            // A stray user_role_id (e.g. a stale client) must be silently ignored.
            'user_role_id' => $adminRoleId,
        ])
        ->assertRedirect();

    $updated = $this->volunteer->fresh();

    expect($updated->name)->toBe('Renamed Volunteer')
        ->and($updated->user_role_id)->toBe($originalRoleId)
        ->and($updated->must_change_password)->toBeFalse();

    Mail::assertNothingSent();
});

test('changing a user\'s email reissues credentials', function () {
    Mail::fake();

    $this->actingAs($this->superadmin)
        ->put(route('users.update', $this->volunteer), [
            'name' => $this->volunteer->name,
            'email' => 'new-address@example.com',
        ])
        ->assertRedirect();

    $updated = $this->volunteer->fresh();

    expect($updated->email)->toBe('new-address@example.com')
        ->and($updated->must_change_password)->toBeTrue();

    Mail::assertSent(VolunteerPasswordMail::class, fn ($mail) => $mail->hasTo('new-address@example.com') && ! $mail->isNewAccount);
});

test('admin cannot edit their own account from the users page', function () {
    $this->actingAs($this->superadmin)
        ->put(route('users.update', $this->superadmin), [
            'name' => 'New Name',
            'email' => $this->superadmin->email,
        ])
        ->assertForbidden();
});

test('admin can delete a volunteer account', function () {
    $this->actingAs($this->superadmin)
        ->delete(route('users.destroy', $this->volunteer))
        ->assertRedirect();

    expect(User::find($this->volunteer->id))->toBeNull();
});

test('admin cannot delete their own account from the users page', function () {
    $this->actingAs($this->superadmin)
        ->delete(route('users.destroy', $this->superadmin))
        ->assertForbidden();

    expect(User::find($this->superadmin->id))->not->toBeNull();
});

test('the superadmin account cannot be deleted and is hidden from the list', function () {

    expect($this->superadmin->id)->toBe(1);

    $admin = User::factory()->create([
        'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Admin->value])->id,
    ]);

    $response = $this->actingAs($admin)->get(route('users.index'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('users', fn ($users) => collect($users)->doesntContain(
            fn ($user) => $user['id'] === 1,
        ))
    );

    $this->actingAs($admin)
        ->delete(route('users.destroy', $this->superadmin))
        ->assertForbidden();

    expect(User::find(1))->not->toBeNull();
});

test('a user with a temporary password is redirected to the password settings page', function () {
    $this->volunteer->update(['must_change_password' => true]);

    $this->actingAs($this->volunteer)
        ->get(route('dashboard'))
        ->assertRedirect(route('user-password.edit'));
});

test('updating the password clears the must-change-password flag and unblocks access', function () {
    $this->volunteer->update(['must_change_password' => true]);

    $this->actingAs($this->volunteer)
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'a-new-secure-password',
            'password_confirmation' => 'a-new-secure-password',
        ])
        ->assertRedirect();

    expect($this->volunteer->fresh()->must_change_password)->toBeFalse();

    $this->actingAs($this->volunteer->fresh())
        ->get(route('dashboard'))
        ->assertOk();
});
