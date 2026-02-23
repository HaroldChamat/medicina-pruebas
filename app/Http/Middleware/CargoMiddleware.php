<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CargoMiddleware
{
    public function handle(Request $request, Closure $next, ...$cargos)
    {
        if (!session()->has('cargo')) {
            return redirect('/');
        }

        $cargoUsuario = session('cargo');

        if (!in_array($cargoUsuario, $cargos)) {
            abort(403, 'No tienes permiso para acceder');
        }

        return $next($request);
    }
}
