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

            if ($user->hasRole('Jefe de Departamento') || $user->hasRole('Director General')) {
                if (empty($user->two_factor_confirmed_at) || empty($user->puesto)) {
                    Log::info('El usuario no ha registrado su 2-FA.');

                    if ($request->is('user/profile')) {
                        return $next($request);
                    }

                    return redirect('/user/profile')->with('message', 'En respuesta a las inquietudes relacionadas con la seguridad de la firma autógrafa dentro de la plataforma, hemos decidido implementar el uso de una firma digital para mayor protección. Para ello, es indispensable que registres y configures la autenticación de doble factor (2FA) en tu aplicación "Authenticator". Este paso garantizará un nivel adicional de seguridad y te permitirá continuar utilizando el sistema sin interrupciones. Agradecemos tu colaboración para mantener la seguridad de nuestros procesos.');
                }
            }
        }

        return $next($request);
    }
}
