<?php

namespace App\Enums;

enum VehicleAvailabilityStatus: string
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Reserved => 'Reserved',
            self::Maintenance => 'Under Maintenance',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Available => 'success',
            self::Reserved => 'warning',
            self::Maintenance => 'danger',
        };
    }
}
