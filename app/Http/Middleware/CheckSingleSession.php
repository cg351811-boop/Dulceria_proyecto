<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSingleSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $currentSessionId = $request->session()->getId();

            // 1. Control de tiempo de inactividad (5 minutos por defecto)
            $timeoutSeconds = config('session.lifetime', 5) * 60;
            $lastActivity = $request->session()->get('last_user_activity');

            if ($lastActivity && (now()->timestamp - $lastActivity) > $timeoutSeconds) {
                // Eliminar la sesión de la base de datos
                if (config('session.driver') === 'database') {
                    DB::table('sessions')->where('id', $currentSessionId)->delete();
                }

                if ($user->last_session_id === $currentSessionId) {
                    $user->last_session_id = null;
                    $user->save();
                }

                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $mensajeInactividad = 'Tu sesión ha expirado tras 5 minutos de inactividad por motivos de seguridad.';

                if ($request->expectsJson() || $request->ajax() || $request->is('check-session')) {
                    return response()->json([
                        'status' => 'session_timeout',
                        'message' => $mensajeInactividad,
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('inactividad', $mensajeInactividad);
            }

            // 2. Control de sesión única (cierre si se inició en otro dispositivo)
            if (empty($user->last_session_id)) {
                $user->last_session_id = $currentSessionId;
                $user->save();
            } elseif ($user->last_session_id !== $currentSessionId) {
                if (config('session.driver') === 'database') {
                    DB::table('sessions')->where('id', $currentSessionId)->delete();
                }

                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $mensajeDispositivo = 'Se cerró la sesión porque se inició sesión en otro dispositivo con esta cuenta.';

                if ($request->expectsJson() || $request->ajax() || $request->is('check-session')) {
                    return response()->json([
                        'status' => 'session_closed',
                        'message' => $mensajeDispositivo,
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('sesion_cerrada', $mensajeDispositivo);
            }

            // 3. Registrar última actividad del usuario (excluyendo el ping de /check-session)
            if (! $request->is('check-session') || empty($lastActivity)) {
                $request->session()->put('last_user_activity', now()->timestamp);
            }
        }

        return $next($request);
    }
}
