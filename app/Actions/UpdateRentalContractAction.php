<?php

namespace App\Actions;

use App\Enums\VehicleAvailabilityStatus;
use App\Events\RentalContractUpdated;
use App\Exceptions\VehicleInMaintenanceException;
use App\Exceptions\VehicleNotAvailableException;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;

class UpdateRentalContractAction
{
    public function execute(RentalContract $contract, array $data): RentalContract
    {
        // Store original data for event
        $originalData = $contract->only(['vehicle_id', 'start_date', 'end_date', 'status']);

        // Recalculate totals if dates or rate changed
        if (isset($data['start_date'], $data['end_date'], $data['daily_rate'])) {
            $startDate = Carbon::parse($data['start_date']);
            $endDate = Carbon::parse($data['end_date']);
            $data['total_days'] = $startDate->diffInDays($endDate) + 1;
            $data['total_amount'] = $data['daily_rate'] * $data['total_days'];
        }

        // Check if critical fields changed
        $vehicleChanged = isset($data['vehicle_id']) && $data['vehicle_id'] !== $contract->vehicle_id;
        $datesChanged = (isset($data['start_date']) && $data['start_date'] != $contract->start_date)
                     || (isset($data['end_date']) && $data['end_date'] != $contract->end_date);

        // If vehicle or dates changed, validate again
        if ($vehicleChanged || $datesChanged) {
            $vehicleId = $data['vehicle_id'] ?? $contract->vehicle_id;
            $vehicle = Vehicle::findOrFail($vehicleId);
            $startDate = Carbon::parse($data['start_date'] ?? $contract->start_date);
            $endDate = Carbon::parse($data['end_date'] ?? $contract->end_date);

            // Check if vehicle is in maintenance
            if ($vehicle->availability_status === VehicleAvailabilityStatus::Maintenance) {
                throw new VehicleInMaintenanceException;
            }

            // Check vehicle availability (exclude current contract)
            if (! $this->isAvailableForUpdate($vehicle, $startDate, $endDate, $contract->id)) {
                throw new VehicleNotAvailableException;
            }
        }

        // Update the contract
        $contract->update($data);

        // Dispatch event for side effects
        event(new RentalContractUpdated($contract, $originalData));

        return $contract->fresh();
    }

    protected function isAvailableForUpdate(Vehicle $vehicle, Carbon $startDate, Carbon $endDate, int $excludeContractId): bool
    {
        if ($vehicle->availability_status === VehicleAvailabilityStatus::Maintenance || ! $vehicle->is_active) {
            return false;
        }

        $hasOverlap = $vehicle->rentalContracts()
            ->where('id', '!=', $excludeContractId) // Exclude current contract
            ->whereIn('status', ['active', 'pending'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        return ! $hasOverlap;
    }
}
