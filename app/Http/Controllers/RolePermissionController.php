<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'permissions')->get();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.roles-permissions', compact('users', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $user->syncRoles($request->roles);
        $user->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Roles y permisos actualizados correctamente.');
    }
}

