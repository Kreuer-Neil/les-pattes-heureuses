<?php

namespace App\Services;

use App\Enums\Roles;
use App\Mail\VolunteerPasswordMail;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserWriter
{
    public static function create(array $attributes): User
    {
        $temporaryPassword = Str::password(12);

        $user = User::create([
            'user_role_id' => UserRole::firstOrCreate(['name' => Roles::Volunteer->value])->id,
            ...$attributes,
            'password' => $temporaryPassword,
            'must_change_password' => true,
        ]);

        self::sendCredentials($user, $temporaryPassword, isNewAccount: true);

        return $user;
    }

    /**
     * Fill/save the given attributes; an email change reissues credentials as a
     * side effect.
     */
    public static function update(User $user, array $attributes): User
    {
        $user->fill($attributes);
        $emailChanged = $user->isDirty('email');
        $user->save();

        if ($emailChanged) {
            self::reissueCredentials($user);
        }

        return $user;
    }

    public static function reissueCredentials(User $user): void
    {
        $temporaryPassword = Str::password(12);

        $user->update([
            'password' => $temporaryPassword,
            'must_change_password' => true,
        ]);

        self::sendCredentials($user, $temporaryPassword, isNewAccount: false);
    }

    private static function sendCredentials(User $user, string $temporaryPassword, bool $isNewAccount): void
    {
        // Sent synchronously (not queued): the mail view resolves route('login') against
        // the admin subdomain of the current request, which a queue worker wouldn't have.
        Mail::to($user)->send(new VolunteerPasswordMail($user, $temporaryPassword, $isNewAccount));
    }
}
