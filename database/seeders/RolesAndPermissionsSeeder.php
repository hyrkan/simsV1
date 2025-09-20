<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'manage staff',
            'view reports',
            'manage settings',
            'view dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo([
            'view users',
            'create users',
            'edit users',
            'view roles',
            'manage staff',
            'view reports',
            'view dashboard',
        ]);

        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view users',
            'manage staff',
            'view reports',
            'view dashboard',
        ]);

        $teacherRole = Role::create(['name' => 'teacher']);
        $teacherRole->givePermissionTo([
            'view users',
            'view dashboard',
        ]);

        $staffRole = Role::create(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view dashboard',
        ]);
    }
}