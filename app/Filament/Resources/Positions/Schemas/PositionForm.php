<?php

namespace App\Filament\Resources\Positions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('position_name')
                    ->label('Nama Jabatan')
                    ->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('role_key')
                    ->label('Role')
                    ->options(fn (): array => Role::query()
                        ->whereIn('name', [
                            'team_member',
                            'group_leader',
                            'section_leader',
                            'supervisor',
                            'assistant_manager',
                            'team_manager',
                            'department_manager',
                            'departement_manager',
                        ])
                        ->orderBy('name')
                        ->pluck('name', 'name')
                        ->all())
                    ->searchable()
                    ->unique(ignoreRecord: true)
                    ->required(),
            ]);
    }
}
