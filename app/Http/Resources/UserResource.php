<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'email_verified_at' => $this->resource->email_verified_at,
            'address' => $this->resource->address,
            'avatar' => $this->resource->avatar,
            'phone_number' => $this->resource->phone_number,
            'is_marketing' => $this->resource->is_marketing,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'last_login_at' => $this->resource->last_login_at,
            'token' => $this->whenNotNull($this->resource->token),
        ];
    }
}
