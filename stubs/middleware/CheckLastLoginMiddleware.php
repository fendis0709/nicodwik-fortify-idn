<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckLastLoginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $lastLogin = $request->session()->get('last_login');
        $day = (config('session.lifetime') / 60) / 24;

        if ($this->_hasBeenOfflineFor($lastLogin, $day)) {
            $request->session()->flush();
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors(['email' => config('fortify.messages.error.login.session-expired')]);
        }

        return $next($request);
    }

    private function _hasBeenOfflineFor($lastLogin, $day): bool
    {
        return empty($lastLogin) ||
            (
                ! empty($lastLogin) &&
                Carbon::parse($lastLogin)->diffInDays(now()) >= $day
            );
    }
}
