<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Helpers\InvoiceLogger;

class InvoiceService
{
    /**
     * Check if invoice service is properly configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        try {
            // Check if storage disk is accessible
            $storageAccessible = Storage::disk('public')->exists('.');
            
            // Check if invoices directory is writable
            $invoicesWritable = true;
            if (!Storage::disk('public')->exists('invoices')) {
                try {
                    Storage::disk('public')->makeDirectory('invoices');
                } catch (\Exception $e) {
                    $invoicesWritable = false;
                }
            }
            
            $configured = $storageAccessible && $invoicesWritable;
            
            Log::info('Invoice service configuration check', [
                'storage_accessible' => $storageAccessible,
                'invoices_writable' => $invoicesWritable,
                'configured' => $configured
            ]);
            
            return $configured;
            
        } catch (\Exception $e) {
            Log::error('Error checking invoice service configuration', [
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get invoice statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        try {
            $invoicesPath = 'invoices';
            
            if (!Storage::disk('public')->exists($invoicesPath)) {
                return [
                    'total_files' => 0,
                    'total_size_bytes' => 0,
                    'total_size_mb' => 0
                ];
            }
            
            $files = Storage::disk('public')->files($invoicesPath);
            $totalSize = 0;
            
            foreach ($files as $file) {
                $totalSize += Storage::disk('public')->size($file);
            }
            
            $stats = [
                'total_files' => count($files),
                'total_size_bytes' => $totalSize,
                'total_size_mb' => round($totalSize / (1024 * 1024), 2)
            ];
            
            Log::info('Invoice statistics retrieved', $stats);
            
            return $stats;
            
        } catch (\Exception $e) {
            Log::error('Error retrieving invoice statistics', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'total_files' => 0,
                'total_size_bytes' => 0,
                'total_size_mb' => 0,
                'error' => $e->getMessage()
            ];
        }
    }
    /**
     * Generate PDF invoice for payment
     *
     * @param Payment $payment
     * @return string|null Path to generated PDF file
     */
    public function generateInvoicePdf(Payment $payment): ?string
    {
        $startTime = microtime(true);
        
        InvoiceLogger::logGenerationStart($payment);
        
        try {
            // Validasi payment status
            if (!$payment->isPaid()) {
                Log::warning('Attempted to generate invoice for unpaid payment', [
                    'payment_id' => $payment->id,
                    'payment_status' => $payment->payment_status
                ]);
                return null;
            }
            
            // Load relasi yang diperlukan
            $payment->load(['booking.user', 'booking.travelPackage']);
            
            $booking = $payment->booking;
            $user = $booking->user;
            $travelPackage = $booking->travelPackage;
            
            // Validasi data yang diperlukan
            if (!$booking || !$user || !$travelPackage) {
                Log::error('Missing required data for invoice generation', [
                    'payment_id' => $payment->id,
                    'has_booking' => !is_null($booking),
                    'has_user' => !is_null($user),
                    'has_travel_package' => !is_null($travelPackage)
                ]);
                return null;
            }
            
            Log::info('Invoice data validation passed', [
                'payment_id' => $payment->id,
                'booking_reference' => $booking->booking_reference,
                'user_id' => $user->id,
                'travel_package_id' => $travelPackage->id
            ]);
            
            // Data untuk invoice
            $invoiceData = [
                'invoice_number' => $this->generateInvoiceNumber($payment),
                'invoice_date' => now()->format('d M Y'),
                'payment' => $payment,
                'booking' => $booking,
                'user' => $user,
                'travel_package' => $travelPackage,
                'company' => [
                    'name' => config('app.name', 'Travel Agency'),
                    'address' => 'Jl. Contoh No. 123, Jakarta',
                    'phone' => '+62 21 1234 5678',
                    'email' => 'info@travelagency.com',
                    'website' => 'www.travelagency.com'
                ]
            ];
            
            Log::info('Generating PDF with DomPDF', [
                'payment_id' => $payment->id,
                'invoice_number' => $invoiceData['invoice_number']
            ]);
            
            // Generate PDF
            $pdf = Pdf::loadView('invoices.payment-invoice', $invoiceData);
            $pdf->setPaper('A4', 'portrait');
            
            // Nama file
            $fileName = 'invoice-' . $booking->booking_reference . '-' . now()->format('YmdHis') . '.pdf';
            $filePath = 'invoices/' . $fileName;
            
            // Pastikan direktori ada
            if (!Storage::disk('public')->exists('invoices')) {
                Storage::disk('public')->makeDirectory('invoices');
                Log::info('Created invoices directory', ['path' => 'invoices']);
            }
            
            // Generate PDF output
            $pdfOutput = $pdf->output();
            $fileSize = strlen($pdfOutput);
            
            Log::info('PDF generated successfully', [
                'payment_id' => $payment->id,
                'file_size_bytes' => $fileSize,
                'file_size_kb' => round($fileSize / 1024, 2)
            ]);
            
            // Simpan ke storage
            $saved = Storage::disk('public')->put($filePath, $pdfOutput);
            
            if (!$saved) {
                Log::error('Failed to save PDF to storage', [
                    'payment_id' => $payment->id,
                    'file_path' => $filePath
                ]);
                return null;
            }
            
            // Verifikasi file tersimpan
            if (!Storage::disk('public')->exists($filePath)) {
                Log::error('PDF file not found after save', [
                    'payment_id' => $payment->id,
                    'file_path' => $filePath
                ]);
                return null;
            }
            
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            InvoiceLogger::logGenerationSuccess($payment, $filePath, $executionTime, [
                'file_size_bytes' => $fileSize,
                'invoice_number' => $invoiceData['invoice_number']
            ]);
            
            return $filePath;
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $executionTime = round(($endTime - $startTime) * 1000, 2);
            
            InvoiceLogger::logGenerationError($payment, $e);
            
            return null;
        }
    }
    
