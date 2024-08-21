<?php

// app/Http/Controllers/PermissionController.php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function create()
    {
        $roles = Role::all();
        return view('admin.create-permission', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
            'roles' => 'required|array',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        // Asignar el permiso a los roles seleccionados
        $roles = Role::whereIn('id', $request->roles)->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permission);
        }

        return redirect()->route('admin.roles-permissions')->with('success', 'Permiso creado y asignado a los roles seleccionados.');
    }
}

