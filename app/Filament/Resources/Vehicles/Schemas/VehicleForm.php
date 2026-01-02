<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\VehicleAvailabilityStatus;
use App\Enums\VehicleCategory;
use App\Enums\VehicleFuelType;
use App\Enums\VehicleTransmission;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')->required(),
                                Toggle::make('is_active')->default(true),
                            ]),

                        TextInput::make('model')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(date('Y') + 1),

                        TextInput::make('registration_number')
                            ->required()
                            ->unique()
                            ->maxLength(20),

                        TextInput::make('vin')
                            ->label('VIN')
                            ->maxLength(17)
                            ->nullable()
                            ->unique(),

                        TextInput::make('color')
                            ->maxLength(50)
                            ->nullable(),
                    ])
                    ->columns(2),

                Section::make('Technical Specifications')
                    ->schema([
                        Select::make('fuel_type')
                            ->required()
                            ->options(VehicleFuelType::class),

                        Select::make('transmission')
                            ->required()
                            ->options(VehicleTransmission::class),

                        TextInput::make('seats')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20),

                        TagsInput::make('features')
                            ->nullable()
                            ->placeholder('Add features (GPS, AC, etc.)')
                            ->suggestions(['GPS', 'AC', 'Bluetooth', 'USB', 'Backup Camera', 'Cruise Control', 'Heated Seats']),
                    ])
                    ->columns(2),

                Section::make('Operational Data')
                    ->schema([
                        TextInput::make('mileage')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->suffix('km'),

                        DatePicker::make('last_maintenance_date')
                            ->nullable(),

                        TextInput::make('next_maintenance_mileage')
                            ->nullable()
                            ->numeric()
                            ->suffix('km'),
                    ])
                    ->columns(3),

                Section::make('Pricing & Category')
                    ->schema([
                        Select::make('category')
                            ->required()
                            ->options(VehicleCategory::class),

                        TextInput::make('daily_rate')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->minValue(0),

                        TextInput::make('weekly_rate')
                            ->nullable()
                            ->numeric()
                            ->prefix('€')
                            ->step('0.01')
                            ->minValue(0),
                    ])
                    ->columns(3),

                Section::make('Availability')
                    ->schema([
                        Select::make('availability_status')
                            ->required()
                            ->options(VehicleAvailabilityStatus::class)
                            ->default('available'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
