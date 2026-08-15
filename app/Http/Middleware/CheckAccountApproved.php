<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->status_akun === 'pending') {
            auth()->logout();
            return redirect()->route('auth.waiting')->with('nip', $user->nip);
        }
        if ($user && $user->status_akun === 'rejected') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah ditolak. Hubungi admin untuk informasi lebih lanjut.');
        }
        return $next($request);
    }
}
