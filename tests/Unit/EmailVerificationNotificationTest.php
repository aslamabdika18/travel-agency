<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function notification_implements_should_queue()
    {
        $notification = new EmailVerificationNotification();
        
        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            $notification
        );
    }

    /** @test */
    public function notification_uses_correct_queue_connection()
    {
        $notification = new EmailVerificationNotification();
        
        $this->assertEquals('database', $notification->connection);
    }

    /** @test */
    public function notification_uses_correct_queue_name()
    {
        $notification = new EmailVerificationNotification();
        
        $this->assertEquals('emails', $notification->queue);
    }

    /** @test */
    public function notification_has_correct_delay()
    {
        $notification = new EmailVerificationNotification();
        
        $this->assertEquals(5, $notification->delay);
    }

    /** @test */
    public function to_mail_method_returns_correct_mail_message()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        $notification = new EmailVerificationNotification();
        $mailMessage = $notification->toMail($user);

        // Test mail message properties
        $this->assertEquals('Verifikasi Alamat Email Anda', $mailMessage->subject);
        $this->assertEquals('mail.email-verification', $mailMessage->view);
        $this->assertEquals(['test@example.com'], $mailMessage->to);
        
        // Test view data
        $this->assertArrayHasKey('user', $mailMessage->viewData);
        $this->assertArrayHasKey('verificationUrl', $mailMessage->viewData);
        $this->assertEquals($user->toArray(), $mailMessage->viewData['user']);
        
        // Test verification URL structure
        $verificationUrl = $mailMessage->viewData['verificationUrl'];
        $this->assertStringContains('/email/verify/', $verificationUrl);
        $this->assertStringContains((string)$user->id, $verificationUrl);
        $this->assertStringContains(sha1($user->email), $verificationUrl);
        $this->assertStringContains('expires=', $verificationUrl);
        $this->assertStringContains('signature=', $verificationUrl);
    }

    /** @test */
    public function verification_url_is_properly_signed()
    {
        $user = User::factory()->create();
        $notification = new EmailVerificationNotification();
        $mailMessage = $notification->toMail($user);
        
        $verificationUrl = $mailMessage->viewData['verificationUrl'];
        
        // Parse URL to check if it's properly signed
        $parsedUrl = parse_url($verificationUrl);
        parse_str($parsedUrl['query'], $queryParams);
        
        $this->assertArrayHasKey('expires', $queryParams);
        $this->assertArrayHasKey('signature', $queryParams);
        
        // Check if expires is in the future
        $expiresAt = Carbon::createFromTimestamp($queryParams['expires']);
        $this->assertTrue($expiresAt->isFuture());
        
        // Check if expires is approximately 60 minutes from now (with 1 minute tolerance)
        $expectedExpiry = now()->addMinutes(60);
        $this->assertTrue($expiresAt->between(
            $expectedExpiry->subMinute(),
            $expectedExpiry->addMinute()
        ));
    }

    /** @test */
    public function verification_url_contains_correct_route_parameters()
    {
        $user = User::factory()->create();
        $notification = new EmailVerificationNotification();
        $mailMessage = $notification->toMail($user);
        
        $verificationUrl = $mailMessage->viewData['verificationUrl'];
        
        // Extract route parameters from URL
        $pattern = '/\/email\/verify\/(\d+)\/([a-f0-9]{40})/';
        preg_match($pattern, $verificationUrl, $matches);
        
        $this->assertCount(3, $matches); // Full match + 2 groups
        $this->assertEquals($user->id, (int)$matches[1]);
        $this->assertEquals(sha1($user->email), $matches[2]);
    }

    /** @test */
    public function notification_can_be_sent_to_user()
    {
        $user = User::factory()->create();
        
        // Test that notification can be sent without errors
        $this->expectNotToPerformAssertions();
        
        $user->notify(new EmailVerificationNotification());
    }

    /** @test */
    public function notification_extends_laravel_verify_email()
    {
        $notification = new EmailVerificationNotification();
        
        $this->assertInstanceOf(
            \Illuminate\Auth\Notifications\VerifyEmail::class,
            $notification
        );
    }

    /** @test */
    public function notification_via_method_returns_mail_channel()
    {
        $user = User::factory()->create();
        $notification = new EmailVerificationNotification();
        
        $channels = $notification->via($user);
        
        $this->assertIsArray($channels);
        $this->assertContains('mail', $channels);
    }

    /** @test */
    public function mail_message_has_correct_from_address()
    {
        $user = User::factory()->create();
        $notification = new EmailVerificationNotification();
        $mailMessage = $notification->toMail($user);
        
        // Check if from address is set (should use default from config)
        $this->assertNotEmpty($mailMessage->from);
    }

    /** @test */
    public function mail_message_view_data_contains_required_fields()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        $notification = new EmailVerificationNotification();
        $mailMessage = $notification->toMail($user);
        
        $viewData = $mailMessage->viewData;
        
        // Check required fields
        $this->assertArrayHasKey('user', $viewData);
        $this->assertArrayHasKey('verificationUrl', $viewData);
        
        // Check user data
        $userData = $viewData['user'];
        $this->assertEquals('John Doe', $userData['name']);
        $this->assertEquals('john@example.com', $userData['email']);
        
        // Check verification URL is not empty
        $this->assertNotEmpty($viewData['verificationUrl']);
        $this->assertIsString($viewData['verificationUrl']);
    }
}