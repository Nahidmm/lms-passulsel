<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePretestCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Skip if not logged in or not a peserta
        if (!$user || !$user->isPeserta()) {
            return $next($request);
        }

        // Skip if already taken
        if ($user->has_taken_pretest) {
            return $next($request);
        }

        // Check if there is an active pretest
        $pretest = \App\Models\Materi::where('is_pretest', true)->where('is_active', true)->first();
        if (!$pretest) {
            return $next($request);
        }

        // Allow access to logout to prevent infinite loop
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // Allow pretest-specific routes (we will create peserta.pretest.take)
        if ($request->routeIs('peserta.pretest.*')) {
            return $next($request);
        }

        // Allow evaluasi routes ONLY IF they are for the pretest
        if ($request->routeIs('peserta.evaluasi.*')) {
            // EvaluasiController uses 'sesi' parameter for most routes
            $sesi = $request->route('sesi');
            if ($sesi) {
                // If the sesi belongs to the pretest, allow it
                $sesiId = is_object($sesi) ? $sesi->id : $sesi;
                $sesiEvaluasi = \App\Models\SesiEvaluasi::find($sesiId);
                if ($sesiEvaluasi && $sesiEvaluasi->materi_id === $pretest->id) {
                    return $next($request);
                }
            } else {
                // For 'evaluasi.start', the materi_id is in the POST payload
                if ($request->input('materi_id') == $pretest->id) {
                    return $next($request);
                }
            }
        }

        // Otherwise, redirect to pretest splash screen
        return redirect()->route('peserta.pretest.take');
    }
}