    /**
     * Generate unique invoice number
     *
     * @param Payment $payment
     * @return string
     */
    private function generateInvoiceNumber(Payment $payment): string
    {
        try {
            $booking = $payment->booking;
            $date = $payment->payment_date ?? now();
            
            $invoiceNumber = 'INV-' . $date->format('Ymd') . '-' . str_pad($booking->id, 6, '0', STR_PAD_LEFT);
            
            Log::debug('Generated invoice number', [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'invoice_number' => $invoiceNumber,
                'payment_date' => $date->format('Y-m-d H:i:s')
            ]);
            
            return $invoiceNumber;
            
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice number', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            
            // Fallback invoice number
            $fallbackNumber = 'INV-' . now()->format('Ymd') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
            
            Log::info('Using fallback invoice number', [
                'payment_id' => $payment->id,
                'fallback_number' => $fallbackNumber
            ]);
            
            return $fallbackNumber;
        }
    }
    
    /**
     * Get invoice file path if exists
     *
     * @param Payment $payment
     * @return string|null
     */
    public function getInvoiceFilePath(Payment $payment): ?string
    {
        try {
            $booking = $payment->booking;
            
            if (!$booking) {
                Log::warning('No booking found for payment when searching invoice', [
                    'payment_id' => $payment->id
                ]);
                return null;
            }
            
            $pattern = 'invoices/invoice-' . $booking->booking_reference . '-*.pdf';
            
            Log::debug('Searching for existing invoice file', [
                'payment_id' => $payment->id,
                'booking_reference' => $booking->booking_reference,
                'search_pattern' => $pattern
            ]);
            
            // Pastikan direktori invoices ada
            if (!Storage::disk('public')->exists('invoices')) {
                Log::info('Invoices directory does not exist', [
                    'payment_id' => $payment->id
                ]);
                return null;
            }
            
            $files = Storage::disk('public')->files('invoices');
            
            Log::debug('Found files in invoices directory', [
                'payment_id' => $payment->id,
                'total_files' => count($files),
                'files' => $files
            ]);
            
            foreach ($files as $file) {
                if (fnmatch($pattern, $file)) {
                    Log::info('Found existing invoice file', [
                        'payment_id' => $payment->id,
                        'file_path' => $file,
                        'file_size' => Storage::disk('public')->size($file)
                    ]);
                    return $file;
                }
            }
            
            Log::info('No existing invoice file found', [
                'payment_id' => $payment->id,
                'search_pattern' => $pattern
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('Error searching for invoice file', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return null;
        }
    }
    
    /**
     * Delete invoice file
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteInvoiceFile(string $filePath): bool
    {
        try {
            InvoiceLogger::logSystemEvent('invoice_file_deletion_attempt', [
                'file_path' => $filePath
            ]);
            
            if (Storage::disk('public')->exists($filePath)) {
                $fileSize = Storage::disk('public')->size($filePath);
                
                $deleted = Storage::disk('public')->delete($filePath);
                
                if ($deleted) {
                    InvoiceLogger::logSystemEvent('invoice_file_deleted_successfully', [
                        'file_path' => $filePath,
                        'file_size_bytes' => $fileSize
                    ]);
                    return true;
                } else {
                    InvoiceLogger::logSystemEvent('invoice_file_deletion_failed', [
                        'file_path' => $filePath
                    ]);
                    return false;
                }
            } else {
                InvoiceLogger::logSystemEvent('invoice_file_not_found_for_deletion', [
                    'file_path' => $filePath
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while deleting invoice file', [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Clean up old invoice files (older than specified days)
     *
     * @param int $daysOld
     * @return array
     */
    public function cleanupOldInvoices(int $daysOld = 30): array
    {
        $deletedFiles = [];
        $errors = [];
        
        try {
            Log::info('Starting invoice cleanup', [
                'days_old' => $daysOld
            ]);
            
            if (!Storage::disk('public')->exists('invoices')) {
                Log::info('No invoices directory found for cleanup');
                return ['deleted' => [], 'errors' => []];
            }
            
            $files = Storage::disk('public')->files('invoices');
            $cutoffTime = now()->subDays($daysOld);
            
            foreach ($files as $file) {
                try {
                    $lastModified = Storage::disk('public')->lastModified($file);
                    $fileDate = \Carbon\Carbon::createFromTimestamp($lastModified);
                    
                    if ($fileDate->lt($cutoffTime)) {
                        $fileSize = Storage::disk('public')->size($file);
                        
                        if (Storage::disk('public')->delete($file)) {
                            $deletedFiles[] = [
                                'file' => $file,
                                'size' => $fileSize,
                                'date' => $fileDate->format('Y-m-d H:i:s')
                            ];
                            
                            Log::info('Deleted old invoice file', [
                                'file' => $file,
                                'size_bytes' => $fileSize,
                                'last_modified' => $fileDate->format('Y-m-d H:i:s')
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'file' => $file,
                        'error' => $e->getMessage()
                    ];
                    
                    Log::error('Error processing file during cleanup', [
                        'file' => $file,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            Log::info('Invoice cleanup completed', [
                'deleted_count' => count($deletedFiles),
                'error_count' => count($errors),
                'days_old' => $daysOld
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error during invoice cleanup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $errors[] = [
                'general_error' => $e->getMessage()
            ];
        }
        
        return [
            'deleted' => $deletedFiles,
            'errors' => $errors
        ];
    }
}