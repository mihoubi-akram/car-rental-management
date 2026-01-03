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

    public function __construct(
        protected UpdateRentalContractAction $updateAction,
        protected DeleteRentalContractAction $deleteAction
    ) {
        parent::__construct();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->using(fn ($record) => $this->deleteAction->execute($record)),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return $this->updateAction->execute($record, $data);
    }
}
