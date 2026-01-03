<?php

namespace App\Filament\Resources\RentalContracts\Pages;

use App\Actions\CreateRentalContractAction;
use App\Filament\Resources\RentalContracts\RentalContractResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateRentalContract extends CreateRecord
{
    protected static string $resource = RentalContractResource::class;

    public function __construct(protected CreateRentalContractAction $createAction)
    {
        parent::__construct();
    }

    protected function handleRecordCreation(array $data): Model
    {
        return $this->createAction->execute($data);
    }
}
