<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PendingAnimalChangeResource extends JsonResource
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
            'action' => $this->resource->action->value,
            'animalName' => $this->resource->animal->name ?? $this->resource->payload['name'] ?? null,
            'proposerName' => $this->resource->user?->name,
            'createdAt' => $this->resource->created_at,
        ];
    }
}
