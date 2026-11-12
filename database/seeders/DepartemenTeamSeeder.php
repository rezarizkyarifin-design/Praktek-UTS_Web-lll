<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Team;
use Illuminate\Database\Seeder;

class DepartemenTeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemenTeams = [
            'MSD' => ['HR Team', 'GA Team'],
            'ITB' => ['P/C Team', 'Infra Team', 'MES Team'],
            'FCD' => ['CCP Team', 'SMP Team', 'Coke Team', 'RMH Team', 'Sinter Team', 'Gas Team', 'Plate Mill Team', 'HRP Team'],
            'CMD' => ['Crane Team', 'Instrument Team', 'Field Repair Team'],
        ];

        foreach ($departemenTeams as $namaDepartemen => $namaTeams) {
            $departemen = Departemen::firstOrCreate(['nama_departemen' => $namaDepartemen]);

            foreach ($namaTeams as $namaTeam) {
                Team::firstOrCreate([
                    'departemen_id' => $departemen->id,
                    'nama_team' => $namaTeam,
                ]);
            }
        }
    }
}
