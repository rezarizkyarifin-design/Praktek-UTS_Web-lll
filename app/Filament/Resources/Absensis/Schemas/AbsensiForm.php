<?php

namespace App\Filament\Resources\Absensis\Schemas;

use App\Models\Absensi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AbsensiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('karyawan_id')
                    ->label('Karyawan')
                    ->relationship('karyawan', 'nama_karyawan')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(),
                TimePicker::make('waktu_masuk')
                    ->label('Waktu Masuk')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('waktu_pulang')
                    ->label('Waktu Pulang')
                    ->seconds(false),
                FileUpload::make('foto_masuk')
                    ->label('Foto Masuk')
                    ->disk('public')
                    ->directory('absensi/foto_masuk')
                    ->image()
                    ->maxSize(5120),
                FileUpload::make('foto_pulang')
                    ->label('Foto Pulang')
                    ->disk('public')
                    ->directory('absensi/foto_pulang')
                    ->image()
                    ->maxSize(5120),
                Toggle::make('is_overtime')
                    ->label('Apakah Overtime?')
                    ->live()
                    ->dehydrated(false)
                    ->default(fn (?Absensi $record): bool => $record?->is_overtime ?? false),
                Textarea::make('alasan_overtime')
                    ->label('Alasan Overtime')
                    ->visible(fn (Get $get): bool => $get('is_overtime'))
                    ->required(fn (Get $get): bool => $get('is_overtime'))
                    ->maxLength(255),
            ]);
    }
}
