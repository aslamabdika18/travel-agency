<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle(Request $request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            
            // For AJAX requests, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email Anda belum diverifikasi. Silakan verifikasi email terlebih dahulu.',
                    'redirect_url' => route('verification.notice')
                ], 403);
            }
            
            // For regular requests, redirect to verification notice
            return $redirectToRoute
                ? Redirect::route($redirectToRoute)
                : Redirect::route('verification.notice')
                    ->with('toast_warning', 'Silakan verifikasi email Anda terlebih dahulu.');
        }

        return $next($request);
    }
}