<?php

namespace App\Listeners;

use App\Events\RentalContractUpdated;
use App\Models\Vehicle;
use Illuminate\Events\Attributes\ListensTo;

class UpdateVehicleAvailabilityOnContractUpdated
{
    #[ListensTo(RentalContractUpdated::class)]
    public function handle(RentalContractUpdated $event): void
    {
        $contract = $event->contract;
        $originalData = $event->originalData;

        // If vehicle changed, update both old and new vehicle status
        if (isset($originalData['vehicle_id']) && $originalData['vehicle_id'] !== $contract->vehicle_id) {
            $oldVehicle = Vehicle::find($originalData['vehicle_id']);
            $oldVehicle?->updateAvailabilityStatus();

            $contract->vehicle?->updateAvailabilityStatus();
        } else {
            $contract->vehicle?->updateAvailabilityStatus();
        }
    }
}
