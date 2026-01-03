<?php

namespace App\Actions;

use App\Enums\VehicleAvailabilityStatus;
use App\Events\RentalContractCreated;
use App\Exceptions\VehicleInMaintenanceException;
use App\Exceptions\VehicleNotAvailableException;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CreateRentalContractAction
{
    public function execute(array $data): RentalContract
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);

        // Calculate totals
        if (isset($data['start_date'], $data['end_date'], $data['daily_rate'])) {
            $data['total_days'] = $startDate->diffInDays($endDate) + 1;
            $data['total_amount'] = $data['daily_rate'] * $data['total_days'];
        }

        // Generate unique contract number
        $data['contract_number'] = $this->generateContractNumber();

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);

        // Check if vehicle is in maintenance
        if ($vehicle->availability_status === VehicleAvailabilityStatus::Maintenance) {
            throw new VehicleInMaintenanceException;
        }

        // Check vehicle availability for dates
        if (! $vehicle->isAvailableForDates($startDate, $endDate)) {
            throw new VehicleNotAvailableException;
        }

        return DB::transaction(function () use ($data) {
            $contract = RentalContract::create($data);
            event(new RentalContractCreated($contract));

            return $contract;
        });
    }

    protected function generateContractNumber(): string
    {
        do {
            $number = 'RC-'.date('Y').'-'.str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (RentalContract::where('contract_number', $number)->exists());

        return $number;
    }
}
