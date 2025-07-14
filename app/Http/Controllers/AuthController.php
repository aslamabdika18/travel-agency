<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Login user dengan cookie-based authentication
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember_me'))) {
            // Regenerate session with more explicit error handling
            try {
                $request->session()->regenerate();
            } catch (\Exception $e) {
                // Log error but continue
                Log::error('Session regeneration error: ' . $e->getMessage());
            }

            $user = Auth::user();

            // Jika request AJAX, return JSON
            if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                $redirectUrl = $this->getRedirectUrlByRole($user);
                Log::info('Login successful via AJAX. User: ' . $user->email . ', Redirect URL: ' . $redirectUrl);
                Log::info('Request headers: ' . json_encode($request->headers->all()));

                $response = response()->json([
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => [
                        'user' => $this->formatUserResponse($user),
                        'redirect_url' => $redirectUrl
                    ]
                ]);

                Log::info('Response headers: ' . json_encode($response->headers->all()));
                return $response;
            }

            // Redirect berdasarkan role untuk request biasa
            if ($user->hasRole(['admin', 'super_admin'])) {
                return redirect()->intended('/admin');
            } elseif ($user->hasRole('customer')) {
                // Cek apakah ada intended URL dengan fragment booking
                $intendedUrl = session('url.intended', '/');
                if (strpos($intendedUrl, '#booking') !== false) {
                    // Jika ada, set session booking_intent
                    session(['booking_intent' => true]);
                    // Hapus #booking dari URL untuk menghindari duplikasi
                    $intendedUrl = str_replace('#booking', '', $intendedUrl);
                    $intendedUrl .= '?booking=true';
                }
                return redirect()->to($intendedUrl);
            } else {
                return redirect()->intended('/');
            }
        }

        // Jika request AJAX, return JSON error
        if ($request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            Log::info('Login failed via AJAX. Email: ' . $request->email);
            Log::info('Request headers: ' . json_encode($request->headers->all()));

            $response = response()->json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);

            Log::info('Response headers: ' . json_encode($response->headers->all()));
            return $response;
        }

        // Return back dengan error untuk request biasa
        return back()->with('toast_error', 'Invalid email or password.')->withInput();
    }

    /**
     * Register user baru
     */
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Name is required',
            'name.min' => 'Name must be at least 2 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role customer secara default
        $user->assignRole('customer');

        // Login otomatis setelah registrasi
        Auth::login($user);
        $request->session()->regenerate();

        // Jika request AJAX, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            $redirectUrl = $this->getRedirectUrlByRole($user);
            return response()->json([
                'success' => true,
                'message' => 'Registration successful! Welcome to our platform.',
                'data' => [
                    'user' => $this->formatUserResponse($user),
                    'redirect_url' => $redirectUrl
                ]
            ], 201);
        }

        // Redirect untuk request biasa
        $redirectUrl = $this->getRedirectUrlByRole($user);
        return redirect($redirectUrl)->with('toast_success', 'Registration successful! Welcome to our platform.');
    }

    /**
     * Logout user dan hapus session
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Jika request AJAX, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Logout successful'
            ]);
        }

        // Redirect untuk request biasa
        return redirect('/')->with('toast_success', 'You have been successfully logged out.');
    }

    /**
     * Check authentication status
     */
    public function check(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'success' => true,
                'data' => $this->formatUserResponse(Auth::user())
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Not authenticated'
        ], 401);
    }

    /**
     * Get authenticated user data
     */
    public function user(Request $request)
    {
        return $this->respondWithSuccess([
            'user' => $this->formatUserResponse($request->user())
        ]);
    }

    /**
     * Refresh session untuk memperpanjang login
     */
    public function refresh(Request $request)
    {
        $this->regenerateSession();

        return $this->respondWithSuccess([
            'message' => 'Session refreshed',
            'user' => $this->formatUserResponse($request->user())
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $this->validateProfileUpdateRequest($request);

        $user = $request->user();
        $user->update($request->validated());

        return $this->respondWithSuccess([
            'message' => 'Profile updated successfully',
            'user' => $this->formatUserResponse($user->fresh())
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $this->validatePasswordChangeRequest($request);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->respondWithError('Current password is incorrect', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return $this->respondWithSuccess([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Send password reset link
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this->respondWithSuccess([
                'message' => 'Password reset link sent to your email'
            ]);
        }

        return $this->respondWithError('Unable to send reset link', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Reset password dengan token
     */
    public function resetPassword(Request $request)
    {
        $this->validatePasswordResetRequest($request);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this->respondWithSuccess([
                'message' => 'Password reset successfully'
            ]);
        }

        return $this->respondWithError('Invalid reset token', Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    // Private helper methods untuk Clean Code

    private function validateLoginRequest(Request $request): array
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
            'remember_me' => 'boolean'
        ]);

        return $request->only('email', 'password');
    }

    private function validateRegistrationRequest(Request $request): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    private function validateProfileUpdateRequest(Request $request): void
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $request->user()->id,
            'contact' => 'sometimes|nullable|string|max:20',
        ]);
    }

    private function validatePasswordChangeRequest(Request $request): void
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
        ]);
    }

    private function validatePasswordResetRequest(Request $request): void
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    private function extractCredentials(Request $request): array
    {
        return $request->only('email', 'password');
    }

    private function attemptLogin(array $credentials, bool $remember): bool
    {
        return Auth::attempt($credentials, $remember);
    }

    private function regenerateSession(): void
    {
        request()->session()->regenerate();
    }

    private function createUser(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Assign role customer secara otomatis untuk user baru
        $user->assignRole('customer');

        return $user;
    }

    private function formatUserResponse(User $user): array
    {
        // Load roles dan permissions relationship untuk RBAC response
        $user->load(['roles', 'permissions']);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'contact' => $user->contact,
            'email_verified_at' => $user->email_verified_at,
            'roles' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
            'permissions' => $user->getAllPermissions()->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name,
                ];
            }),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }

    private function respondWithSuccess(array $data, int $status = Response::HTTP_OK): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data
        ], $status);
    }

    private function respondWithError(string $message, int $status = Response::HTTP_BAD_REQUEST): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    /**
     * Tentukan redirect URL berdasarkan role user
     */
    private function getRedirectUrlByRole($user): string
    {
        // Cek jika ada intended URL di session
        $intendedUrl = session()->pull('url.intended');
        Log::info('getRedirectUrlByRole - User: ' . $user->email . ', Intended URL: ' . ($intendedUrl ?: 'tidak ada'));
        Log::info('getRedirectUrlByRole - User roles: ' . implode(', ', $user->getRoleNames()->toArray()));

        // Jika ada intended URL dan user adalah customer, gunakan intended URL
        if ($intendedUrl && $user->hasRole('customer')) {
            Log::info('getRedirectUrlByRole - Menggunakan intended URL: ' . $intendedUrl);
            return $intendedUrl;
        }

        // Jika user adalah super_admin atau admin, redirect ke dashboard filament
        if ($user->hasRole(['admin', 'super_admin'])) {
            Log::info('getRedirectUrlByRole - User adalah admin, redirect ke /admin');
            return '/admin';
        }

        // Jika user adalah customer, redirect ke user/bookings
        if ($user->hasRole('customer')) {
            Log::info('getRedirectUrlByRole - User adalah customer, redirect ke /user/bookings');
            return '/user/bookings';
        }

        // Default redirect
        $defaultUrl = $intendedUrl ?: '/';
        Log::info('getRedirectUrlByRole - Menggunakan default URL: ' . $defaultUrl);
        return $defaultUrl;
    }
}
