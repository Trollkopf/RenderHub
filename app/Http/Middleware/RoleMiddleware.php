<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role=null): Response
    {
        // Si el usuario no está autenticado, redirigir al login
        if (!auth()->check()) {
            return redirect('/login');
        }

        // Si el usuario no tiene el rol requerido, retornar 403
        if (auth()->user()->role !== $role) {
            abort(403, 'Acceso no autorizado');
        }

        return $next($request);
    }
}
