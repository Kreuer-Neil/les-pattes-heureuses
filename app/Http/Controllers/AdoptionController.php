<?php

namespace App\Http\Controllers;

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
        // TODO replace with a ClientAnimalPolicy (view)
        abort_unless(Animal::visible()->whereKey($animal->id)->exists(), 404);

        $animal->load(['specie', 'breed', 'furColor', 'secondaryFurColor', 'furSchema', 'status']);

        return view('client.animal', compact('animal'));
    }
}
