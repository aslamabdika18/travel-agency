<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Role;
use Mockery;

class EnsureEmailIsVerifiedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles for testing
        $this->createRoles();
        
        $this->middleware = new EnsureEmailIsVerified();
    }

    private function createRoles()
    {
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    /** @test */
    public function middleware_allows_verified_user_to_continue()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Success', $response->getContent());
    }

    /** @test */
    public function middleware_redirects_unverified_user_to_verification_notice()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContains('verification.notice', $response->getTargetUrl());
        
        // Check for toast message in session
        $this->assertEquals(
            'Silakan verifikasi email Anda terlebih dahulu.',
            session('toast_warning')
        );
    }

    /** @test */
    public function middleware_redirects_guest_user_to_verification_notice()
    {
        $request = Request::create('/test');
        $request->setUserResolver(function () {
            return null; // No authenticated user
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContains('verification.notice', $response->getTargetUrl());
    }

    /** @test */
    public function middleware_returns_json_for_ajax_request_from_unverified_user()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $request = Request::create('/test');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals(
            'Email Anda belum diverifikasi. Silakan verifikasi email terlebih dahulu.',
            $responseData['message']
        );
        $this->assertStringContains('verification.notice', $responseData['redirect_url']);
    }

    /** @test */
    public function middleware_returns_json_for_ajax_request_from_guest()
    {
        $request = Request::create('/test');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->setUserResolver(function () {
            return null;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    /** @test */
    public function middleware_allows_verified_user_with_ajax_request()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $request = Request::create('/test');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new JsonResponse(['success' => true]);
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
    }

    /** @test */
    public function middleware_uses_custom_redirect_route_when_provided()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        // Test with custom redirect route
        $response = $this->middleware->handle($request, $next, 'custom.route');

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContains('custom.route', $response->getTargetUrl());
    }

    /** @test */
    public function middleware_handles_user_that_implements_must_verify_email()
    {
        // Create user that implements MustVerifyEmail (which our User model does)
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->assertInstanceOf(
            \Illuminate\Contracts\Auth\MustVerifyEmail::class,
            $user
        );

        $request = Request::create('/test');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    /** @test */
    public function middleware_checks_has_verified_email_method()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        // Verify that hasVerifiedEmail method works correctly
        $this->assertTrue($user->hasVerifiedEmail());

        $unverifiedUser = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->assertFalse($unverifiedUser->hasVerifiedEmail());
    }

    /** @test */
    public function middleware_handles_expectsJson_method_correctly()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        // Test with Accept: application/json header
        $request = Request::create('/test');
        $request->headers->set('Accept', 'application/json');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $next = function ($request) {
            return new Response('Success');
        };

        $response = $this->middleware->handle($request, $next);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(403, $response->getStatusCode());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}