<?php

namespace App\Listeners;

use App\Events\RentalContractDeleted;
use Illuminate\Events\Attributes\ListensTo;

class UpdateVehicleAvailabilityOnContractDeleted
{
    #[ListensTo(RentalContractDeleted::class)]
    public function handle(RentalContractDeleted $event): void
    {
        $vehicle = $event->contract?->vehicle;

        // Re-evaluate vehicle availability after contract deletion
        $vehicle?->updateAvailabilityStatus();
    }
}
