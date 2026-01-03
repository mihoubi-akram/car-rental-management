<?php

namespace App\Actions;

use App\Events\RentalContractDeleted;
use App\Models\RentalContract;

class DeleteRentalContractAction
{
    public function execute(RentalContract $contract): void
    {
        // Dispatch event before deletion (so listeners have access to contract data)
        event(new RentalContractDeleted($contract));

        // Delete the contract
        $contract->delete();
    }
}
