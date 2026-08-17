<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Gate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    /**
     * Show the user's notification settings page.
     */
    public function edit(Request $request): Response
    {
        Gate::authorize('manageNotificationPreferences', $request->user());

        return Inertia::render('settings/notifications', [
            'notifyAdoptionRequests' => $request->user()->notify_adoption_requests,
            'notifyContactMessages' => $request->user()->notify_contact_messages,
        ]);
    }

    /**
     * Update the user's notification settings.
     */
    public function update(Request $request): RedirectResponse
    {
        Gate::authorize('manageNotificationPreferences', $request->user());

        $validated = $request->validate([
            'notify_adoption_requests' => 'required|boolean',
            'notify_contact_messages' => 'required|boolean',
        ]);

        $request->user()->update($validated);

        return to_route('notification-preferences.edit');
    }
}
