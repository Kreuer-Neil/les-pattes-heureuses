<?php

namespace App\Http\Controllers;

use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use App\Http\Resources\AdoptionRequestResource;
use App\Mail\NewAdoptionRequestMail;
use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\AnimalStatus;
use App\Models\User;
use App\Services\AnimalWriter;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AdoptionRequestController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AdoptionRequest::class);

        // Merge with general admin notifications view? Using notification type filter to easily validate requests
        // (For animal create/edits, add the user's profile picture and name, then a resume of the changes maybe?
        // Knowing who suggested the changes already makes it worth automatically validating.

        $adoptionRequests = AdoptionRequest::with(['animal', 'adopterProfile'])
            ->latest()
            ->get();

        return Inertia::render('adoption-requests/index', [
            'adoptionRequests' => AdoptionRequestResource::collection($adoptionRequests)->toArray($request),
        ]);
    }

    public function updateStatus(Request $request, AdoptionRequest $adoptionRequest)
    {
        Gate::authorize('updateStatus', AdoptionRequest::class);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(PendingApprobationStatus::class)],
        ]);

        $newStatus = PendingApprobationStatus::from($validated['status']);

        $adoptionRequest->update(['status' => $newStatus]);

        // Contacting the adopter also puts the animal on hold — it's no longer just
        // "available", someone is actively being considered for it.
        if ($newStatus === PendingApprobationStatus::Pending) {
            $adoptionRequest->animal->update([
                'animal_status_id' => AnimalStatus::where('name', Status::Pending->value)->value('id'),
            ]);
        } elseif ($newStatus === PendingApprobationStatus::Approved) {
            $adoptionRequest->update(['accepted_at' => Carbon::now()]);

            // "Approved" here means the paperwork is done IRL — the animal has left the shelter.
            AnimalWriter::update($adoptionRequest->animal, [
                'animal_status_id' => AnimalStatus::where('name', Status::Adopted->value)->value('id'),
            ], null);
        }

        return redirect()->back();
    }

    public function store(Request $request, Animal $animal)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        $adopterProfile = AdopterProfile::firstOrCreate(
            ['email' => $validated['email']],
            ['first_name' => $validated['first_name'], 'last_name' => $validated['last_name']]
        );

        $adoptionRequest = AdoptionRequest::create([
            'animal_id' => $animal->id,
            'adopter_profile_id' => $adopterProfile->id,
            'content' => $validated['message'],
            'status' => PendingApprobationStatus::Unattended,
        ]);

        // Safe to queue (unlike VolunteerPasswordMail) to avoid problems with the hosting (since it's the admin subdomain)
        Mail::to(User::admins()->get())->queue(new NewAdoptionRequestMail($adoptionRequest));

        return redirect()
            ->route('client.animal.show', $animal)
            ->with('status', 'adoption-request-sent');
    }
}
