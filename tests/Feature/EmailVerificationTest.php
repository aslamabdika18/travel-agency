<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions for testing
        $this->createRolesAndPermissions();

        // Fake notifications dan events untuk testing
        Notification::fake();
        Event::fake();
    }

    private function createRolesAndPermissions()
    {
        // Create customer role
        $customerRole = Role::create([
            'name' => 'customer',
            'guard_name' => 'web'
        ]);

        // Create admin role
        $adminRole = Role::create([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        // Create super_admin role
        $superAdminRole = Role::create([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);
    }

    /** @test */
    public function user_can_view_email_verification_notice_page()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify-email');
    }

    /** @test */
    public function verified_user_is_redirected_from_verification_notice()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);
        $user->assignRole('customer');

        $response = $this->actingAs($user)
            ->get(route('verification.notice'));

        $response->assertRedirect('/packages');
    }

    /** @test */
    public function user_can_verify_email_with_valid_link()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);
        $user->assignRole('customer');

        // Generate signed URL untuk verifikasi
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)
            ->get($verificationUrl);

        // Assert user email sudah terverifikasi
        $this->assertTrue($user->fresh()->hasVerifiedEmail());

        // Assert event Verified dipicu
        Event::assertDispatched(Verified::class);

        // Assert redirect ke halaman yang sesuai dengan role
        $response->assertRedirect('/packages');
        $response->assertSessionHas('toast_success');
    }

    /** @test */
    public function user_cannot_verify_email_with_invalid_hash()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        // Generate URL dengan hash yang salah
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->actingAs($user)
            ->get($verificationUrl);

        // Assert user email belum terverifikasi
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        // Assert redirect ke verification notice dengan error
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('toast_error');
    }

    /** @test */
    public function user_cannot_verify_email_with_expired_link()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        // Generate expired URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subMinutes(60), // Expired 1 hour ago
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)
            ->get($verificationUrl);

        // Assert user email belum terverifikasi
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        // Assert redirect dengan error
        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('toast_error');
    }

    /** @test */
    public function user_can_resend_verification_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->actingAs($user)
            ->post(route('verification.send'));

        // Assert notification dikirim
        Notification::assertSentTo(
            $user,
            EmailVerificationNotification::class
        );

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Email verifikasi telah dikirim ulang!'
        ]);
    }

    /** @test */
    public function verified_user_cannot_resend_verification_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $response = $this->actingAs($user)
            ->post(route('verification.send'));

        // Assert tidak ada notification yang dikirim
        Notification::assertNothingSent();

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Email Anda sudah terverifikasi.'
        ]);
    }

    /** @test */
    public function registration_sends_email_verification_notification()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->post(route('auth.register'), $userData);

        // Assert user dibuat
        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertFalse($user->hasVerifiedEmail());

        // Assert notification dikirim
        Notification::assertSentTo(
            $user,
            EmailVerificationNotification::class
        );

        // Assert redirect ke verification notice
        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function unverified_user_cannot_access_protected_routes()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);
        $user->assignRole('customer');

        // Test akses ke route yang memerlukan verifikasi
        $response = $this->actingAs($user)
            ->get('/booking');

        $response->assertRedirect(route('verification.notice'));
        $response->assertSessionHas('toast_warning');
    }

    /** @test */
    public function verified_user_can_access_protected_routes()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);
        $user->assignRole('customer');

        // Test akses ke route yang memerlukan verifikasi
        $response = $this->actingAs($user)
            ->get('/booking');

        // Booking route redirects to travel package detail, so expect redirect
        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /** @test */
    public function ajax_request_for_unverified_user_returns_json_error()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);
        $user->assignRole('customer');

        $response = $this->actingAs($user)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get('/user/bookings'); // Use a route that returns a view instead of redirect

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Email Anda belum diverifikasi. Silakan verifikasi email terlebih dahulu.',
            'redirect_url' => route('verification.notice')
        ]);
    }

    /** @test */
    public function guest_user_cannot_access_verification_routes()
    {
        // Test verification notice
        $response = $this->get(route('verification.notice'));
        $response->assertRedirect('/auth');

        // Test resend verification
        $response = $this->post(route('verification.send'));
        $response->assertRedirect('/auth');
    }

    /** @test */
    public function user_model_sends_custom_verification_notification()
    {
        $user = User::factory()->create();

        // Test method sendEmailVerificationNotification
        $user->sendEmailVerificationNotification();

        // Assert custom notification dikirim
        Notification::assertSentTo(
            $user,
            EmailVerificationNotification::class
        );
    }
}