<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->resource->uuid,
            'products' => $this->resource->products,
            'address' => $this->resource->address,
            'delivery_fee' => $this->resource->delivery_fee,
            'amount' => $this->resource->amount,
            'user' => new UserResource($this->resource->user),
            'order_status' => new OrderStatusResource($this->resource->orderStatus),
            'payment' => new PaymentResource($this->resource->payment),
            'shipped_at' => $this->resource->shipped_at,
            'created_at' => $this->resource->created_at,
            'updated_at' => $this->resource->updated_at,
        ];
    }
}
