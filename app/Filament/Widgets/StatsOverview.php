<?php

namespace App\Filament\Widgets;

use App\Enums\RentalContractStatus;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $availableVehicles = Vehicle::available()->count();
        $activeContracts = RentalContract::active()->count();
        $expiredThisMonth = $this->getExpiredContractsThisMonth();
        $totalVehicles = Vehicle::count();

        return [
            Stat::make('Available Vehicles', $availableVehicles)
                ->description($availableVehicles === 1 ? '1 vehicle ready to rent' : "{$availableVehicles} vehicles ready to rent")
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->icon('heroicon-o-truck'),

            Stat::make('Active Contracts', $activeContracts)
                ->description($activeContracts === 1 ? '1 rental in progress' : "{$activeContracts} rentals in progress")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->icon('heroicon-o-document-check'),

            Stat::make('Expired This Month', $expiredThisMonth)
                ->description('Completed in '.now()->format('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('warning')
                ->icon('heroicon-o-calendar-days'),
        ];
    }

    protected function getExpiredContractsThisMonth(): int
    {
        return RentalContract::where('status', RentalContractStatus::Completed)
            ->whereYear('end_date', now()->year)
            ->whereMonth('end_date', now()->month)
            ->count();
    }
}
