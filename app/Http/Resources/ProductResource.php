<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'image' => new FileResource($this->getImageFile()),
            'category' => new CategoryResource($this->category),
            'brand' => new BrandResource($this->getBrand()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
