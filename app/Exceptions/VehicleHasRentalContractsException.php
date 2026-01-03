<?php

namespace App\Exceptions;

use Exception;

class VehicleHasRentalContractsException extends Exception
{
    protected $message = 'Cannot delete vehicle because it has rental contracts associated with it. Please remove or reassign all contracts first.';
}
