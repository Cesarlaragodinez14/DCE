<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CatUaa;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $uaas = CatUaa::all();
        return view('users.create', compact('roles', 'uaas'));
    }


    public function store(Request $request)
    {
        // Validar los datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id', // Validamos que los roles existan
            'uaa_id' => 'nullable|exists:cat_uaa,id', // Validar que la UAA exista o permitir "ninguna"
        ]);

        // Crear el usuario con el campo UAA
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'uaa_id' => $validated['uaa_id'] ?? null, // Guardar UAA o null si no se asignó
        ]);

        // Convertir IDs de roles a nombres de roles
        $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();

        // Sincronizar los roles con el usuario
        $user->syncRoles($roleNames);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }


    public function edit(User $user)
    {
        $roles = Role::all();
        $uaas = CatUaa::all();  // Obtener todas las UAA
        $isAdmin = auth()->user()->hasRole('admin');
        
        return view('users.edit', compact('user', 'roles', 'uaas', 'isAdmin'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'uaa_id' => 'nullable|exists:cat_uaa,id', // Validar que la UAA exista o permitir nulo
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'uaa_id' => $validated['uaa_id'], // Actualizar la UAA
        ]);

        $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();
        $user->syncRoles($roleNames);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}
