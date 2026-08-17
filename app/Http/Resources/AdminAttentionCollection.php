<?php

namespace App\Http\Resources;

use App\Models\AdoptionRequest;
use App\Models\PendingAnimalChanges;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AdminAttentionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int, array<string, mixed>>
     */
    public function toArray(Request $request): array
    {
        return $this->collection
            ->map(fn ($item) => match (true) {
                $item instanceof AdoptionRequest => array_merge(
                    ['type' => 'adoption_request'],
                    (new AdoptionRequestResource($item))->toArray($request),
                ),
                $item instanceof PendingAnimalChanges => array_merge(
                    ['type' => 'animal_change'],
                    (new PendingAnimalChangeResource($item))->toArray($request),
                ),
            })
            ->values()
            ->all();
    }
}
