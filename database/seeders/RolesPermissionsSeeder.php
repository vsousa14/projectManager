<?php


namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $ceoRole = Role::firstOrCreate(['name' => 'CEO']);

        Permission::firstOrCreate(['name' => 'View Backoffice']);

        //User CRUD permissions
        Permission::firstOrCreate(['name' => 'Create User']);
        Permission::firstOrCreate(['name' => 'View Users']);
        Permission::firstOrCreate(['name' => 'Update User']);
        Permission::firstOrCreate(['name' => 'Delete User']);

        //Roles CRUD permissions
        Permission::firstOrCreate(['name' => 'Create Role']);
        Permission::firstOrCreate(['name' => 'View Roles']);
        Permission::firstOrCreate(['name' => 'Update Role']);
        Permission::firstOrCreate(['name' => 'Delete Role']);

        //Permissions CRUD permissions
        Permission::firstOrCreate(['name' => 'Create Permission']);
        Permission::firstOrCreate(['name' => 'View Permissions']);
        Permission::firstOrCreate(['name' => 'Update Permission']);
        Permission::firstOrCreate(['name' => 'Delete Permission']);

        $ceoRole->givePermissionTo(Permission::all());

        $user = User::find(1);
        if ($user && !$user->hasRole('CEO')) {
            $user->assignRole($ceoRole);
    }

    }
}