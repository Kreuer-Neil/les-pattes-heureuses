<?php

namespace App\Http\Controllers;

use App\Models\AdopterProfile;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdopterProfileController extends Controller
{
    public function update(Request $request, AdopterProfile $adopterProfile)
    {
        Gate::authorize('update', $adopterProfile);

        $validated = $request->validate([
            'details' => 'nullable|string|min:5',
            'first_name' => 'sometimes|string|min:2|max:255',
            'last_name' => 'sometimes|string|min:2|max:255',
            // Rule with ignore so that AdopterProfile with this ID is excluded from this unique rule
            'email' => ['sometimes', 'nullable', 'email', Rule::unique('adopter_profiles', 'email')->ignore($adopterProfile->id)],
            'other_contact' => 'sometimes|nullable|string',
        ]);

        $adopterProfile->update($validated);

        return redirect()->back();
    }
}
