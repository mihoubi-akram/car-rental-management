<?php

namespace App\Exceptions;

use Exception;

class VehicleInMaintenanceException extends Exception
{
    protected $message = 'Vehicles under maintenance cannot be rented.';
}
