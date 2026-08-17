<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdopterProfileResource extends JsonResource
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
            'otherContact' => $this->resource->other_contact,
            'details' => $this->resource->details,
        ];
    }
}
