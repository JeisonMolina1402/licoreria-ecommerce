<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next)
    {
        // Si el usuario está logueado y es 'admin' o 'vendedor', lo dejamos pasar al panel
        if (Auth::check() && in_array(Auth::user()->rol, ['admin', 'vendedor'])) {
            return $next($request);
        }

        // Si es 'cliente', le bloqueamos el paso y lo regresamos a la tienda
        return redirect()->route('tienda.index');
    }
}