<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CatUaa;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

use App\Helpers\MailHelper;

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
        // Validar los datos iniciales
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|min:8|confirmed',
            'roles'      => 'required|array',
            'roles.*'    => 'exists:roles,id',
            'uaa_id'     => 'nullable|exists:cat_uaa,id',
            // Campos adicionales
            'firma_autografa' => 'nullable|string|max:255',
            'puesto'          => 'nullable|string|max:255',
        ]);

        // Obtener los nombres de los roles seleccionados
        $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();

        // Verificar si el rol 'Jefe de departamento' está seleccionado
        if (in_array('Jefe de departamento', $roleNames)) {
            // Si es 'Jefe de departamento', los campos 'firma_autógrafa' y 'puesto' son obligatorios
            $request->validate([
                'firma_autografa' => 'required|string|max:255',
                'puesto'          => 'required|string|max:255',
            ]);
        }

        // Crear el usuario con los campos adicionales
        $user = User::create([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'password'        => bcrypt($validated['password']),
            'uaa_id'          => $validated['uaa_id'] ?? null,
            'firma_autografa' => $validated['firma_autografa'] ?? null,
            'puesto'          => $validated['puesto'] ?? null,
        ]);

         // 9) Enviar mail a la contraparte
         $user_email = $validated['email'];
         //$user_email = "ablozano@asf.gob.mx";
         $subject  = 'Te damos la bienvenida';
         $content = '<b>¡Te damos la bienvenida a la plataforma SAES!</b><br><br> Tu correo ha sido registrado exitosamente. Para comenzar, haz clic en el botón. <br><br>Si no has actualizado tu contraseña. Esta se construye por defailt con tus iniciales (Ejemplo - Juan Pérez Reyez = JPR) seguidas por 2025* <br><small>En dígitos: Dos Mil Veiticinco Asterisco</small><br> Ejemplo: <b>JPR2025*</b>.<br><br><a href="http://saes.asf.gob.mx/" target="_blank" style="background-color: #007bff; color: #ffffff; padding: 10px 15px; text-decoration: none; border-radius: 4px; font-weight: bold;">Ir a la plataforma</a>.<br><br> Si tu cuenta lo requiere, configura el 2FA con una aplicación de autenticathor.<br><br> Para cambiar tu contraseña, ingresa a “Mi perfil” haciendo clic en tu nombre en la esquina superior derecha de la pantalla ingresa tu nueva contraseña y presiona actualizar.<br><br>';

         $mailData = [
             'footer' => 'Correo automático, no supervisado.',
             'action' => [
                 'text' => 'Ir a la plataforma',
                 'url'  => 'http://saes.asf.gob.mx/'
             ]
         ];
 
         MailHelper::sendDynamicMail([$user_email], $subject, $content, $mailData);

        // Asignar los roles al usuario
        $user->syncRoles($roleNames);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $uaas = CatUaa::all();
        $isAdmin = auth()->user()->hasRole('admin');

        return view('users.edit', compact('user', 'roles', 'uaas', 'isAdmin'));
    }

    public function update(Request $request, User $user)
    {
        // Validar los datos iniciales
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'two_factor_secret' => 'nullable|string|max:255',
            'two_factor_recovery_codes' => 'nullable|string|max:255',
            'two_factor_confirmed_at' => 'nullable|string|max:255',
            'password'   => 'nullable|string|min:8|confirmed',
            'roles'      => 'required|array',
            'roles.*'    => 'exists:roles,id',
            'uaa_id'     => 'nullable|exists:cat_uaa,id',
            // Campos adicionales
            'firma_autografa' => 'nullable|string|max:255',
            'puesto'          => 'nullable|string|max:255',
        ]);

        // Obtener los nombres de los roles seleccionados
        $roleNames = Role::whereIn('id', $validated['roles'])->pluck('name')->toArray();

        // Verificar si el rol 'Jefe de departamento' está seleccionado
        if (in_array('Jefe de departamento', $roleNames)) {
            // Si es 'Jefe de departamento', los campos 'firma_autógrafa' y 'puesto' son obligatorios
            $request->validate([
                'firma_autografa' => 'required|string|max:255',
                'puesto'          => 'required|string|max:255',
            ]);
        }

        // Actualizar el usuario con los campos adicionales
        $user->update([
            'name'            => $validated['name'],
            'email'           => $validated['email'],
            'two_factor_secret'           => $validated['two_factor_secret'],
            'two_factor_recovery_codes'           => $validated['two_factor_recovery_codes'],
            'two_factor_confirmed_at'           => $validated['two_factor_confirmed_at'],
            'password'        => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            'uaa_id'          => $validated['uaa_id'],
            'firma_autografa' => $validated['firma_autografa'] ?? $user->firma_autografa,
            'puesto'          => $validated['puesto'] ?? $user->puesto,
        ]);

        // Sincronizar los roles del usuario
        $user->syncRoles($roleNames);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }

    public function impersonate(User $user)
    {
        if (Auth::user()->canImpersonate() && $user->canBeImpersonated()) {
            Auth::user()->impersonate($user);
            return redirect()->route('dashboard');
        }
        abort(403, 'No tienes permisos para impersonar a este usuario.');
    }

    public function stopImpersonation()
    {
        if (Auth::user()->isImpersonating()) {
            Auth::user()->leaveImpersonation();
            return redirect()->route('dashboard');
        }
        abort(403, 'No estás impersonando a ningún usuario.');
    }

}
