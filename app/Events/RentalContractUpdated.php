<?php

namespace App\Events;

use App\Models\RentalContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RentalContractUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public RentalContract $contract,
        public array $originalData
    ) {}
}
