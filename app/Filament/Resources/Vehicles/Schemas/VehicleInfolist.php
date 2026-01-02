<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class VehicleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('brand.name')
                    ->label('Brand'),
                TextEntry::make('model'),
                TextEntry::make('year')
                    ->numeric(),
                TextEntry::make('registration_number'),
                TextEntry::make('vin')
                    ->placeholder('-'),
                TextEntry::make('color')
                    ->placeholder('-'),
                TextEntry::make('fuel_type')
                    ->badge(),
                TextEntry::make('transmission')
                    ->badge(),
                TextEntry::make('seats')
                    ->numeric(),
                TextEntry::make('mileage')
                    ->numeric(),
                TextEntry::make('last_maintenance_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('next_maintenance_mileage')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('daily_rate')
                    ->numeric(),
                TextEntry::make('weekly_rate')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('category')
                    ->badge(),
                TextEntry::make('availability_status')
                    ->badge(),
                IconEntry::make('is_active')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
