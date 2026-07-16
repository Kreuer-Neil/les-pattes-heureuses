<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Animal;

class AdoptionController extends Controller
{
    public function index()
    {
        $animals = Animal::available()
            ->with(['specie', 'breed'])
            ->orderBy('name')
            ->get();

        return view('client.adoption', compact('animals'));
    }

    public function show(Animal $animal)
    {
        // TODO replace with an AnimalPolicy (view) once we introduce policies for public visibility rules
        abort_unless(Animal::visible()->whereKey($animal->id)->exists(), 404);

        $animal->load(['specie', 'breed', 'furColor', 'secondaryFurColor', 'furSchema', 'status']);

        return view('client.animal', compact('animal'));
    }

    // TODO persist as an adoption request tied to $animal and notify the admin (see cahier des charges "Demandes d'adoption")
    public function request(Animal $animal)
    {
        return redirect()
            ->route('client.animal.show', $animal)
            ->with('status', 'adoption-request-sent');
    }
}
