<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'karyawan_id' => Karyawan::factory(),
            'tanggal' => fake()->date(),
            'waktu_masuk' => fake()->unique()->time('H:i:s'),
            'waktu_pulang' => fake()->time('H:i:s'),
            'is_overtime' => false,
            'alasan_overtime' => null,
            'foto_masuk' => null,
            'foto_pulang' => null,
        ];
    }
}
