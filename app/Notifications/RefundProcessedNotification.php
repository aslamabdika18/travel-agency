<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;

class RefundProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    protected $refundAmount;
    protected $refundPercentage;
    protected $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking, float $refundAmount, int $refundPercentage, ?string $reason = null)
    {
        $this->booking = $booking;
        $this->refundAmount = $refundAmount;
        $this->refundPercentage = $refundPercentage;
        $this->reason = $reason;
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
        $mailMessage = (new MailMessage)
            ->subject('Refund Processed - ' . $this->booking->booking_reference)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your refund request has been processed successfully.')
            ->line('**Booking Details:**')
            ->line('Booking Reference: ' . $this->booking->booking_reference)
            ->line('Travel Package: ' . ($this->booking->travelPackage?->name ?? 'N/A'))
            ->line('Original Amount: ' . formatRupiah($this->booking->total_price))
            ->line('Refund Percentage: ' . $this->refundPercentage . '%')
            ->line('Refund Amount: ' . formatRupiah($this->refundAmount))
            ->line('Booking Date: ' . $this->booking->booking_date?->format('d M Y'))
            ->line('');

        if ($this->reason) {
            $mailMessage->line('**Refund Reason:**')
                        ->line($this->reason)
                        ->line('');
        }

        $mailMessage->line('The refund will be processed back to your original payment method within 3-7 business days.')
                    ->line('If you have any questions, please contact our customer service.')
                    ->action('View Booking Details', route('booking.detail', $this->booking->id))
                    ->line('Thank you for choosing our travel services.');

        return $mailMessage;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'refund_processed',
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'travel_package_name' => $this->booking->travelPackage?->name,
            'original_amount' => $this->booking->total_price,
            'refund_amount' => $this->refundAmount,
            'refund_percentage' => $this->refundPercentage,
            'reason' => $this->reason,
            'formatted_original_amount' => formatRupiah($this->booking->total_price),
            'formatted_refund_amount' => formatRupiah($this->refundAmount),
            'message' => "Your refund of {$this->refundPercentage}% (" . formatRupiah($this->refundAmount) . ") for booking {$this->booking->booking_reference} has been processed."
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