<?php

namespace App\Filament\Resources\Vehicles\Pages;

use App\Actions\DeleteVehicleAction;
use App\Exceptions\VehicleHasRentalContractsException;
use App\Filament\Resources\Vehicles\VehicleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVehicle extends EditRecord
{
    protected static string $resource = VehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->using(function ($record, DeleteAction $action) {
                    try {
                        app(DeleteVehicleAction::class)->execute($record);
                    } catch (VehicleHasRentalContractsException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Vehicle')
                            ->body($e->getMessage())
                            ->duration(5000)
                            ->send();

                        $action->halt();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getDeleteRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
