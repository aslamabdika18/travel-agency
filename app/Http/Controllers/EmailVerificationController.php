<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\EmailVerificationNotification;

class EmailVerificationController extends Controller
{
    /**
     * Show the email verification notice.
     */
    public function notice()
    {
        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email sudah terverifikasi sebelumnya.',
                    'redirect_url' => $this->getRedirectUrl($request->user())
                ]);
            }
            
            return redirect()->intended($this->getRedirectUrl($request->user()))
                ->with('toast_info', 'Email Anda sudah terverifikasi sebelumnya.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email berhasil diverifikasi! Selamat datang di platform kami.',
                'redirect_url' => $this->getRedirectUrl($request->user())
            ]);
        }

        return redirect()->intended($this->getRedirectUrl($request->user()))
            ->with('toast_success', 'Email berhasil diverifikasi! Selamat datang di platform kami.');
    }

    /**
     * Send a new email verification notification.
     */
    public function send(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terverifikasi.'
                ], 400);
            }
            
            return back()->with('toast_info', 'Email Anda sudah terverifikasi.');
        }

        $request->user()->notify(new EmailVerificationNotification());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Link verifikasi telah dikirim ke email Anda.'
            ]);
        }

        return back()->with('toast_success', 'Link verifikasi telah dikirim ke email Anda.');
    }

    /**
     * Resend email verification notification (alias for send method).
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email Anda sudah terverifikasi.'
                ], 400);
            }
            
            return back()->with('toast_info', 'Email Anda sudah terverifikasi.');
        }

        $request->user()->notify(new EmailVerificationNotification());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Email verifikasi telah dikirim ulang!'
            ]);
        }

        return back()->with('toast_success', 'Email verifikasi telah dikirim ulang!');
    }

    /**
     * Get redirect URL based on user role
     */
    private function getRedirectUrl($user)
    {
        if ($user->hasRole(['admin', 'super_admin'])) {
            return '/admin';
        } elseif ($user->hasRole('customer')) {
            // Check if there's a booking intent
            if (session('booking_intent')) {
                session()->forget('booking_intent');
                return session('url.intended', '/') . '?booking=true';
            }
            return session('url.intended', '/');
        }
        
        return '/';
    }
}