<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Verificar autenticación
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión');
        }
        
        // Verificar si es administrador
        if (!isset(auth()->user()->is_admin) || !auth()->user()->is_admin) {
            abort(403, 'No tienes permisos de administrador');
        }
        
        return $next($request);
    }
}