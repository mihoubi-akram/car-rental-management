<?php

namespace App\Models;

use App\Enums\RentalContractStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'vehicle_id',
        'client_id',
        'start_date',
        'end_date',
        'daily_rate',
        'total_days',
        'total_amount',
        'status',
        'mileage_start',
        'mileage_end',
        'notes',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RentalContractStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'daily_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'cancelled_at' => 'datetime',
        ];
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function calculateTotals(): void
    {
        $this->total_days = $this->start_date->diffInDays($this->end_date) + 1;
        $this->total_amount = $this->daily_rate * $this->total_days;
    }

    public function cancel(): void
    {
        $this->update([
            'status' => RentalContractStatus::Cancelled,
            'cancelled_at' => now(),
        ]);
    }

    public function complete(): void
    {
        $this->update(['status' => RentalContractStatus::Completed]);
    }

    public function canBeModified(): bool
    {
        return $this->status === RentalContractStatus::Pending;
    }

    public function scopeActive($query)
    {
        return $query->where('status', RentalContractStatus::Active);
    }

    public function scopePending($query)
    {
        return $query->where('status', RentalContractStatus::Pending);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', RentalContractStatus::Completed);
    }

    public function scopeForVehicle($query, Vehicle $vehicle)
    {
        return $query->where('vehicle_id', $vehicle->id);
    }

    public function scopeForDateRange($query, Carbon $start, Carbon $end)
    {
        return $query->where(function ($q) use ($start, $end) {
            $q->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function ($query) use ($start, $end) {
                    $query->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        });
    }
}
