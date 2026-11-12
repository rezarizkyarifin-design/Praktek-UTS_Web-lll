<?php

namespace App\Filament\Resources\Absensis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AbsensisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('karyawan.nama_karyawan')->label('Karyawan')->sortable()->searchable(),
                TextColumn::make('karyawan.position.position_name')->label('Jabatan')->sortable()->searchable(),
                TextColumn::make('tanggal')->label('Tanggal')->date()->sortable(),
                TextColumn::make('waktu_masuk')->label('Masuk'),
                TextColumn::make('waktu_pulang')->label('Pulang'),
                ImageColumn::make('foto_masuk')->label('Foto Masuk')->disk('public')->height(50)->toggleable(),
                ImageColumn::make('foto_pulang')->label('Foto Pulang')->disk('public')->height(50)->toggleable(),
                IconColumn::make('is_overtime')->boolean()->label('Lembur'),
                TextColumn::make('alasan_overtime')->label('Alasan Overtime')->limit(30)->toggleable(),
            ])
            ->filters([
                SelectFilter::make('is_overtime')
                    ->label('Overtime')
                    ->options([
                        '1' => 'Ya',
                        '0' => 'Tidak',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        filled($data['value'] ?? null),
                        fn (Builder $query): Builder => $query->where('is_overtime', $data['value']),
                    )),
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
