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

                    // Permitir acceso a perfil y aviso de privacidad para evitar loops
                    if (
                        $request->is('user/profile') ||
                        $request->is('aceptar-aviso-privacidad') ||
                        $request->routeIs('privacy.notice') ||
                        $request->routeIs('privacy.notice.accept')
                    ) {
                        return $next($request);
                    }

                    return redirect('/user/profile')->with('message', 'En respuesta a las inquietudes relacionadas con la seguridad de la firma autógrafa dentro de la plataforma, hemos decidido implementar el uso de una firma digital para mayor protección. Para ello, es indispensable que registres y configures la autenticación de doble factor (2FA) en tu aplicación "Authenticator". Este paso garantizará un nivel adicional de seguridad y te permitirá continuar utilizando el sistema sin interrupciones. Agradecemos tu colaboración para mantener la seguridad de nuestros procesos.');
                }
            }

            // Verificación de aceptación y versión del Aviso de Privacidad
            $currentVersion = (string) config('privacy.notice_version');
            $userAccepted = (bool) ($user->user_ap_accepted ?? false);
            $versionMismatch = $user->user_ap_version && $user->user_ap_version !== $currentVersion;
            if (!$userAccepted || $versionMismatch) {
                // Permitir acceso a la pantalla de aceptación para evitar loops
                if ($request->is('aceptar-aviso-privacidad') || $request->routeIs('privacy.notice') || $request->routeIs('privacy.notice.accept')) {
                    return $next($request);
                }

                return redirect('/aceptar-aviso-privacidad')
                    ->with('message', 'Debes aceptar el Aviso de Privacidad para continuar usando la plataforma.');
            }
        }

        return $next($request);
    }
}
