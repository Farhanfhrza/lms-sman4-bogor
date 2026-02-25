<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && empty($user->email)) {
            if (!$request->routeIs('profile.edit') && !$request->routeIs('profile.update') && !$request->is('logout')) {
                return redirect()->route('profile.edit')
                    ->with('status', 'Please provide your email address to verify your account.');
            }
        }

        return $next($request);
    }
}
