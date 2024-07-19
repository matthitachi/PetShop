<?php

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'payment_id' => Payment::factory()->create(),
            'order_status_id' => OrderStatus::factory()->create(),
            'address' => json_encode(
                [
                    'billing' => fake()->address(),
                    'shipping' => fake()->address(),
                ]
            ),
            'products' => json_encode(
                Product::inRandomOrder()->skip(rand(1, 20))
                    ->take(rand(1, 20))
                    ->get()->map(function ($product) {
                        return [
                            'product' => $product->uuid,
                            'quantity' => rand(1, 10),
                        ];
                    })->toArray()
            ),
            'delivery_fee' => fake()->randomFloat(2, 2, 50),
            'amount' => fake()->randomFloat(2, 10, 5000),
        ];
    }
}
