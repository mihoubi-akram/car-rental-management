<?php

namespace App\Filament\Resources\Brands\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BrandForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Information')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(100)
                            ->unique(),

                        FileUpload::make('logo_url')
                            ->image()
                            ->nullable()
                            ->disk('public')
                            ->directory('brand-logos')
                            ->imageEditor()
                            ->label('Brand Logo'),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }
}
