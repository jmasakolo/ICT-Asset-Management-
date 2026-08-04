<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts a route to specific User::ROLES values. Only applies to the
 * web/sanctum-guarded User model — the separate `admin` guard/Admin model
 * has no role column and is unaffected (an admin session never reaches this
 * middleware since admin routes use their own guard/routes).
 *
 * Must run after 'auth'/'auth:sanctum', which sets the resolved guard for
 * the rest of the request, so $request->user() already reflects it here.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        abort_if(! $user || ! in_array($user->role, $roles, true), 403);

        return $next($request);
    }
}
