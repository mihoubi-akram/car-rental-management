<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),

                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('phone')
                    ->searchable(),

                TextColumn::make('rentalContracts_count')
                    ->counts('rentalContracts')
                    ->label('Rentals'),

                IconColumn::make('is_blacklisted')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->label('Status'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_blacklisted')
                    ->label('Blacklist Status')
                    ->placeholder('All clients')
                    ->trueLabel('Blacklisted only')
                    ->falseLabel('Not blacklisted'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('toggle_blacklist')
                    ->label(fn ($record) => $record->is_blacklisted ? 'Remove Blacklist' : 'Blacklist Client')
                    ->icon(fn ($record) => $record->is_blacklisted ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn ($record) => $record->is_blacklisted ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalDescription(fn ($record) => $record->is_blacklisted
                        ? 'This client will be able to make rentals again.'
                        : 'This client will not be able to make new rentals.')
                    ->action(function ($record) {
                        $record->update(['is_blacklisted' => ! $record->is_blacklisted]);

                        Notification::make()
                            ->title($record->is_blacklisted ? 'Client blacklisted' : 'Blacklist removed')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
