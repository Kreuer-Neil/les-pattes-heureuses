<?php

namespace App\Http\Controllers;

use App\Enums\PendingApprobationStatus;
use App\Models\AdopterProfile;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use Illuminate\Http\Request;

class AdoptionRequestController extends Controller
{
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

        AdoptionRequest::create([
            'animal_id' => $animal->id,
            'adopter_profile_id' => $adopterProfile->id,
            'content' => $validated['message'],
            'status' => PendingApprobationStatus::Unattended,
        ]);

        return redirect()
            ->route('client.animal.show', $animal)
            ->with('status', 'adoption-request-sent');
    }
}
