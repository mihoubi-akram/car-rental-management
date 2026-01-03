<?php

namespace App\Listeners;

use App\Events\RentalContractUpdated;
use App\Models\Vehicle;

class UpdateVehicleAvailabilityOnContractUpdated
{
    public function handle(RentalContractUpdated $event): void
    {
        $contract = $event->contract;
        $originalData = $event->originalData;

        // If vehicle changed, update both old and new vehicle status
        if (isset($originalData['vehicle_id']) && $originalData['vehicle_id'] !== $contract->vehicle_id) {
            // Update old vehicle status
            $oldVehicle = Vehicle::find($originalData['vehicle_id']);
            if ($oldVehicle) {
                $oldVehicle->updateAvailabilityStatus();
            }

            // Update new vehicle status
            $contract->vehicle->updateAvailabilityStatus();
        } else {
            // Just update current vehicle status
            $contract->vehicle->updateAvailabilityStatus();
        }
    }
}
