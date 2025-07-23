<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use App\Models\Payment;
use App\Models\Booking;
use Exception;
use Throwable;

class InvoiceLogger
{
    /**
     * Log invoice generation start
     */
    public static function logGenerationStart(Payment $payment, array $context = [])
    {
        if (!self::isLoggingEnabled('log_generation')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_generation_start',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'amount' => $payment->amount,
            'status' => $payment->status,
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice')->info('Invoice generation started', $logData);
    }

    /**
     * Log invoice generation success
     */
    public static function logGenerationSuccess(Payment $payment, string $filePath, float $duration, array $context = [])
    {
        if (!self::isLoggingEnabled('log_generation')) {
            return;
        }

        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

        $logData = array_merge([
            'event' => 'invoice_generation_success',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'file_path' => $filePath,
            'file_size_bytes' => $fileSize,
            'file_size_mb' => round($fileSize / 1024 / 1024, 2),
            'generation_time_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice')->info('Invoice generated successfully', $logData);

        // Log performance if it's slow
        if ($duration > (Config::get('invoice.logging.slow_request_threshold', 5000) / 1000)) {
            self::logPerformanceIssue('slow_generation', $payment, $duration, $context);
        }
    }

    /**
     * Log invoice generation error
     */
    public static function logGenerationError(Payment $payment, Throwable $exception, array $context = [])
    {
        if (!self::isLoggingEnabled('log_errors')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_generation_error',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice_errors')->error('Invoice generation failed', $logData);
    }

    /**
     * Log invoice download start
     */
    public static function logDownloadStart(Payment $payment, array $context = [])
    {
        if (!self::isLoggingEnabled('log_downloads')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_download_start',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->header('User-Agent'),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice')->info('Invoice download started', $logData);
    }

    /**
     * Log invoice download success
     */
    public static function logDownloadSuccess(Payment $payment, string $filePath, float $duration, array $context = [])
    {
        if (!self::isLoggingEnabled('log_downloads')) {
            return;
        }

        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

        $logData = array_merge([
            'event' => 'invoice_download_success',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'file_path' => $filePath,
            'file_size_bytes' => $fileSize,
            'file_size_mb' => round($fileSize / 1024 / 1024, 2),
            'download_time_ms' => round($duration * 1000, 2),
            'ip_address' => request()?->ip(),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice')->info('Invoice downloaded successfully', $logData);
    }

    /**
     * Log invoice download error
     */
    public static function logDownloadError(Payment $payment, Throwable $exception, array $context = [])
    {
        if (!self::isLoggingEnabled('log_errors')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_download_error',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
            'ip_address' => request()?->ip(),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice_errors')->error('Invoice download failed', $logData);
    }

    /**
     * Log email attachment start
     */
    public static function logEmailAttachmentStart(Payment $payment, array $context = [])
    {
        if (!self::isLoggingEnabled('log_generation')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_email_attachment_start',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'recipient_email' => $payment->booking?->user?->email,
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice')->info('Invoice email attachment started', $logData);
    }

    /**
     * Log email attachment success
     */
    public static function logEmailAttachmentSuccess(Payment $payment, string $filePath, float $duration, array $context = [])
    {
        if (!self::isLoggingEnabled('log_generation')) {
            return;
        }

        $fileSize = file_exists($filePath) ? filesize($filePath) : 0;

        $logData = array_merge([
            'event' => 'invoice_email_attachment_success',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'recipient_email' => $payment->booking?->user?->email,
            'file_path' => $filePath,
            'file_size_bytes' => $fileSize,
            'file_size_mb' => round($fileSize / 1024 / 1024, 2),
            'attachment_time_ms' => round($duration * 1000, 2),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice')->info('Invoice attached to email successfully', $logData);
    }

    /**
     * Log email attachment error
     */
    public static function logEmailAttachmentError(Payment $payment, Throwable $exception, array $context = [])
    {
        if (!self::isLoggingEnabled('log_errors')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_email_attachment_error',
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'recipient_email' => $payment->booking?->user?->email,
            'error_message' => $exception->getMessage(),
            'error_code' => $exception->getCode(),
            'error_file' => $exception->getFile(),
            'error_line' => $exception->getLine(),
            'stack_trace' => $exception->getTraceAsString(),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice_errors')->error('Invoice email attachment failed', $logData);
    }

    /**
     * Log performance issues
     */
    public static function logPerformanceIssue(string $type, Payment $payment, float $duration, array $context = [])
    {
        if (!self::isLoggingEnabled('log_performance')) {
            return;
        }

        $logData = array_merge([
            'event' => 'invoice_performance_issue',
            'issue_type' => $type,
            'payment_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'booking_reference' => $payment->booking?->booking_reference,
            'user_id' => $payment->booking?->user_id,
            'duration_ms' => round($duration * 1000, 2),
            'threshold_ms' => Config::get('invoice.logging.slow_request_threshold', 5000),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'timestamp' => now()->toISOString(),
        ], $context);

        Log::channel('invoice_performance')->warning('Invoice performance issue detected', $logData);
    }

    /**
     * Log system events
     */
    public static function logSystemEvent(string $event, array $data = [])
    {
        if (!self::isLoggingEnabled()) {
            return;
        }

        $logData = array_merge([
            'event' => $event,
            'timestamp' => now()->toISOString(),
        ], $data);

        Log::channel('invoice')->info('Invoice system event', $logData);
    }

    /**
     * Log maintenance activities
     */
    public static function logMaintenance(string $activity, array $data = [])
    {
        $logData = array_merge([
            'event' => 'invoice_maintenance',
            'activity' => $activity,
            'timestamp' => now()->toISOString(),
        ], $data);

        Log::channel('invoice')->info('Invoice maintenance activity', $logData);
    }

    /**
     * Check if logging is enabled for specific type
     */
    private static function isLoggingEnabled(string $type = null): bool
    {
        $enabled = Config::get('invoice.logging.enabled', true);
        
        if (!$enabled) {
            return false;
        }

        if ($type) {
            return Config::get("invoice.logging.{$type}", true);
        }

        return true;
    }

    /**
     * Get logging statistics
     */
    public static function getStatistics(): array
    {
        $logPath = storage_path('logs');
        $stats = [
            'invoice_log_size' => 0,
            'error_log_size' => 0,
            'performance_log_size' => 0,
            'total_log_files' => 0,
        ];

        // Get invoice log files
        $invoiceFiles = glob($logPath . '/invoice-*.log');
        if ($invoiceFiles) {
            $stats['total_log_files'] += count($invoiceFiles);
            foreach ($invoiceFiles as $file) {
                $stats['invoice_log_size'] += filesize($file);
            }
        }

        // Get error log files
        $errorFiles = glob($logPath . '/invoice-errors-*.log');
        if ($errorFiles) {
            $stats['total_log_files'] += count($errorFiles);
            foreach ($errorFiles as $file) {
                $stats['error_log_size'] += filesize($file);
            }
        }

        // Get performance log files
        $performanceFiles = glob($logPath . '/invoice-performance-*.log');
        if ($performanceFiles) {
            $stats['total_log_files'] += count($performanceFiles);
            foreach ($performanceFiles as $file) {
                $stats['performance_log_size'] += filesize($file);
            }
        }

        // Convert to human readable
        $stats['invoice_log_size_mb'] = round($stats['invoice_log_size'] / 1024 / 1024, 2);
        $stats['error_log_size_mb'] = round($stats['error_log_size'] / 1024 / 1024, 2);
        $stats['performance_log_size_mb'] = round($stats['performance_log_size'] / 1024 / 1024, 2);
        $stats['total_size_mb'] = $stats['invoice_log_size_mb'] + $stats['error_log_size_mb'] + $stats['performance_log_size_mb'];

        return $stats;
    }
}