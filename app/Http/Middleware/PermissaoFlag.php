<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissaoFlag
{
    public function handle(Request $request, Closure $next, string $flag)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admin tem acesso total
        if ((int)($user->admin ?? 0) === 1) {
            return $next($request);
        }

        // Valida flag 0/1 da coluna
        if (!isset($user->$flag) || (int)$user->$flag !== 1) {
            abort(403, 'Verifique sua Permiçao de Acesso Com Admin !!!');
        }

        return $next($request);
    }
}
