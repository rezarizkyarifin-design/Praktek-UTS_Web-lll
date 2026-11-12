<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('departemen_id')
                    ->label('Departemen')
                    ->relationship('departemen', 'nama_departemen')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('nama_team')
                    ->label('Nama Team')
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }
}
