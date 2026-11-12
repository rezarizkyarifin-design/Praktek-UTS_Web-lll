<?php

namespace Database\Factories;

use App\Models\Karyawan;
use App\Models\Position;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Karyawan>
 */
class KaryawanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_karyawan' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'team_id' => Team::factory(),
            'position_id' => Position::factory(),
        ];
    }
}
