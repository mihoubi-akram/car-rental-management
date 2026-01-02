<?php

namespace App\Filament\Resources\RentalContracts;

use App\Filament\Resources\RentalContracts\Pages\CreateRentalContract;
use App\Filament\Resources\RentalContracts\Pages\EditRentalContract;
use App\Filament\Resources\RentalContracts\Pages\ListRentalContracts;
use App\Filament\Resources\RentalContracts\Pages\ViewRentalContract;
use App\Filament\Resources\RentalContracts\Schemas\RentalContractForm;
use App\Filament\Resources\RentalContracts\Schemas\RentalContractInfolist;
use App\Filament\Resources\RentalContracts\Tables\RentalContractsTable;
use App\Models\RentalContract;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class RentalContractResource extends Resource
{
    protected static ?string $model = RentalContract::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Contracts';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return RentalContractForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RentalContractInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RentalContractsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRentalContracts::route('/'),
            'create' => CreateRentalContract::route('/create'),
            'view' => ViewRentalContract::route('/{record}'),
            'edit' => EditRentalContract::route('/{record}/edit'),
        ];
    }
}
