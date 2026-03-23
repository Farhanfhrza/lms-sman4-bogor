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
        // System verifikasi email dinonaktifkan sementara.
        // Biarkan semua user mengakses platform tanpa harus punya atau memverifikasi email.
        return $next($request);
    }
}
