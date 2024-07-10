<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
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
            'uuid' => $this->resource->resource->uuid,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'email' => $this->resource->email,
            'address' => $this->resource->address,
            'phone_number' => $this->resource->phone_number,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
            'token' => $this->whenNotNull($this->resource->token),
        ];
    }
}
