<?php

namespace App\Http\Controllers;

use App\Enums\ContactMessageType;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContactMessageController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|email',
            'type' => ['required', Rule::enum(ContactMessageType::class)],
            'message' => 'required|string',
        ]);

        ContactMessage::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'content' => $validated['message'],
        ]);

        // TODO email the admins directly (README: "Notifications par email (admin et adoptant)") —
        // no Mail infrastructure exists yet, deferred separately from the in-app attention feed.

        return redirect()
            ->back()
            ->with('status', 'message-sent');
    }
}
