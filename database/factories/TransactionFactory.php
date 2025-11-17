<?php

namespace Database\Factories;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => \App\Models\Client::factory(),
            'gateway_id' => null,
            'external_id' => fake()->uuid(),
            'status' => TransactionStatus::SUCCESS,
            'amount' => fake()->numberBetween(1000, 100000),
            'card_last_numbers' => fake()->numerify('####'),
            'gateway_response' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::PENDING,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::FAILED,
        ]);
    }

    public function refunded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TransactionStatus::REFUNDED,
        ]);
    }
}

