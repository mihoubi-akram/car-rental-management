<?php

namespace App\Events;

use App\Models\RentalContract;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RentalContractDeleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public RentalContract $contract) {}
}
