<?php

namespace App\Http\Controllers;

use App\Enums\Animals\Status;
use App\Enums\PendingApprobationStatus;
use App\Http\Resources\AdoptionRequestResource;
use App\Mail\AdoptionRequestReplyMail;
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
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AdoptionRequestController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', AdoptionRequest::class);

        $adoptionRequests = AdoptionRequest::with(['animal', 'adopterProfile'])
            ->latest()
            ->get();

        return Inertia::render('adoption-requests/index', [
            'adoptionRequests' => AdoptionRequestResource::collection($adoptionRequests)->toArray($request),
            'animals' => Animal::excludingDeceased()->select('id', 'name')->orderBy('name')->get(),
            'defaultSignature' => $request->user()->defaultSignature(),
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

        $this->applyStatusUpdateEffects($adoptionRequest, $newStatus);

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

    /**
     * Manual entry for an AdopterProfile.
     * Only in case an adopter contacts trough another way than the website.
     */
    public function storeManual(Request $request)
    {
        Gate::authorize('create', AdoptionRequest::class);

        $validated = $request->validate([
            'animal_id' => 'required|exists:animals,id',
            'first_name' => 'required|string|min:2|max:255',
            'last_name' => 'required|string|min:2|max:255',
            'email' => 'nullable|email|unique:adopter_profiles,email|required_without:other_contact',
            'other_contact' => 'nullable|string|required_without:email',
            'content' => 'required|string',
        ]);

        $adopterProfile = isset($validated['email'])
            ? AdopterProfile::firstOrCreate(
                ['email' => $validated['email']],
                [
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'other_contact' => $validated['other_contact'] ?? null,
                ]
            )
            : AdopterProfile::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'other_contact' => $validated['other_contact'],
            ]);

        AdoptionRequest::create([
            'animal_id' => $validated['animal_id'],
            'adopter_profile_id' => $adopterProfile->id,
            'content' => $validated['content'],
            'status' => PendingApprobationStatus::Unattended,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, AdoptionRequest $adoptionRequest)
    {
        Gate::authorize('update', AdoptionRequest::class);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $adoptionRequest->update($validated);

        return redirect()->back();
    }

    public function reply(Request $request, AdoptionRequest $adoptionRequest)
    {
        Gate::authorize('reply', AdoptionRequest::class);

        $validated = $request->validate([
            'message' => 'required|string',
            'signature' => 'required|string',
            'outcome' => ['required', Rule::in(['positive', 'negative'])],
        ]);

        // In the rare case the adopter has no email
        if ($adoptionRequest->adopterProfile->email === null) {
            throw ValidationException::withMessages([
                'message' => [__('validation.custom.adoption_request.no_email_on_file')],
            ]);
        }

        Mail::to($adoptionRequest->adopterProfile->email)->queue(
            new AdoptionRequestReplyMail($adoptionRequest, $validated['message'], $validated['signature'])
        );

        // Only a positive reply implies the animal is being actively considered.
        // A negative reply automatically rejects the request.
        $newStatus = $validated['outcome'] === 'positive'
            ? PendingApprobationStatus::Pending
            : PendingApprobationStatus::Rejected;

        $adoptionRequest->update(['status' => $newStatus]);

        $this->applyStatusUpdateEffects($adoptionRequest, $newStatus);

        return redirect()->back();
    }

    private function applyStatusUpdateEffects(AdoptionRequest $adoptionRequest, PendingApprobationStatus $newStatus): void
    {
        if ($newStatus === PendingApprobationStatus::Pending) {
            $this->putAnimalOnHold($adoptionRequest);
        } elseif ($newStatus === PendingApprobationStatus::Approved) {
            $adoptionRequest->update(['accepted_at' => Carbon::now()]);

            // "Approved" here means the paperwork is done IRL — the animal has left the shelter.
            AnimalWriter::update($adoptionRequest->animal, [
                'animal_status_id' => AnimalStatus::where('name', Status::Adopted->value)->value('id'),
            ], null);
        } elseif ($newStatus === PendingApprobationStatus::Rejected) {
            $adoptionRequest->animal->availableIfUnrequested();
        }
    }

    private function putAnimalOnHold(AdoptionRequest $adoptionRequest): void
    {
        $adoptionRequest->animal->update([
            'animal_status_id' => AnimalStatus::where('name', Status::Pending->value)->value('id'),
        ]);
    }
}
