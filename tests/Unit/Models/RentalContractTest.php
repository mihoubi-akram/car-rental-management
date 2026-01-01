<?php

namespace Tests\Unit\Models;

use App\Enums\RentalContractStatus;
use App\Models\Client;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_rental_contract_belongs_to_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create();
        $contract = RentalContract::factory()->for($vehicle)->create();

        $this->assertInstanceOf(Vehicle::class, $contract->vehicle);
        $this->assertEquals($vehicle->id, $contract->vehicle->id);
    }

    public function test_rental_contract_belongs_to_client(): void
    {
        $client = Client::factory()->create();
        $contract = RentalContract::factory()->for($client)->create();

        $this->assertInstanceOf(Client::class, $contract->client);
        $this->assertEquals($client->id, $contract->client->id);
    }

    public function test_calculate_totals_computes_correct_amounts(): void
    {
        $contract = RentalContract::factory()->make([
            'start_date' => Carbon::parse('2026-01-10'),
            'end_date' => Carbon::parse('2026-01-15'),
            'daily_rate' => 100.00,
        ]);

        $contract->calculateTotals();

        $this->assertEquals(6, $contract->total_days);
        $this->assertEquals(600.00, $contract->total_amount);
    }

    public function test_cancel_sets_status_and_records_timestamp(): void
    {
        $contract = RentalContract::factory()->create([
            'status' => RentalContractStatus::Pending,
        ]);

        $contract->cancel();

        $this->assertEquals(RentalContractStatus::Cancelled, $contract->fresh()->status);
        $this->assertNotNull($contract->fresh()->cancelled_at);
    }

    public function test_complete_sets_status_to_completed(): void
    {
        $contract = RentalContract::factory()->create([
            'status' => RentalContractStatus::Active,
        ]);

        $contract->complete();

        $this->assertEquals(RentalContractStatus::Completed, $contract->fresh()->status);
    }

    public function test_can_be_modified_returns_true_for_pending_status(): void
    {
        $contract = RentalContract::factory()->create([
            'status' => RentalContractStatus::Pending,
        ]);

        $this->assertTrue($contract->canBeModified());
    }

    public function test_can_be_modified_returns_false_for_active_status(): void
    {
        $contract = RentalContract::factory()->create([
            'status' => RentalContractStatus::Active,
        ]);

        $this->assertFalse($contract->canBeModified());
    }
}
