<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = ['credit_card', 'cash_on_delivery', 'bank_transfer'][rand(0, 2)];

        return [
            'uuid' => Str::uuid(),
            'type' => $type,
            'details' => json_encode($this->getPaymentDetails($type)),
        ];
    }

    private function getPaymentDetails(string $type)
    {
        if ($type == 'credit_card') {
            return [
                'holder_name' => fake()->firstName() . ' ' . fake()->lastName(),
                'number' => fake()->creditCardNumber(),
                'ccv' => fake()->randomNumber(3),
                'expire_date' => fake()->creditCardExpirationDate(),
            ];
        } elseif ($type == 'cash_on_delivery') {
            return [
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'address' => fake()->address(),
            ];
        } elseif ($type == 'bank_transfer') {
            return [
                'swift' => fake()->swiftBicNumber(),
                'iban' => fake()->iban(),
                'name' => fake()->name(),
            ];
        }
    }
}
