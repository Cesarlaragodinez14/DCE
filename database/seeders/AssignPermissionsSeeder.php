<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Encuentra al usuario root
        $rootUser = User::where('email', 'al@mdtch.mx')->first();
        
        // Asigna el rol admin si no lo tiene
        $rootUser->assignRole('admin');
        
        // Encuentra el rol admin
        $adminRole = Role::findByName('admin');

        // Asigna los permisos
        $adminRole->givePermissionTo(['view permissions', 'manage roles']);
    }
}
