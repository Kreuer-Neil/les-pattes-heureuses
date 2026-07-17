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
            'id' => $this->resource->id,
            'vaccineType' => [
                'id' => $this->resource->vaccine->id,
                'name' => $this->resource->vaccine->name,
            ],
            'vaccinatedAt' => $this->resource->vaccinated_at
        ];
    }
}
