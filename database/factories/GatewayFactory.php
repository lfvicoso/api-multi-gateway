<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gateway>
 */
class GatewayFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'url' => fake()->url(),
            'is_active' => true,
            'priority' => fake()->numberBetween(1, 10),
            'type' => fake()->randomElement(['gateway1', 'gateway2']),
            'credentials' => [
                'token' => fake()->uuid(),
                'secret' => fake()->uuid(),
            ],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function gateway1(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'gateway1',
            'url' => env('GATEWAY_1_URL', 'http://gateway-mock:3001'),
            'credentials' => [
                'email' => 'dev@betalent.tech',
                'token' => 'FEC9BB078BF338F464F96B48089EB498',
            ],
        ]);
    }

    public function gateway2(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'gateway2',
            'url' => env('GATEWAY_2_URL', 'http://gateway-mock:3002'),
            'credentials' => [
                'token' => 'tk_f2198cc671b5289fa856',
                'secret' => '3d15e8ed6131446ea7e3456728b1211f',
            ],
        ]);
    }
}

