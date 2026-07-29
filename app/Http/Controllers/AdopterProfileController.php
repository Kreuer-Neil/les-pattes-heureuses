<?php

namespace App\Http\Controllers;

use App\Models\AdopterProfile;
use Gate;
use Illuminate\Http\Request;

class AdopterProfileController extends Controller
{
    public function update(Request $request, AdopterProfile $adopterProfile)
    {
        Gate::authorize('update', $adopterProfile);

        $validated = $request->validate([
            'details' => 'nullable|string|min:5',
        ]);

        $adopterProfile->update($validated);

        return redirect()->back();
    }
}
