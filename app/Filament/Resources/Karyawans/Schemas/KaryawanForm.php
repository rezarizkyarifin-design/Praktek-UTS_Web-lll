<?php

namespace App\Filament\Resources\Karyawans\Schemas;

use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Team;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class KaryawanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_karyawan')
                    ->label('Nama Karyawan')
                    ->maxLength(255)
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('departemen_selector')
                    ->label('Departemen')
                    ->options(fn (): array => Departemen::query()
                        ->orderBy('nama_departemen')
                        ->pluck('nama_departemen', 'id')
                        ->all())
                    ->default(fn (?Karyawan $record): ?int => $record?->team?->departemen_id)
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(fn (Set $set) => $set('team_id', null))
                    ->searchable()
                    ->required(),
                Select::make('team_id')
                    ->label('Team')
                    ->options(fn (Get $get): array => Team::query()
                        ->where('departemen_id', $get('departemen_selector'))
                        ->orderBy('nama_team')
                        ->pluck('nama_team', 'id')
                        ->all())
                    ->disabled(fn (Get $get): bool => blank($get('departemen_selector')))
                    ->searchable()
                    ->required(),
                Select::make('position_id')
                    ->label('Jabatan')
                    ->relationship('position', 'position_name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }
}
