<?php

namespace App\Http\Controllers;

use App\Enums\PendingApprobationStatus;
use App\Enums\PendingChanges;
use App\Models\Animal;
use App\Models\PendingAnimalChanges;
use App\Services\AnimalWriter;
use Gate;

class AnimalChangeController extends Controller
{
    public function acceptChange(PendingAnimalChanges $pendingAnimalChange)
    {
        Gate::authorize('review', Animal::class);

        $payload = collect($pendingAnimalChange->payload);
        // Whitelist to exactly the keys AnimalWriter::syncVaccines() reads, regardless
        // of what else ended up serialized into the payload's JSON at suggestion time.
        $vaccines = collect($payload->get('vaccines', []))
            ->map(fn ($vaccine) => [
                'vaccine_id' => $vaccine['vaccine_id'],
                'vaccinated_at' => $vaccine['vaccinated_at'],
            ])->all();
        $recoveredAt = $payload->get('recovered_at');
        $attributes = $payload->except(['vaccines', 'recovered_at'])->all();

        match ($pendingAnimalChange->action) {
            PendingChanges::Store => AnimalWriter::create(new Animal($attributes), $vaccines, $recoveredAt),
            PendingChanges::Update => AnimalWriter::update($pendingAnimalChange->animal, $attributes, $vaccines),
            PendingChanges::Delete => $pendingAnimalChange->animal?->delete(),
        };

        $pendingAnimalChange->update(['status' => PendingApprobationStatus::Approved]);

        return redirect()->back();
    }

    public function denyChange(PendingAnimalChanges $pendingAnimalChange)
    {
        Gate::authorize('review', Animal::class);

        $pendingAnimalChange->update(['status' => PendingApprobationStatus::Rejected]);

        return redirect()->back();
    }
}
