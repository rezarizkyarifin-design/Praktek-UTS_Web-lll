<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'attendance.create',
            'attendance.view.self',
            'attendance.view.subordinates',
            'filament.access',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'team_member' => ['attendance.create', 'attendance.view.self'],
            'group_leader' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'section_leader' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'supervisor' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'assistant_manager' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'team_manager' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'department_manager' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'departement_manager' => ['attendance.create', 'attendance.view.self', 'attendance.view.subordinates'],
            'admin' => $permissions,
            'super_admin' => $permissions,
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions(
                in_array($roleName, ['admin', 'super_admin'], true)
                    ? Permission::query()->where('guard_name', 'web')->get()
                    : $rolePermissions,
            );
        }
    }
}
