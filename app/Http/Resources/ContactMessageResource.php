<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactMessageResource extends JsonResource
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
            'firstName' => $this->resource->first_name,
            'lastName' => $this->resource->last_name,
            'email' => $this->resource->email,
            'type' => $this->resource->type,
            'content' => $this->resource->content,
            'status' => $this->resource->status,
            'readAt' => $this->resource->read_at,
            'createdAt' => $this->resource->created_at,
        ];
    }
}
