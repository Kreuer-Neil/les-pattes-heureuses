<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class AnimalResource extends AnimalMiniatureResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [

            'bornAt' => [
                'toString' => $this->resource->born_at->translatedFormat('j F Y'),
                'value' => $this->resource->born_at,
            ],

            'vaccines' => AnimalVaccineResource::collection($this->resource->vaccines)->toArray($request),
            'hasActiveAdoptionRequest' => $this->resource->hasActiveAdoptionRequest(),
            'notes' => AnimalNoteResource::collection($this->resource->notes()->latest()->get())->toArray($request),
        ]);
    }
}
