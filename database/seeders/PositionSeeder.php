<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            'Team Member' => 'team_member',
            'Group Leader' => 'group_leader',
            'Section Leader' => 'section_leader',
            'Supervisor' => 'supervisor',
            'Assistant Manager' => 'assistant_manager',
            'Team Manager' => 'team_manager',
            'Department Manager' => 'department_manager',
        ];

        foreach ($positions as $positionName => $roleKey) {
            Position::firstOrCreate(
                ['role_key' => $roleKey],
                ['position_name' => $positionName],
            );
        }
    }
}
