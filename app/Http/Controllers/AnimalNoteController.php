<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use App\Models\AnimalNote;
use Gate;
use Illuminate\Http\Request;

class AnimalNoteController extends Controller
{
    public function store(Request $request, Animal $animal)
    {
        Gate::authorize('create', AnimalNote::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        $animal->notes()->create([
            ...$validated,
            'user_id' => $request->user()->id,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, AnimalNote $animalNote)
    {
        Gate::authorize('update', $animalNote);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
        ]);

        $animalNote->update($validated);

        return redirect()->back();
    }

    public function destroy(AnimalNote $animalNote)
    {
        Gate::authorize('delete', $animalNote);

        $animalNote->delete();

        return redirect()->back();
    }
}
