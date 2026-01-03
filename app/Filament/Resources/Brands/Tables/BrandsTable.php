<?php

namespace App\Filament\Resources\Brands\Tables;

use App\Actions\DeleteBrandAction;
use App\Exceptions\BrandHasVehiclesException;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BrandsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('logo_url')
                    ->label('Logo')
                    ->circular(),

                TextColumn::make('vehicles_count')
                    ->counts('vehicles')
                    ->label('Vehicles'),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All brands')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            $action = app(DeleteBrandAction::class);
                            $failed = 0;
                            $success = 0;

                            foreach ($records as $record) {
                                try {
                                    $action->execute($record);
                                    $success++;
                                } catch (BrandHasVehiclesException $e) {
                                    $failed++;

                                    Notification::make()
                                        ->danger()
                                        ->title("Cannot Delete: {$record->name}")
                                        ->body($e->getMessage())
                                        ->duration(5000)
                                        ->send();
                                }
                            }

                            if ($success > 0) {
                                Notification::make()
                                    ->success()
                                    ->title("Deleted {$success} brand(s)")
                                    ->duration(5000)
                                    ->send();
                            }
                        }),
                ]),
            ]);
    }
}
