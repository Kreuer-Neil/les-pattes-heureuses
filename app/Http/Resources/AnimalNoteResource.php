<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalNoteResource extends JsonResource
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
            'title' => $this->resource->title,
            'text' => $this->resource->text,
            'authorId' => $this->resource->user_id,
            'authorName' => $this->resource->user?->name,
            'createdAt' => $this->resource->created_at,
        ];
    }
}
