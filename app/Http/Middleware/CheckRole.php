<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Dozwolone role (np. 'admin', 'supervisor')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Sprawdź czy użytkownik jest zalogowany
        if (!$request->user()) {
            abort(403, 'Nieautoryzowany dostęp');
        }

        // Sprawdź czy użytkownik ma odpowiednią rolę
        if (!in_array($request->user()->role, $roles)) {
            abort(403, 'Brak uprawnień do tej sekcji');
        }

        return $next($request);
    }
}
