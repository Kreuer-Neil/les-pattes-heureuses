<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Str;

class AnimalMiniatureResource extends JsonResource
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
            'name' => $this->resource->name,
            'image' => $this->resource->image,
            'gender' => $this->resource->gender,
            'chip' => $this->resource->chip,

            'statusId' => $this->resource->animal_status_id,
            'specieId' => $this->resource->specie_id,
            'breedId' => $this->resource->breed_id,
            'furColorId' => $this->resource->fur_color_id,
            'secondaryFurColorId' => $this->resource->secondary_fur_color_id,
            'furPatternId' => $this->resource->fur_pattern_id,

            'personality' => Str::limit(value: $this->resource->personality, preserveWords: true),
        ];
    }
}
