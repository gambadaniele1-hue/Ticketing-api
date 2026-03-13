<?php

namespace App\Http\Resources\V1;

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
            'id'         => $this->user_id,
            'name'       => $this->name,
            'surname'    => $this->surname,
            'email'      => $this->email,
            'role'       => new RoleResource($this->whenLoaded('role')),
            'verified'   => !is_null($this->email_verified_at),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
