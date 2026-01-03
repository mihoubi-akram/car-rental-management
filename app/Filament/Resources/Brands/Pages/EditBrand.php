<?php

namespace App\Filament\Resources\Brands\Pages;

use App\Actions\DeleteBrandAction;
use App\Exceptions\BrandHasVehiclesException;
use App\Filament\Resources\Brands\BrandResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditBrand extends EditRecord
{
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->using(function ($record, DeleteAction $action) {
                    try {
                        app(DeleteBrandAction::class)->execute($record);
                    } catch (BrandHasVehiclesException $e) {
                        Notification::make()
                            ->danger()
                            ->title('Cannot Delete Brand')
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
