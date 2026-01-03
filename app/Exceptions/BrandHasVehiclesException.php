<?php

namespace App\Exceptions;

use Exception;

class BrandHasVehiclesException extends Exception
{
    protected $message = 'Cannot delete brand because it has vehicles associated with it. Please remove or reassign all vehicles first.';
}
