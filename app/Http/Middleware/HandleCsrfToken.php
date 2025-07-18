<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class HandleCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Exclude API routes from CSRF for initial login
        // 'api/auth/login',
        // 'api/auth/register',
        // 'api/auth/forgot-password',
        // 'api/auth/reset-password',
        // // Exclude booking endpoints
        // 'api/booking/*',
        // 'api/travel-packages/*',
        // // Temporary: exclude web booking for testing
        // 'booking',
        // // Exclude admin routes for session sharing
        // 'admin/*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Add CSRF token to response headers for frontend
        $response = parent::handle($request, $next);

        if ($request->expectsJson()) {
            $response->header('X-CSRF-TOKEN', csrf_token());
        }

        return $response;
    }
}
