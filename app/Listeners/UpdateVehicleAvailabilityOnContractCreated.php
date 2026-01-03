<?php

namespace App\Listeners;

use App\Enums\VehicleAvailabilityStatus;
use App\Events\RentalContractCreated;
use Illuminate\Events\Attributes\ListensTo;

class UpdateVehicleAvailabilityOnContractCreated
{
    #[ListensTo(RentalContractCreated::class)]
    public function handle(RentalContractCreated $event): void
    {
        $vehicle = $event->contract?->vehicle;

        $vehicle->update([
            'availability_status' => VehicleAvailabilityStatus::Reserved,
        ]);
    }
}
