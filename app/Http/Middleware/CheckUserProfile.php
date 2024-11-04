<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckUserProfile
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            Log::info('Middleware CheckUserProfile ejecutado para el usuario ID: ' . $user->id);
            Log::info('Roles del usuario: ' . implode(', ', $user->getRoleNames()->toArray()));

            if ($user->hasRole('Jefe de Departamento')) {
                if (empty($user->firma_autografa) || empty($user->puesto)) {
                    Log::info('El usuario no ha completado firma_autografa o puesto.');

                    if ($request->is('user/profile')) {
                        return $next($request);
                    }

                    return redirect('/user/profile')->with('message', 'Por favor, complete los campos requeridos de su perfil.');
                }
            }
        }

        return $next($request);
    }
}
