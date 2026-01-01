<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = [
            'Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes-Benz',
            'Audi', 'Volkswagen', 'Nissan', 'Hyundai', 'Kia',
            'Mazda', 'Subaru', 'Jeep', 'Chevrolet', 'Peugeot',
            'Renault', 'Citroën', 'Volvo', 'Tesla', 'Porsche',
        ];

        return [
            'name' => fake()->unique()->randomElement($brands),
            'logo_url' => null,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
