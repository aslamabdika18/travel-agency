<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\TravelPackage;
use App\Services\MidtransService;
use App\Notifications\PaymentSuccessNotification;
use App\Notifications\PaymentPendingNotification;
use App\Notifications\PaymentFailedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class PaymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $travelPackage;
    protected $booking;
    protected $payment;
    protected $midtransService;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake notifications untuk testing
        Notification::fake();

        // Buat test data
        $this->user = User::factory()->create();
        $this->travelPackage = TravelPackage::factory()->create();
        $this->booking = Booking::factory()->create([
            'user_id' => $this->user->id,
            'travel_package_id' => $this->travelPackage->id,
        ]);
        $this->payment = Payment::factory()->create([
            'booking_id' => $this->booking->id,
        ]);

        $this->midtransService = new MidtransService();
    }

    /** @test */
    public function it_sends_success_notification_for_settlement_status()
    {
        // Simulasi notifikasi Midtrans untuk status settlement
        $notificationData = [
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi success dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentSuccessNotification::class
        );
    }

    /** @test */
    public function it_sends_pending_notification_for_pending_status()
    {
        // Simulasi notifikasi Midtrans untuk status pending
        $notificationData = [
            'transaction_status' => 'pending',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi pending dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentPendingNotification::class
        );
    }

    /** @test */
    public function it_sends_failed_notification_for_deny_status()
    {
        // Simulasi notifikasi Midtrans untuk status deny
        $notificationData = [
            'transaction_status' => 'deny',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi failed dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentFailedNotification::class
        );
    }

    /** @test */
    public function it_sends_failed_notification_for_cancel_status()
    {
        // Simulasi notifikasi Midtrans untuk status cancel
        $notificationData = [
            'transaction_status' => 'cancel',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi failed dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentFailedNotification::class
        );
    }

    /** @test */
    public function it_sends_failed_notification_for_expire_status()
    {
        // Simulasi notifikasi Midtrans untuk status expire
        $notificationData = [
            'transaction_status' => 'expire',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi failed dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentFailedNotification::class
        );
    }

    /** @test */
    public function it_sends_failed_notification_for_failure_status()
    {
        // Simulasi notifikasi Midtrans untuk status failure
        $notificationData = [
            'transaction_status' => 'failure',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi failed dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentFailedNotification::class
        );
    }

    /** @test */
    public function it_sends_success_notification_for_capture_status()
    {
        // Simulasi notifikasi Midtrans untuk status capture
        $notificationData = [
            'transaction_status' => 'capture',
            'fraud_status' => 'accept',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi success dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentSuccessNotification::class
        );
    }

    /** @test */
    public function it_sends_pending_notification_for_challenge_fraud_status()
    {
        // Simulasi notifikasi Midtrans untuk fraud status challenge
        $notificationData = [
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
            'order_id' => 'BOOKING-' . $this->booking->id . '-' . time(),
        ];

        // Update payment status
        $this->midtransService->updatePaymentStatus(
            $this->payment, 
            $notificationData['transaction_status'], 
            $notificationData['fraud_status'] ?? null, 
            $notificationData
        );

        // Assert notifikasi pending dikirim
        Notification::assertSentTo(
            $this->user,
            PaymentPendingNotification::class
        );
    }
}
