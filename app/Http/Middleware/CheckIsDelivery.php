<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckIsDelivery
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || !auth()->user()->is_delivery) {
            abort(403, 'Acceso solo para repartidores.');
        }
        return $next($request);
    }
}
