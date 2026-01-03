<?php

namespace App\Exceptions;

use Exception;

class VehicleNotAvailableException extends Exception
{
    protected $message = 'The selected vehicle is not available for the specified dates.';
}
