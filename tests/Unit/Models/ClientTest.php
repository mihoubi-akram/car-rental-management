<?php

namespace Tests\Unit\Models;

use App\Models\Client;
use App\Models\RentalContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_has_many_rental_contracts(): void
    {
        $client = Client::factory()->create();
        RentalContract::factory(3)->for($client)->create();

        $this->assertCount(3, $client->rentalContracts);
    }

    public function test_get_full_name_attribute_returns_full_name(): void
    {
        $client = Client::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $client->full_name);
    }

    public function test_can_rent_returns_true_when_not_blacklisted(): void
    {
        $client = Client::factory()->create(['is_blacklisted' => false]);

        $this->assertTrue($client->canRent());
    }

    public function test_can_rent_returns_false_when_blacklisted(): void
    {
        $client = Client::factory()->blacklisted()->create();

        $this->assertFalse($client->canRent());
    }

    public function test_has_active_rentals_returns_true_when_active(): void
    {
        $client = Client::factory()->create();
        RentalContract::factory()->active()->for($client)->create();

        $this->assertTrue($client->fresh()->hasActiveRentals());
    }

    public function test_has_active_rentals_returns_false_when_no_active(): void
    {
        $client = Client::factory()->create();

        $this->assertFalse($client->hasActiveRentals());
    }
}
