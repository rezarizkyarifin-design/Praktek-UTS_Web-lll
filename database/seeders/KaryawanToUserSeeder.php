<?php

namespace Database\Seeders;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KaryawanToUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPassword = env('EMPLOYEE_DEFAULT_PASSWORD');

        Karyawan::query()
            ->with('position')
            ->chunkById(100, function ($karyawans) use ($defaultPassword): void {
                foreach ($karyawans as $karyawan) {
                    $user = User::firstOrCreate(
                        ['email' => $karyawan->email],
                        [
                            'name' => $karyawan->nama_karyawan,
                            'karyawan_id' => $karyawan->id,
                            'password' => Hash::make($defaultPassword ?: Str::password(40)),
                        ],
                    );

                    if ($user->karyawan_id !== null && $user->karyawan_id !== $karyawan->id) {
                        $this->command?->warn("Skipping {$karyawan->email}: its account is linked to another employee.");

                        continue;
                    }

                    $user->forceFill(['karyawan_id' => $karyawan->id])->save();

                    if ($roleKey = $karyawan->position?->role_key) {
                        $user->syncRoles([$roleKey]);
                    }
                }
            });
    }
}
