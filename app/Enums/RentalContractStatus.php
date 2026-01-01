<?php

namespace App\Enums;

enum RentalContractStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Cancelled => 'Cancelled',
        };
    }

    public function canTransitionTo(RentalContractStatus $newStatus): bool
    {
        return match ($this) {
            self::Pending => in_array($newStatus, [self::Active, self::Cancelled]),
            self::Active => in_array($newStatus, [self::Completed, self::Cancelled]),
            self::Completed => false,
            self::Cancelled => false,
        };
    }
}
