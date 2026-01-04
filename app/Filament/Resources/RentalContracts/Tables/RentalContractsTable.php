<?php

namespace App\Filament\Resources\RentalContracts\Tables;

use App\Enums\RentalContractStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RentalContractsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['client', 'vehicle.brand']))
            ->columns([
                TextColumn::make('contract_number')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable(['clients.first_name', 'clients.last_name']),

                TextColumn::make('vehicle.brand.name')
                    ->label('Brand')
                    ->searchable(),

                TextColumn::make('vehicle.model')
                    ->label('Model')
                    ->searchable(),

                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (RentalContractStatus $state): string => match ($state) {
                        RentalContractStatus::Pending => 'gray',
                        RentalContractStatus::Active => 'info',
                        RentalContractStatus::Completed => 'success',
                        RentalContractStatus::Cancelled => 'danger',
                    }),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(RentalContractStatus::class),

                SelectFilter::make('vehicle_id')
                    ->relationship('vehicle', 'model')
                    ->searchable()
                    ->preload()
                    ->label('Vehicle'),

                SelectFilter::make('client_id')
                    ->relationship('client', 'email')
                    ->searchable()
                    ->preload()
                    ->label('Client'),

                Filter::make('start_date')
                    ->schema([
                        DatePicker::make('start_from')
                            ->label('Start date from'),
                        DatePicker::make('start_until')
                            ->label('Start date until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_from'], fn ($q, $date) => $q->where('start_date', '>=', $date))
                            ->when($data['start_until'], fn ($q, $date) => $q->where('start_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('complete')
                    ->label('Complete')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === RentalContractStatus::Active->value)
                    ->action(function ($record) {
                        $record->complete();

                        Notification::make()
                            ->title('Contract completed')
                            ->success()
                            ->send();
                    }),

                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to cancel this contract?')
                    ->visible(fn ($record) => in_array($record->status, [RentalContractStatus::Pending->value, RentalContractStatus::Active->value]))
                    ->action(function ($record) {
                        $record->cancel();

                        Notification::make()
                            ->title('Contract cancelled')
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
