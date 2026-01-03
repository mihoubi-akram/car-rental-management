<?php

namespace App\Listeners;

use App\Enums\VehicleAvailabilityStatus;
use App\Events\RentalContractCreated;

class UpdateVehicleAvailabilityOnContractCreated
{
    public function handle(RentalContractCreated $event): void
    {
        $vehicle = $event->contract->vehicle;

        // Update vehicle status to Reserved
        $vehicle->update([
            'availability_status' => VehicleAvailabilityStatus::Reserved,
        ]);
    }
}
