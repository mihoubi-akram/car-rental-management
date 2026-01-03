<?php

namespace App\Listeners;

use App\Events\RentalContractDeleted;

class UpdateVehicleAvailabilityOnContractDeleted
{
    public function handle(RentalContractDeleted $event): void
    {
        $vehicle = $event->contract->vehicle;

        // Re-evaluate vehicle availability after contract deletion
        $vehicle->updateAvailabilityStatus();
    }
}
