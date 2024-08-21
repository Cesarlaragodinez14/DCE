<?php

// app/Http/Controllers/RoleController.php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function create()
    {
        $permissions = Permission::all();
        return view('admin.create-role', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);

        return redirect()->route('admin.roles-permissions')->with('success', 'Rol creado y permisos asignados correctamente.');
    }
}
