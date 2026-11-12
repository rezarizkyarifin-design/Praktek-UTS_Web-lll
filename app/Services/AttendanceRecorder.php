<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AttendanceRecorder
{
    public function create(array $data): Absensi
    {
        return DB::transaction(function () use ($data): Absensi {
            $karyawan = Karyawan::query()
                ->lockForUpdate()
                ->findOrFail($data['karyawan_id']);
            $data = $this->normalizeTimes($data);

            $data['is_overtime'] = Absensi::query()
                ->where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $data['tanggal'])
                ->exists();

            $this->validateRecord($data, $karyawan->id);

            return $karyawan->absensis()->create($this->persistableData($data));
        });
    }

    public function update(Absensi $absensi, array $data): Absensi
    {
        return DB::transaction(function () use ($absensi, $data): Absensi {
            $karyawanId = $data['karyawan_id'] ?? $absensi->karyawan_id;
            $karyawan = Karyawan::query()
                ->lockForUpdate()
                ->findOrFail($karyawanId);

            $data['karyawan_id'] = $karyawan->id;
            $data = $this->normalizeTimes($data);
            $data['is_overtime'] = Absensi::query()
                ->where('karyawan_id', $karyawan->id)
                ->whereDate('tanggal', $data['tanggal'])
                ->whereKeyNot($absensi->id)
                ->exists();

            $this->validateRecord($data, $karyawan->id, $absensi->id);
            $absensi->update($this->persistableData($data));

            return $absensi->refresh();
        });
    }

    private function validateRecord(array $data, int $karyawanId, ?int $ignoreId = null): void
    {
        if (filled($data['waktu_pulang'] ?? null) && $data['waktu_pulang'] <= $data['waktu_masuk']) {
            throw ValidationException::withMessages([
                'waktu_pulang' => 'Waktu pulang harus lebih besar dari waktu masuk.',
            ]);
        }

        if ($data['is_overtime'] && blank($data['alasan_overtime'] ?? null)) {
            throw ValidationException::withMessages([
                'alasan_overtime' => 'Alasan lembur wajib diisi.',
            ]);
        }

        $duplicate = Absensi::query()
            ->where('karyawan_id', $karyawanId)
            ->whereDate('tanggal', $data['tanggal'])
            ->where('waktu_masuk', $data['waktu_masuk'])
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'waktu_masuk' => 'Karyawan sudah memiliki absensi pada waktu masuk tersebut.',
            ]);
        }
    }

    private function persistableData(array $data): array
    {
        if (! $data['is_overtime']) {
            $data['alasan_overtime'] = null;
        }

        return [
            'karyawan_id' => $data['karyawan_id'],
            'tanggal' => $data['tanggal'],
            'waktu_masuk' => $data['waktu_masuk'],
            'waktu_pulang' => $data['waktu_pulang'] ?? null,
            'is_overtime' => $data['is_overtime'],
            'alasan_overtime' => $data['alasan_overtime'] ?? null,
            'foto_masuk' => $data['foto_masuk'] ?? null,
            'foto_pulang' => $data['foto_pulang'] ?? null,
        ];
    }

    private function normalizeTimes(array $data): array
    {
        foreach (['waktu_masuk', 'waktu_pulang'] as $field) {
            if (filled($data[$field] ?? null) && strlen($data[$field]) === 5) {
                $data[$field] .= ':00';
            }
        }

        return $data;
    }
}
