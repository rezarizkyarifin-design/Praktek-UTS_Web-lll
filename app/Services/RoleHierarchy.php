<?php

namespace App\Services;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class RoleHierarchy
{
    private const ROLE_ORDER = [
        'team_member',
        'group_leader',
        'section_leader',
        'supervisor',
        'assistant_manager',
        'team_manager',
        'department_manager',
    ];

    public static function accessibleRoles(string $roleName): array
    {
        $roleName = self::normalizeRole($roleName);
        $index = array_search($roleName, self::ROLE_ORDER, true);

        if ($index === false) {
            return $roleName === '' ? [] : [$roleName];
        }

        return array_slice(self::ROLE_ORDER, 0, $index + 1);
    }

    public function visibleEmployees(User $user): Builder
    {
        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return Karyawan::query();
        }

        $karyawan = $user->karyawan()->with(['position', 'team'])->first();

        if (! $karyawan) {
            return Karyawan::query()->whereRaw('1 = 0');
        }

        $roleName = self::normalizeRole(
            $karyawan->position?->role_key ?? $user->getRoleNames()->first() ?? '',
        );
        $accessibleRoles = self::accessibleRoles($roleName);

        if (count($accessibleRoles) < 2 || ! $karyawan->team) {
            return Karyawan::query()->whereKey($karyawan->getKey());
        }

        return Karyawan::query()->where(function (Builder $query) use ($karyawan, $roleName, $accessibleRoles): void {
            $query->whereKey($karyawan->getKey())
                ->orWhere(function (Builder $subordinates) use ($karyawan, $roleName, $accessibleRoles): void {
                    $subordinates->whereHas('position', function (Builder $positions) use ($accessibleRoles): void {
                        $positions->whereIn('role_key', $accessibleRoles);
                    });

                    if ($roleName === 'department_manager') {
                        $subordinates->whereHas('team', function (Builder $teams) use ($karyawan): void {
                            $teams->where('departemen_id', $karyawan->team->departemen_id);
                        });

                        return;
                    }

                    $subordinates->where('team_id', $karyawan->team_id);
                });
        });
    }

    public function visibleAbsensi(User $user): Builder
    {
        return Absensi::query()
            ->whereIn('karyawan_id', $this->visibleEmployees($user)->select('id'));
    }

    private static function normalizeRole(string $roleName): string
    {
        return $roleName === 'departement_manager' ? 'department_manager' : $roleName;
    }
}
