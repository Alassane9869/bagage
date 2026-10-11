<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:administrateur,responsable')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role?->slug;

        if (empty($roles) || in_array($userRole, $roles)) {
            return $next($request);
        }

        abort(403, 'Accès refusé. Vous n\'avez pas les droits nécessaires.');
    }
}
