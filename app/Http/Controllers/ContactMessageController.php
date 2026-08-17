<?php

namespace App\Http\Controllers;

use App\Enums\ContactMessageStatus;
use App\Enums\ContactMessageType;
use App\Enums\NotificationPreference;
use App\Http\Resources\ContactMessageResource;
use App\Mail\ContactMessageReplyMail;
use App\Mail\NewContactMessageMail;
use App\Models\ContactMessage;
use App\Models\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', ContactMessage::class);

        $contactMessages = ContactMessage::latest()->get();

        return Inertia::render('contact-messages/index', [
            'contactMessages' => ContactMessageResource::collection($contactMessages)->toArray($request),
            'defaultSignature' => $request->user()->defaultSignature(),
        ]);
    }

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
        $adminsToNotify = User::admins()->toNotify(NotificationPreference::ContactMessages)->get();

        if ($adminsToNotify->isNotEmpty()) {
            Mail::to($adminsToNotify)->queue(new NewContactMessageMail($contactMessage));
        }

        return redirect()
            ->back()
            ->with('status', 'message-sent');
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        Gate::authorize('reply', ContactMessage::class);

        $validated = $request->validate([
            'message' => 'required|string',
            'signature' => 'required|string',
        ]);

        Mail::to($contactMessage->email)->queue(
            new ContactMessageReplyMail($contactMessage, $validated['message'], $validated['signature'])
        );

        $contactMessage->update([
            'status' => ContactMessageStatus::Answered,
            'read_at' => $contactMessage->read_at ?? now(),
        ]);

        return redirect()->back();
    }

    public function markIgnored(ContactMessage $contactMessage)
    {
        Gate::authorize('markIgnored', ContactMessage::class);

        $contactMessage->update([
            'status' => ContactMessageStatus::Ignored,
            'read_at' => $contactMessage->read_at ?? now(),
        ]);

        return redirect()->back();
    }
}
