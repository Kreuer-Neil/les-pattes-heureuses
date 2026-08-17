<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Jobs\HandleImagesUploads;
use App\Services\UserWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Str;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/profile', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Update the user's profile settings.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $user = $request->user();

        // For avatar changes
        if (array_key_exists('avatar', $validated)) {
            $oldAvatar = $user->avatar !== 'default' ? $user->avatar : null;

            $imagePath = $validated['avatar']->store('images/users', 'public');
            $imageName = Str::beforeLast(Str::afterLast($imagePath, '/'), '.');

            HandleImagesUploads::dispatch($imageName, $oldAvatar, $imagePath, 'users');

            $validated['avatar'] = $imageName;
        }

        UserWriter::update($user, $validated);

        return to_route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
