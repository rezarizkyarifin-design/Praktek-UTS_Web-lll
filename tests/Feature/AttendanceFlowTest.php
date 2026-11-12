<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Departemen;
use App\Models\Karyawan;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_second_attendance_on_the_same_day_requires_a_reason_and_is_marked_overtime(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $karyawan = $this->createKaryawan('team_member');
        $user = User::factory()->create(['karyawan_id' => $karyawan->id]);
        $user->assignRole('team_member');
        $tanggal = now()->toDateString();

        $this->actingAs($user)
            ->post(route('attendances.store'), [
                'tanggal' => $tanggal,
                'waktu_masuk' => '08:00',
                'waktu_pulang' => '17:00',
            ])
            ->assertRedirect(route('attendances.index'));

        $this->assertDatabaseHas('absensis', [
            'karyawan_id' => $karyawan->id,
            'tanggal' => "{$tanggal} 00:00:00",
            'waktu_masuk' => '08:00:00',
            'is_overtime' => false,
        ]);

        $this->actingAs($user)
            ->post(route('attendances.store'), [
                'tanggal' => $tanggal,
                'waktu_masuk' => '08:00',
                'alasan_overtime' => 'Waktu yang sama',
            ])
            ->assertSessionHasErrors('waktu_masuk');

        $this->actingAs($user)
            ->post(route('attendances.store'), [
                'tanggal' => $tanggal,
                'waktu_masuk' => '18:00',
                'waktu_pulang' => '20:00',
            ])
            ->assertSessionHasErrors('alasan_overtime');

        $this->actingAs($user)
            ->post(route('attendances.store'), [
                'tanggal' => $tanggal,
                'waktu_masuk' => '18:00',
                'waktu_pulang' => '20:00',
                'alasan_overtime' => 'Pekerjaan belum selesai',
            ])
            ->assertRedirect(route('attendances.index'));

        $this->assertDatabaseHas('absensis', [
            'karyawan_id' => $karyawan->id,
            'tanggal' => "{$tanggal} 00:00:00",
            'waktu_masuk' => '18:00:00',
            'is_overtime' => true,
            'alasan_overtime' => 'Pekerjaan belum selesai',
        ]);
    }

    public function test_manager_sees_team_attendance_but_not_other_teams(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $departemen = Departemen::factory()->create();
        $team = Team::factory()->for($departemen)->create();
        $otherTeam = Team::factory()->for($departemen)->create();
        $managerPosition = Position::factory()->create(['role_key' => 'group_leader']);
        $memberPosition = Position::factory()->create(['role_key' => 'team_member']);

        $manager = Karyawan::factory()->for($team)->for($managerPosition, 'position')->create();
        $member = Karyawan::factory()->for($team)->for($memberPosition, 'position')->create();
        $outsideMember = Karyawan::factory()->for($otherTeam)->for($memberPosition, 'position')->create();
        $user = User::factory()->create(['karyawan_id' => $manager->id]);
        $user->assignRole('group_leader');

        Absensi::factory()->create(['karyawan_id' => $manager->id]);
        Absensi::factory()->create(['karyawan_id' => $member->id]);
        Absensi::factory()->create(['karyawan_id' => $outsideMember->id]);

        $response = $this->actingAs($user)->get(route('attendances.index'));

        $response
            ->assertOk()
            ->assertSee($manager->nama_karyawan)
            ->assertSee($member->nama_karyawan)
            ->assertDontSee($outsideMember->nama_karyawan);
    }

    public function test_department_manager_sees_all_teams_in_the_department_only(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $departemen = Departemen::factory()->create();
        $otherDepartemen = Departemen::factory()->create();
        $team = Team::factory()->for($departemen)->create();
        $otherTeam = Team::factory()->for($departemen)->create();
        $outsideTeam = Team::factory()->for($otherDepartemen)->create();
        $managerPosition = Position::factory()->create(['role_key' => 'department_manager']);
        $memberPosition = Position::factory()->create(['role_key' => 'team_member']);

        $manager = Karyawan::factory()->for($team)->for($managerPosition, 'position')->create();
        $sameDepartmentMember = Karyawan::factory()->for($otherTeam)->for($memberPosition, 'position')->create();
        $outsideMember = Karyawan::factory()->for($outsideTeam)->for($memberPosition, 'position')->create();
        $user = User::factory()->create(['karyawan_id' => $manager->id]);
        $user->assignRole('department_manager');

        Absensi::factory()->create(['karyawan_id' => $sameDepartmentMember->id]);
        Absensi::factory()->create(['karyawan_id' => $outsideMember->id]);

        $response = $this->actingAs($user)->get(route('attendances.index'));

        $response
            ->assertOk()
            ->assertSee($sameDepartmentMember->nama_karyawan)
            ->assertDontSee($outsideMember->nama_karyawan);
    }

    public function test_login_redirects_by_role_and_filament_is_limited_to_administrators(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $employee = User::factory()->create();
        $employee->assignRole('team_member');

        $this->post('/login', [
            'email' => $employee->email,
            'password' => 'password',
        ])->assertRedirect(route('attendances.index'));

        $this->get('/admin')->assertForbidden();
        $this->post('/logout');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin');

        $this->get('/admin')->assertOk();
        $this->get('/admin/departemens')->assertOk();
        $this->get('/admin/teams/create')->assertOk();
        $this->get('/admin/positions')->assertOk();
        $this->get('/admin/karyawans/create')->assertOk();
        $this->get('/admin/absensis/create')->assertOk();
        $this->get('/admin/users/create')->assertOk();
        $this->get('/admin/shield/roles')->assertOk();
    }

    public function test_employee_role_tracks_position_changes(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $karyawan = $this->createKaryawan('team_member');
        $user = User::factory()->create(['karyawan_id' => $karyawan->id]);
        $this->assertSame(['team_member'], $user->getRoleNames()->all());

        $newPosition = Position::factory()->create(['role_key' => 'group_leader']);
        $karyawan->update(['position_id' => $newPosition->id]);

        $this->assertSame(['group_leader'], $user->fresh()->getRoleNames()->all());
    }

    private function createKaryawan(string $roleKey): Karyawan
    {
        $position = Position::factory()->create(['role_key' => $roleKey]);

        return Karyawan::factory()
            ->for(Team::factory()->for(Departemen::factory()))
            ->for($position, 'position')
            ->create();
    }
}
