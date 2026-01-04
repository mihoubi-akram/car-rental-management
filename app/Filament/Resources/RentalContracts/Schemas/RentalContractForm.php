<?php

namespace App\Filament\Resources\RentalContracts\Schemas;

use App\Enums\RentalContractStatus;
use App\Models\RentalContract;
use App\Models\Vehicle;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class RentalContractForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contract Information')
                    ->schema([
                        TextInput::make('contract_number')
                            ->label('Contract Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn () => 'RC-'.now()->year.'-'.str_pad(RentalContract::count() + 1, 5, '0', STR_PAD_LEFT))
                            ->visible(fn ($livewire) => $livewire instanceof EditRecord),

                        Select::make('status')
                            ->options(RentalContractStatus::class)
                            ->required()
                            ->default(RentalContractStatus::Pending->value),

                        Select::make('client_id')
                            ->relationship('client', 'email')
                            ->required()
                            ->searchable(['first_name', 'last_name', 'email'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->full_name}")
                            ->createOptionForm([
                                TextInput::make('first_name')->required(),
                                TextInput::make('last_name')->required(),
                                TextInput::make('email')->email()->required()->unique(),
                                TextInput::make('phone')->required(),
                                Textarea::make('address')->required(),
                            ]),

                        Select::make('vehicle_id')
                            ->relationship('vehicle', 'model')
                            ->required()
                            ->searchable(['model', 'registration_number'])
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->brand->name} {$record->model} ({$record->registration_number})")
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                if ($state) {
                                    $vehicle = Vehicle::find($state);
                                    if ($vehicle) {
                                        $set('daily_rate', $vehicle->daily_rate);

                                        $startDate = $get('start_date');
                                        $endDate = $get('end_date');
                                        if ($startDate && $endDate) {
                                            $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
                                            $set('total_days', $days);
                                            $set('total_amount', $vehicle->daily_rate * $days);
                                        }
                                    }
                                }
                            }),
                    ])
                    ->columns(2),

                Section::make('Rental Period')
                    ->schema([
                        DatePicker::make('start_date')
                            ->required()
                            ->minDate(now())
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $endDate = $get('end_date');
                                $dailyRate = $get('daily_rate');
                                if ($state && $endDate && $dailyRate) {
                                    $days = \Carbon\Carbon::parse($state)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
                                    $set('total_days', $days);
                                    $set('total_amount', $dailyRate * $days);
                                }
                            }),

                        DatePicker::make('end_date')
                            ->required()
                            ->after('start_date')
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $startDate = $get('start_date');
                                $dailyRate = $get('daily_rate');
                                if ($state && $startDate && $dailyRate) {
                                    $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($state)) + 1;
                                    $set('total_days', $days);
                                    $set('total_amount', $dailyRate * $days);
                                }
                            }),
                    ])
                    ->columns(2),

                Section::make('Pricing')
                    ->schema([
                        TextInput::make('daily_rate')
                            ->required()
                            ->numeric()
                            ->prefix('€')
                            ->reactive()
                            ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                $startDate = $get('start_date');
                                $endDate = $get('end_date');
                                if ($state && $startDate && $endDate) {
                                    $days = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;
                                    $set('total_days', $days);
                                    $set('total_amount', $state * $days);
                                }
                            }),

                        TextInput::make('total_days')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('€')
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(3),

                Section::make('Optional Details')
                    ->schema([
                        TextInput::make('mileage_start')
                            ->numeric()
                            ->nullable()
                            ->suffix('km'),

                        TextInput::make('mileage_end')
                            ->numeric()
                            ->nullable()
                            ->suffix('km')
                            ->gte('mileage_start'),

                        Textarea::make('notes')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
