<?php

namespace Database\Factories;

use App\Enums\RentalContractStatus;
use App\Models\Client;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalContract>
 */
class RentalContractFactory extends Factory
{
    private static $counter = 0;

    public function definition(): array
    {
        self::$counter++;

        $startDate = fake()->dateTimeBetween('-2 months', '+1 month');
        $endDate = fake()->dateTimeBetween($startDate, $startDate->format('Y-m-d').' +14 days');
        $dailyRate = fake()->randomFloat(2, 25, 200);

        $startCarbon = Carbon::parse($startDate);
        $endCarbon = Carbon::parse($endDate);
        $totalDays = $startCarbon->diffInDays($endCarbon) + 1;
        $totalAmount = $dailyRate * $totalDays;

        return [
            'contract_number' => 'RC-'.now()->year.'-'.str_pad(self::$counter, 5, '0', STR_PAD_LEFT),
            'vehicle_id' => Vehicle::factory(),
            'client_id' => Client::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'daily_rate' => $dailyRate,
            'total_days' => $totalDays,
            'total_amount' => $totalAmount,
            'status' => fake()->randomElement(RentalContractStatus::cases())->value,
            'mileage_start' => null,
            'mileage_end' => null,
            'notes' => fake()->optional()->sentence(),
            'cancelled_at' => null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RentalContractStatus::Active->value,
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(5),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RentalContractStatus::Completed->value,
            'start_date' => now()->subDays(10),
            'end_date' => now()->subDays(3),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => RentalContractStatus::Cancelled->value,
            'cancelled_at' => now()->subDays(1),
        ]);
    }

    public function withMileageTracking(): static
    {
        return $this->state(fn (array $attributes) => [
            'mileage_start' => fake()->numberBetween(10000, 100000),
            'mileage_end' => fake()->optional()->numberBetween(10100, 100500),
        ]);
    }
}
