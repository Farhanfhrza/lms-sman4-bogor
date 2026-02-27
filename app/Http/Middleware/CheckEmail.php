<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmail
{
    /**
     * Routes that should be accessible even without email/verification.
     */
    protected array $except = [
        'profile.edit',
        'profile.update',
        'verification.notice',
        'verification.verify',
        'verification.send',
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // Allow excepted routes through
        foreach ($this->except as $route) {
            if ($request->routeIs($route)) {
                return $next($request);
            }
        }

        // Also allow logout via POST path
        if ($request->is('logout')) {
            return $next($request);
        }

        // Step 1: User has no email → force them to add one
        if (empty($user->email)) {
            return redirect()->route('profile.edit')
                ->with('status', 'Silakan tambahkan alamat email Anda terlebih dahulu.');
        }

        // Step 2: User has email but hasn't verified it → send to verification page
        if (is_null($user->email_verified_at)) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
