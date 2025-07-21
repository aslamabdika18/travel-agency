<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;
use App\Models\Booking;

class PaymentPendingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->booking = $payment->booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $expiryTime = $this->payment->created_at->addHours(24)->format('d M Y H:i');

        return (new MailMessage)
            ->subject('Menunggu Pembayaran - ' . $this->booking->booking_reference)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Booking Anda telah dibuat dan menunggu pembayaran.')
            ->line('')
            ->line('**Detail Booking:**')
            ->line('Referensi Booking: ' . $this->booking->booking_reference)
            ->line('Paket Wisata: ' . ($this->booking->travelPackage?->name ?? 'N/A'))
            ->line('Total Pembayaran: ' . formatRupiah($this->payment->total_price))
            ->line('Status: Menunggu Pembayaran')
            ->line('Batas Waktu Pembayaran: ' . $expiryTime)
            ->line('')
            ->line('Silakan selesaikan pembayaran Anda sebelum batas waktu yang ditentukan.')
            ->line('Jika pembayaran tidak diselesaikan dalam 24 jam, booking akan dibatalkan secara otomatis.')
            ->action('Lanjutkan Pembayaran', route('payment.continue', $this->payment->id))
            ->line('Terima kasih telah memilih layanan travel kami!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_pending',
            'payment_id' => $this->payment->id,
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'travel_package_name' => $this->booking->travelPackage?->name,
            'amount' => $this->payment->total_price,
            'gateway_status' => $this->payment->gateway_status,
            'expiry_time' => $this->payment->created_at->addHours(24),
            'formatted_amount' => formatRupiah($this->payment->total_price),
            'message' => "Booking {$this->booking->booking_reference} menunggu pembayaran sebesar " . formatRupiah($this->payment->total_price) . ". Silakan selesaikan pembayaran dalam 24 jam."
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
