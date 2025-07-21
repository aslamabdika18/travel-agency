<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;
use App\Models\Booking;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $booking;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payment $payment, ?string $reason = null)
    {
        $this->payment = $payment;
        $this->booking = $payment->booking;
        $this->reason = $reason ?? $this->getFailureReason($payment->gateway_status);
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
        return (new MailMessage)
            ->subject('Pembayaran Gagal - ' . $this->booking->booking_reference)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Maaf, pembayaran Anda tidak dapat diproses.')
            ->line('')
            ->line('**Detail Booking:**')
            ->line('Referensi Booking: ' . $this->booking->booking_reference)
            ->line('Paket Wisata: ' . ($this->booking->travelPackage?->name ?? 'N/A'))
            ->line('Total Pembayaran: ' . formatRupiah($this->payment->total_price))
            ->line('Status: Pembayaran Gagal')
            ->line('Alasan: ' . $this->reason)
            ->line('')
            ->line('Anda dapat mencoba melakukan pembayaran ulang dengan metode pembayaran yang berbeda.')
            ->line('Jika masalah berlanjut, silakan hubungi customer service kami.')
            ->action('Coba Bayar Lagi', route('payment.retry', $this->payment->id))
            ->line('Terima kasih atas pengertian Anda.');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'payment_failed',
            'payment_id' => $this->payment->id,
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'travel_package_name' => $this->booking->travelPackage?->name,
            'amount' => $this->payment->total_price,
            'gateway_status' => $this->payment->gateway_status,
            'failure_reason' => $this->reason,
            'formatted_amount' => formatRupiah($this->payment->total_price),
            'message' => "Pembayaran untuk booking {$this->booking->booking_reference} gagal diproses. Alasan: {$this->reason}"
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

    /**
     * Get failure reason based on gateway status
     */
    private function getFailureReason(string $gatewayStatus): string
    {
        return match($gatewayStatus) {
            'deny' => 'Pembayaran ditolak oleh bank atau sistem deteksi fraud',
            'cancel' => 'Transaksi dibatalkan',
            'expire' => 'Waktu pembayaran telah habis',
            'failure' => 'Terjadi kesalahan sistem saat memproses pembayaran',
            default => 'Pembayaran tidak dapat diproses'
        };
    }
}
