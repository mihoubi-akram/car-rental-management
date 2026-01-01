<?php

namespace Tests\Unit\Models;

use App\Enums\RentalContractStatus;
use App\Models\Brand;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_belongs_to_brand(): void
    {
        $brand = Brand::factory()->create();
        $vehicle = Vehicle::factory()->for($brand)->create();

        $this->assertInstanceOf(Brand::class, $vehicle->brand);
        $this->assertEquals($brand->id, $vehicle->brand->id);
    }

    public function test_vehicle_has_many_rental_contracts(): void
    {
        $vehicle = Vehicle::factory()->create();
        RentalContract::factory(3)->for($vehicle)->create();

        $this->assertCount(3, $vehicle->rentalContracts);
    }

    public function test_is_available_for_dates_returns_true_when_no_conflicts(): void
    {
        $vehicle = Vehicle::factory()->available()->create();

        $this->assertTrue($vehicle->isAvailableForDates(
            Carbon::now()->addDays(10),
            Carbon::now()->addDays(15)
        ));
    }

    public function test_is_available_for_dates_returns_false_when_overlapping_active_contract(): void
    {
        $vehicle = Vehicle::factory()->create();

        RentalContract::factory()
            ->for($vehicle)
            ->create([
                'status' => RentalContractStatus::Active,
                'start_date' => Carbon::now()->addDays(5),
                'end_date' => Carbon::now()->addDays(10),
            ]);

        $this->assertFalse($vehicle->fresh()->isAvailableForDates(
            Carbon::now()->addDays(8),
            Carbon::now()->addDays(12)
        ));
    }

    public function test_is_available_for_dates_returns_false_when_in_maintenance(): void
    {
        $vehicle = Vehicle::factory()->inMaintenance()->create();

        $this->assertFalse($vehicle->isAvailableForDates(
            Carbon::now()->addDays(1),
            Carbon::now()->addDays(5)
        ));
    }

    public function test_has_active_contract_returns_true_when_active(): void
    {
        $vehicle = Vehicle::factory()->create();

        RentalContract::factory()
            ->for($vehicle)
            ->create([
                'status' => RentalContractStatus::Active,
                'start_date' => Carbon::now()->subDays(2),
                'end_date' => Carbon::now()->addDays(3),
            ]);

        $this->assertTrue($vehicle->fresh()->hasActiveContract());
    }

    public function test_has_active_contract_returns_false_when_no_active(): void
    {
        $vehicle = Vehicle::factory()->create();

        $this->assertFalse($vehicle->hasActiveContract());
    }
}
