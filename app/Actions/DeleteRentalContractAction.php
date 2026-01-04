<?php

namespace App\Actions;

use App\Events\RentalContractDeleted;
use App\Models\RentalContract;
use Illuminate\Support\Facades\DB;

class DeleteRentalContractAction
{
    public function execute(RentalContract $contract): void
    {
        DB::transaction(function () use ($contract) {
            // Dispatch event before deletion (so listeners have access to contract data)
            event(new RentalContractDeleted($contract));

            // Delete the contract
            $contract->delete();
        });
    }
}
