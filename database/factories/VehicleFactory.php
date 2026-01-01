<?php

namespace Database\Factories;

use App\Enums\VehicleAvailabilityStatus;
use App\Enums\VehicleCategory;
use App\Enums\VehicleFuelType;
use App\Enums\VehicleTransmission;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'model' => fake()->word().' '.fake()->randomNumber(3),
            'year' => fake()->numberBetween(2015, 2024),
            'registration_number' => strtoupper(fake()->bothify('??-###-??')),
            'vin' => strtoupper(fake()->bothify('?????????????####')),
            'color' => fake()->safeColorName(),
            'fuel_type' => fake()->randomElement(VehicleFuelType::cases())->value,
            'transmission' => fake()->randomElement(VehicleTransmission::cases())->value,
            'seats' => fake()->numberBetween(2, 7),
            'mileage' => fake()->numberBetween(0, 150000),
            'last_maintenance_date' => fake()->optional()->dateTimeBetween('-6 months', 'now'),
            'next_maintenance_mileage' => fake()->optional()->numberBetween(160000, 200000),
            'daily_rate' => fake()->randomFloat(2, 25, 200),
            'weekly_rate' => fake()->optional()->randomFloat(2, 150, 1200),
            'category' => fake()->randomElement(VehicleCategory::cases())->value,
            'availability_status' => VehicleAvailabilityStatus::Available->value,
            'is_active' => true,
            'features' => fake()->optional()->randomElements(
                ['gps', 'ac', 'bluetooth', 'usb', 'backup_camera', 'cruise_control', 'heated_seats'],
                fake()->numberBetween(1, 5)
            ),
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_status' => VehicleAvailabilityStatus::Available->value,
        ]);
    }

    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_status' => VehicleAvailabilityStatus::Reserved->value,
        ]);
    }

    public function inMaintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'availability_status' => VehicleAvailabilityStatus::Maintenance->value,
        ]);
    }

    public function electric(): static
    {
        return $this->state(fn (array $attributes) => [
            'fuel_type' => VehicleFuelType::Electric->value,
        ]);
    }

    public function luxury(): static
    {
        return $this->state(fn (array $attributes) => [
            'category' => VehicleCategory::Luxury->value,
            'daily_rate' => fake()->randomFloat(2, 150, 500),
        ]);
    }
}
