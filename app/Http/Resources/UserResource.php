<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->resource->email,
            'avatar' => $this->resource->avatar,
            'role' => $this->resource->role,
            'userRoleId' => $this->resource->user_role_id,
            'mustChangePassword' => $this->resource->must_change_password,
            'createdAt' => $this->resource->created_at,
        ];
    }
}
