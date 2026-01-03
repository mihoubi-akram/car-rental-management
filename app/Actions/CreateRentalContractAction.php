<?php

namespace App\Actions;

use App\Enums\VehicleAvailabilityStatus;
use App\Events\RentalContractCreated;
use App\Exceptions\VehicleInMaintenanceException;
use App\Exceptions\VehicleNotAvailableException;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;

class CreateRentalContractAction
{
    public function execute(array $data): RentalContract
    {
        // Calculate totals
        if (isset($data['start_date'], $data['end_date'], $data['daily_rate'])) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $data['total_days'] = $startDate->diffInDays($endDate) + 1;
            $data['total_amount'] = $data['daily_rate'] * $data['total_days'];
        }

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        // Check if vehicle is in maintenance
        if ($vehicle->availability_status === VehicleAvailabilityStatus::Maintenance) {
            throw new VehicleInMaintenanceException;
        }

        // Check vehicle availability for dates
        if (! $vehicle->isAvailableForDates($startDate, $endDate)) {
            throw new VehicleNotAvailableException;
        }

        // Create the contract
        $contract = RentalContract::create($data);

        // Dispatch event for side effects (status update, etc.)
        event(new RentalContractCreated($contract));

        return $contract;
    }
}
