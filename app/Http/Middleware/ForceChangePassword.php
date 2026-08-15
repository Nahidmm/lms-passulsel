<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->force_change_password) {
            // Don't redirect if already on the change password page or logout
            if (!$request->routeIs('auth.change-password*') && !$request->routeIs('logout')) {
                return redirect()->route('auth.change-password')
                    ->with('warning', 'Anda wajib mengganti password sebelum melanjutkan.');
            }
        }
        return $next($request);
    }
}
