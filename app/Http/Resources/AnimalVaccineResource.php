<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalVaccineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->pivot->id,
            'vaccineType' => [
                'id' => $this->resource->id,
                'name' => $this->resource->name,
            ],
            'vaccinatedAt' => $this->resource->pivot->vaccinated_at,
        ];
    }
}
