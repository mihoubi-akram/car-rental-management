<?php

namespace App\Filament\Resources\RentalContracts\Pages;

use App\Actions\DeleteRentalContractAction;
use App\Actions\UpdateRentalContractAction;
use App\Filament\Resources\RentalContracts\RentalContractResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditRentalContract extends EditRecord
{
    protected static string $resource = RentalContractResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->using(fn ($record) => app(DeleteRentalContractAction::class)->execute($record)),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(UpdateRentalContractAction::class)->execute($record, $data);
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
