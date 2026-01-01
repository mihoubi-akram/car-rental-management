<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'is_blacklisted' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function blacklisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_blacklisted' => true,
            'notes' => 'Client blacklisted due to '.fake()->randomElement([
                'unpaid damages',
                'repeated late returns',
                'fraudulent activity',
                'vehicle misuse',
            ]),
        ]);
    }
}
