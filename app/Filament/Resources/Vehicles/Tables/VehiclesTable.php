<?php

namespace App\Filament\Resources\Vehicles\Tables;

use App\Actions\DeleteVehicleAction;
use App\Enums\VehicleAvailabilityStatus;
use App\Enums\VehicleCategory;
use App\Enums\VehicleFuelType;
use App\Exceptions\VehicleHasRentalContractsException;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('brand'))
            ->columns([
                TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('year')
                    ->sortable(),

                TextColumn::make('registration_number')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Registration copied!'),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (VehicleCategory $state): string => match ($state) {
                        VehicleCategory::Economy => 'gray',
                        VehicleCategory::Compact => 'info',
                        VehicleCategory::Sedan => 'success',
                        VehicleCategory::Suv => 'warning',
                        VehicleCategory::Luxury => 'danger',
                        VehicleCategory::Van => 'primary',
                    }),

                TextColumn::make('availability_status')
                    ->badge()
                    ->color(fn (VehicleAvailabilityStatus $state): string => match ($state) {
                        VehicleAvailabilityStatus::Available => 'success',
                        VehicleAvailabilityStatus::Reserved => 'warning',
                        VehicleAvailabilityStatus::Maintenance => 'danger',
                    }),

                TextColumn::make('daily_rate')
                    ->money('EUR')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Brand'),

                SelectFilter::make('category')
                    ->options(VehicleCategory::class)
                    ->label('Category'),

                SelectFilter::make('availability_status')
                    ->options(VehicleAvailabilityStatus::class)
                    ->label('Availability'),

                SelectFilter::make('fuel_type')
                    ->options(VehicleFuelType::class)
                    ->label('Fuel Type'),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All vehicles')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('set_maintenance')
                        ->label('Set to Maintenance')
                        ->icon('heroicon-o-wrench')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->availability_status !== VehicleAvailabilityStatus::Maintenance->value)
                        ->action(function ($record) {
                            $record->update(['availability_status' => VehicleAvailabilityStatus::Maintenance->value]);

                            Notification::make()
                                ->title('Vehicle set to maintenance')
                                ->success()
                                ->send();
                        }),

                    Action::make('set_available')
                        ->label('Set to Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->availability_status !== VehicleAvailabilityStatus::Available->value)
                        ->action(function ($record) {
                            $record->update(['availability_status' => VehicleAvailabilityStatus::Available->value]);

                            Notification::make()
                                ->title('Vehicle set to available')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $action = app(DeleteVehicleAction::class);
                            $failed = 0;
                            $success = 0;

                            foreach ($records as $record) {
                                try {
                                    $action->execute($record);
                                    $success++;
                                } catch (VehicleHasRentalContractsException $e) {
                                    $failed++;

                                    Notification::make()
                                        ->danger()
                                        ->title("Cannot Delete: {$record->registration_number}")
                                        ->body($e->getMessage())
                                        ->duration(5000)
                                        ->send();
                                }
                            }

                            if ($success > 0) {
                                Notification::make()
                                    ->success()
                                    ->title("Deleted {$success} vehicle(s)")
                                    ->duration(5000)
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
