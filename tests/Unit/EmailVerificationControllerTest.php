<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Http\Controllers\EmailVerificationController;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Mockery;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmailVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions for testing
        $this->createRolesAndPermissions();
        
        $this->controller = new EmailVerificationController();
        Notification::fake();
    }

    private function createRolesAndPermissions()
    {
        // Create customer role
        Role::create([
            'name' => 'customer',
            'guard_name' => 'web'
        ]);

        // Create admin role
        Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        // Create super_admin role
        Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);
    }

    /** @test */
    public function notice_method_returns_correct_view_for_unverified_user()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $request = Request::create('/email/verify');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->notice($request);

        $this->assertEquals('auth.verify-email', $response->getName());
    }

    /** @test */
    public function notice_method_redirects_verified_user_based_on_role()
    {
        // Test customer role
        $customer = User::factory()->create([
            'email_verified_at' => now()
        ]);
        $customer->assignRole('customer');

        $request = Request::create('/email/verify');
        $request->setUserResolver(function () use ($customer) {
            return $customer;
        });

        $response = $this->controller->notice($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('/packages', $response->getTargetUrl());

        // Test admin role
        $admin = User::factory()->create([
            'email_verified_at' => now()
        ]);
        $admin->assignRole('admin');

        $request->setUserResolver(function () use ($admin) {
            return $admin;
        });

        $response = $this->controller->notice($request);
        $this->assertStringContains('/admin', $response->getTargetUrl());
    }

    /** @test */
    public function verify_method_successfully_verifies_user_with_valid_data()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);
        $user->assignRole('customer');

        $request = Request::create('/email/verify/' . $user->id . '/' . sha1($user->email));
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->route()->setParameter('id', $user->id);
        $request->route()->setParameter('hash', sha1($user->email));

        $response = $this->controller->verify($request);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertEquals(302, $response->getStatusCode());
    }

    /** @test */
    public function verify_method_fails_with_invalid_hash()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $request = Request::create('/email/verify/' . $user->id . '/invalid-hash');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });
        $request->route()->setParameter('id', $user->id);
        $request->route()->setParameter('hash', 'invalid-hash');

        $response = $this->controller->verify($request);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertStringContains('verification.notice', $response->getTargetUrl());
    }

    /** @test */
    public function resend_method_sends_notification_for_unverified_user()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $request = Request::create('/email/verification-notification', 'POST');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->resend($request);

        Notification::assertSentTo(
            $user,
            EmailVerificationNotification::class
        );

        $responseData = json_decode($response->getContent(), true);
        $this->assertTrue($responseData['success']);
        $this->assertEquals('Email verifikasi telah dikirim ulang!', $responseData['message']);
    }

    /** @test */
    public function resend_method_fails_for_verified_user()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $request = Request::create('/email/verification-notification', 'POST');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $response = $this->controller->resend($request);

        Notification::assertNothingSent();

        $responseData = json_decode($response->getContent(), true);
        $this->assertFalse($responseData['success']);
        $this->assertEquals('Email Anda sudah terverifikasi.', $responseData['message']);
    }

    /** @test */
    public function get_redirect_url_by_role_returns_correct_urls()
    {
        // Test customer role
        $customer = User::factory()->create();
        $customer->assignRole('customer');

        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('getRedirectUrlByRole');
        $method->setAccessible(true);

        $url = $method->invoke($this->controller, $customer);
        $this->assertEquals('/packages', $url);

        // Test admin role
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $url = $method->invoke($this->controller, $admin);
        $this->assertEquals('/admin', $url);

        // Test super_admin role
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $url = $method->invoke($this->controller, $superAdmin);
        $this->assertEquals('/admin', $url);

        // Test user without role
        $userWithoutRole = User::factory()->create();

        $url = $method->invoke($this->controller, $userWithoutRole);
        $this->assertEquals('/packages', $url);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}