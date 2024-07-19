<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderStatus>
 */
class OrderStatusFactory extends Factory
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
            'title' => [
                'Pending',
                'Awaiting payment',
                'Paid', 'Awaiting shipment',
                'Out for delivery',
                'Delivered',
                'Refunded',
            ][rand(0, 6)],
        ];
    }
}
