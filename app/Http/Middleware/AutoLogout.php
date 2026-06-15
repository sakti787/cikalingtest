<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AutoLogout
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity');

            if ($lastActivity && (time() - $lastActivity) > 1800) {
                Auth::logout();
                session()->invalidate();

                return redirect('/login')->with('timeout', 'Sesi berakhir karena tidak aktif 30 menit.');
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
