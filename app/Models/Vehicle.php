<?php

namespace App\Models;

use App\Enums\RentalContractStatus;
use App\Enums\VehicleAvailabilityStatus;
use App\Enums\VehicleCategory;
use App\Enums\VehicleFuelType;
use App\Enums\VehicleTransmission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'model',
        'year',
        'registration_number',
        'vin',
        'color',
        'fuel_type',
        'transmission',
        'seats',
        'mileage',
        'last_maintenance_date',
        'next_maintenance_mileage',
        'daily_rate',
        'weekly_rate',
        'category',
        'availability_status',
        'is_active',
        'features',
    ];

    protected function casts(): array
    {
        return [
            'fuel_type' => VehicleFuelType::class,
            'transmission' => VehicleTransmission::class,
            'category' => VehicleCategory::class,
            'availability_status' => VehicleAvailabilityStatus::class,
            'is_active' => 'boolean',
            'features' => 'array',
            'last_maintenance_date' => 'date',
            'daily_rate' => 'decimal:2',
            'weekly_rate' => 'decimal:2',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function rentalContracts(): HasMany
    {
        return $this->hasMany(RentalContract::class);
    }

    public function isAvailableForDates(Carbon $startDate, Carbon $endDate): bool
    {
        if ($this->availability_status === VehicleAvailabilityStatus::Maintenance || ! $this->is_active) {
            return false;
        }

        $hasOverlap = $this->rentalContracts()
            ->whereIn('status', [RentalContractStatus::Active, RentalContractStatus::Pending])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        return ! $hasOverlap;
    }

    public function updateAvailabilityStatus(): void
    {
        if ($this->hasActiveContract()) {
            $this->update(['availability_status' => VehicleAvailabilityStatus::Reserved]);
        } elseif ($this->availability_status === VehicleAvailabilityStatus::Reserved) {
            $this->update(['availability_status' => VehicleAvailabilityStatus::Available]);
        }
    }

    public function hasActiveContract(): bool
    {
        return $this->rentalContracts()
            ->whereIn('status', [RentalContractStatus::Active, RentalContractStatus::Pending])
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->exists();
    }

    public function scopeAvailable($query)
    {
        return $query->where('availability_status', VehicleAvailabilityStatus::Available)
            ->where('is_active', true);
    }

    public function scopeInMaintenance($query)
    {
        return $query->where('availability_status', VehicleAvailabilityStatus::Maintenance);
    }

    public function scopeByCategory($query, VehicleCategory $category)
    {
        return $query->where('category', $category);
    }
}
