<?php

namespace App\Filament\Resources\Karyawans\Tables;

use App\Models\Departemen;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KaryawansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_karyawan')->label('Nama Karyawan')->sortable()->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('team.nama_team')->label('Team')->sortable()->searchable(),
                TextColumn::make('team.departemen.nama_departemen')->label('Departemen')->sortable()->searchable(),
                TextColumn::make('position.position_name')->label('Jabatan')->sortable()->searchable(),
                TextColumn::make('absensis_count')->label('Jumlah Absensi')->sortable(),
            ])
            ->filters([
                SelectFilter::make('departemen_id')
                    ->label('Departemen')
                    ->options(fn (): array => Departemen::query()
                        ->orderBy('nama_departemen')
                        ->pluck('nama_departemen', 'id')
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->whereHas(
                            'team',
                            fn (Builder $teams): Builder => $teams->where('departemen_id', $data['value']),
                        ),
                    )),
                SelectFilter::make('team_id')
                    ->relationship('team', 'nama_team')
                    ->label('Team'),
                SelectFilter::make('position_id')
                    ->relationship('position', 'position_name')
                    ->label('Jabatan'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
