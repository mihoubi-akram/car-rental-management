<?php

namespace App\Enums;

enum VehicleCategory: string
{
    case Economy = 'economy';
    case Compact = 'compact';
    case Sedan = 'sedan';
    case Suv = 'suv';
    case Luxury = 'luxury';
    case Van = 'van';

    public function label(): string
    {
        return match ($this) {
            self::Economy => 'Economy',
            self::Compact => 'Compact',
            self::Sedan => 'Sedan',
            self::Suv => 'SUV',
            self::Luxury => 'Luxury',
            self::Van => 'Van',
        };
    }
}
