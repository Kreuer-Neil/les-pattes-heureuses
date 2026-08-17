<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdoptionRequestResource extends JsonResource
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
            'status' => $this->resource->status,
            'content' => $this->resource->content,
            'createdAt' => $this->resource->created_at,

            'animal' => (new AnimalMiniatureResource($this->resource->animal))->toArray($request),

            'adopterProfile' => (new AdopterProfileResource($this->resource->adopterProfile))->toArray($request),
        ];
    }
}
