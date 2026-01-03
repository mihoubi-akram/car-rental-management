<?php

namespace App\Actions;

use App\Exceptions\VehicleHasRentalContractsException;
use App\Models\Vehicle;

class DeleteVehicleAction
{
    public function execute(Vehicle $vehicle): void
    {
        // Prevent deletion if vehicle has any rental contracts
        if ($vehicle->rentalContracts()->exists()) {
            throw new VehicleHasRentalContractsException;
        }

        $vehicle->delete();
    }
}
