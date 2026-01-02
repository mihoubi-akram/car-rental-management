<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(100),

                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        Textarea::make('address')
                            ->required()
                            ->maxLength(255)
                            ->rows(3),
                    ])
                    ->columns(2),

                Section::make('Status & Notes')
                    ->schema([
                        Toggle::make('is_blacklisted')
                            ->label('Blacklisted')
                            ->default(false)
                            ->helperText('Prevent this client from making new rentals'),

                        Textarea::make('notes')
                            ->nullable()
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
