<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEvaluasiMode
{
    /**
     * Block access to AI assistant routes when user has an active evaluation session.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->hasActiveSesiEvaluasi()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'AI Assistant tidak tersedia selama sesi evaluasi berlangsung.',
                    'evaluasi_active' => true,
                ], 403);
            }
            return redirect()->route('peserta.evaluasi.soal', [
                'sesi' => $user->getActiveSesiEvaluasi()->id
            ])->with('warning', 'Anda memiliki sesi evaluasi yang sedang berlangsung.');
        }
        return $next($request);
    }
}
