<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Payment;
use App\Models\Booking;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Helpers\InvoiceLogger;

class PaymentSuccessNotification extends Notification implements ShouldQueue
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
        $mailMessage = (new MailMessage)
            ->subject('Pembayaran Berhasil - ' . $this->booking->booking_reference)
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Pembayaran Anda telah berhasil diproses!')
            ->line('')
            ->line('**Detail Booking:**')
            ->line('Referensi Booking: ' . $this->booking->booking_reference)
            ->line('Paket Wisata: ' . ($this->booking->travelPackage?->name ?? 'N/A'))
            ->line('Total Pembayaran: ' . formatRupiah($this->payment->total_price))
            ->line('Tanggal Pembayaran: ' . $this->payment->payment_date?->format('d M Y H:i'))
            ->line('Status: Pembayaran Berhasil')
            ->line('')
            ->line('Booking Anda telah dikonfirmasi. Kami akan mengirimkan detail perjalanan lebih lanjut melalui email terpisah.')
            ->line('')
            ->line('**Invoice PDF terlampir pada email ini untuk referensi Anda.**')
            ->action('Lihat Detail Booking', route('booking.detail', $this->booking->id))
            ->line('Terima kasih telah memilih layanan travel kami!');
        
        // Generate dan attach PDF invoice
        $attachmentStartTime = microtime(true);
        
        InvoiceLogger::logEmailAttachmentStart($this->payment);
        
        try {
            $invoiceService = new InvoiceService();
            
            // Check if service is properly configured
            if (!$invoiceService->isConfigured()) {
                Log::error('Invoice service not properly configured', [
                    'payment_id' => $this->payment->id
                ]);
                return $mailMessage;
            }
            
            // Check for existing invoice first
            $existingPath = $invoiceService->getInvoiceFilePath($this->payment);
            
            if ($existingPath && Storage::disk('public')->exists($existingPath)) {
                $pdfPath = $existingPath;
                Log::info('Using existing invoice file for email attachment', [
                    'payment_id' => $this->payment->id,
                    'existing_path' => $existingPath
                ]);
            } else {
                Log::info('Generating new invoice PDF for email attachment', [
                    'payment_id' => $this->payment->id
                ]);
                
                $pdfPath = $invoiceService->generateInvoicePdf($this->payment);
            }
            
            if (!$pdfPath) {
                Log::error('Failed to generate or retrieve invoice PDF', [
                    'payment_id' => $this->payment->id,
                    'booking_reference' => $this->booking->booking_reference
                ]);
                return $mailMessage;
            }
            
            // Verify file exists and is readable
            if (!Storage::disk('public')->exists($pdfPath)) {
                Log::error('Invoice PDF file not found in storage', [
                    'payment_id' => $this->payment->id,
                    'pdf_path' => $pdfPath
                ]);
                return $mailMessage;
            }
            
            $fullPath = Storage::disk('public')->path($pdfPath);
            $fileName = 'Invoice-' . $this->booking->booking_reference . '.pdf';
            $fileSize = Storage::disk('public')->size($pdfPath);
            
            // Verify physical file exists
            if (!file_exists($fullPath)) {
                Log::error('Invoice PDF physical file not found', [
                    'payment_id' => $this->payment->id,
                    'full_path' => $fullPath,
                    'pdf_path' => $pdfPath
                ]);
                return $mailMessage;
            }
            
            // Check file size
            if ($fileSize === 0) {
                Log::error('Invoice PDF file is empty', [
                    'payment_id' => $this->payment->id,
                    'pdf_path' => $pdfPath
                ]);
                return $mailMessage;
            }
            
            Log::info('Attaching invoice PDF to email', [
                'payment_id' => $this->payment->id,
                'file_path' => $pdfPath,
                'file_size_bytes' => $fileSize,
                'file_size_kb' => round($fileSize / 1024, 2),
                'attachment_name' => $fileName
            ]);
            
            try {
                $mailMessage->attach($fullPath, [
                    'as' => $fileName,
                    'mime' => 'application/pdf',
                ]);
                
                $attachmentEndTime = microtime(true);
                $attachmentTime = $attachmentEndTime - $attachmentStartTime;
                
                InvoiceLogger::logEmailAttachmentSuccess($this->payment, $fullPath, $attachmentTime);
                
            } catch (\Exception $attachException) {
                Log::error('Failed to attach PDF to email message', [
                    'payment_id' => $this->payment->id,
                    'pdf_path' => $pdfPath,
                    'error' => $attachException->getMessage(),
                    'trace' => $attachException->getTraceAsString()
                ]);
            }
            
        } catch (\Exception $e) {
            InvoiceLogger::logEmailAttachmentError($this->payment, $e);
        }
        
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
            'type' => 'payment_success',
            'payment_id' => $this->payment->id,
            'booking_id' => $this->booking->id,
            'booking_reference' => $this->booking->booking_reference,
            'travel_package_name' => $this->booking->travelPackage?->name,
            'amount' => $this->payment->total_price,
            'payment_date' => $this->payment->payment_date,
            'gateway_status' => $this->payment->gateway_status,
            'formatted_amount' => formatRupiah($this->payment->total_price),
            'message' => "Pembayaran sebesar " . formatRupiah($this->payment->total_price) . " untuk booking {$this->booking->booking_reference} telah berhasil diproses."
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
