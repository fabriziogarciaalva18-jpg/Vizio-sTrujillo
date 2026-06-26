<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeliveryPersonMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->check()) {
            return redirect()->route('login');
        }
        if (!auth()->user()->is_delivery && !auth()->user()->is_admin) {
             abort(403,'No tienes permisos eres un reparetidor');
         }
     return $next($request);
    }
}
