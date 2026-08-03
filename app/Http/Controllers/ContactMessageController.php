<?php

namespace App\Http\Controllers;

use App\Enums\ContactMessageType;
use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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

        $contactMessage = ContactMessage::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'content' => $validated['message'],
        ]);

        // Safe to queue (unlike VolunteerPasswordMail) to avoid problems with the hosting (since it's the admin subdomain)
        Mail::to(User::admins()->get())->queue(new NewContactMessageMail($contactMessage));

        return redirect()
            ->back()
            ->with('status', 'message-sent');
    }
}
