<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Log;

class InvoiceMaintenanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:maintenance 
                            {--cleanup : Clean up old invoice files}
                            {--days=30 : Number of days to keep invoice files}
                            {--stats : Show invoice statistics}
                            {--check : Check invoice service configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maintenance commands for invoice management';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Invoice Maintenance...');
        
        Log::info('Invoice maintenance command started', [
            'options' => $this->options(),
            'user' => auth()->user()->id ?? 'console'
        ]);
        
        $invoiceService = new InvoiceService();
        
        // Check configuration
        if ($this->option('check')) {
            $this->checkConfiguration($invoiceService);
        }
        
        // Show statistics
        if ($this->option('stats')) {
            $this->showStatistics($invoiceService);
        }
        
        // Cleanup old files
        if ($this->option('cleanup')) {
            $days = (int) $this->option('days');
            $this->cleanupOldFiles($invoiceService, $days);
        }
        
        // If no specific option, show help
        if (!$this->option('check') && !$this->option('stats') && !$this->option('cleanup')) {
            $this->showHelp();
        }
        
        Log::info('Invoice maintenance command completed');
        $this->info('Invoice Maintenance completed.');
    }
    
    /**
     * Check invoice service configuration
     */
    private function checkConfiguration(InvoiceService $invoiceService)
    {
        $this->info('\n=== Configuration Check ===');
        
        $configured = $invoiceService->isConfigured();
        
        if ($configured) {
            $this->info('✅ Invoice service is properly configured');
            Log::info('Invoice service configuration check passed');
        } else {
            $this->error('❌ Invoice service configuration issues detected');
            Log::error('Invoice service configuration check failed');
        }
        
        // Additional checks
        $this->info('\nDetailed checks:');
        
        // Check storage disk
        try {
            $storageWorks = \Illuminate\Support\Facades\Storage::disk('public')->exists('.');
            $this->info($storageWorks ? '✅ Storage disk accessible' : '❌ Storage disk not accessible');
        } catch (\Exception $e) {
            $this->error('❌ Storage disk error: ' . $e->getMessage());
        }
        
        // Check invoices directory
        try {
            $invoicesExists = \Illuminate\Support\Facades\Storage::disk('public')->exists('invoices');
            $this->info($invoicesExists ? '✅ Invoices directory exists' : '⚠️  Invoices directory does not exist');
            
            if (!$invoicesExists) {
                $this->info('Attempting to create invoices directory...');
                \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('invoices');
                $this->info('✅ Invoices directory created');
            }
        } catch (\Exception $e) {
            $this->error('❌ Invoices directory error: ' . $e->getMessage());
        }
        
        // Check DomPDF
        try {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML('<h1>Test</h1>');
            $this->info('✅ DomPDF is working');
        } catch (\Exception $e) {
            $this->error('❌ DomPDF error: ' . $e->getMessage());
        }
    }
    
    /**
     * Show invoice statistics
     */
    private function showStatistics(InvoiceService $invoiceService)
    {
        $this->info('\n=== Invoice Statistics ===');
        
        $stats = $invoiceService->getStatistics();
        
        if (isset($stats['error'])) {
            $this->error('Error retrieving statistics: ' . $stats['error']);
            return;
        }
        
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Files', $stats['total_files']],
                ['Total Size (bytes)', number_format($stats['total_size_bytes'])],
                ['Total Size (MB)', $stats['total_size_mb']],
                ['Average File Size (KB)', $stats['total_files'] > 0 ? round($stats['total_size_bytes'] / $stats['total_files'] / 1024, 2) : 0]
            ]
        );
        
        Log::info('Invoice statistics displayed', $stats);
    }
    
    /**
     * Clean up old invoice files
     */
    private function cleanupOldFiles(InvoiceService $invoiceService, int $days)
    {
        $this->info("\n=== Cleaning up files older than {$days} days ===");
        
        if ($days < 7) {
            $this->error('Minimum cleanup age is 7 days for safety');
            return;
        }
        
        $confirm = $this->confirm("Are you sure you want to delete invoice files older than {$days} days?");
        
        if (!$confirm) {
            $this->info('Cleanup cancelled');
            return;
        }
        
        $result = $invoiceService->cleanupOldInvoices($days);
        
        $deletedCount = count($result['deleted']);
        $errorCount = count($result['errors']);
        
        $this->info("Cleanup completed:");
        $this->info("- Files deleted: {$deletedCount}");
        
        if ($errorCount > 0) {
            $this->warn("- Errors encountered: {$errorCount}");
        }
        
        // Show deleted files
        if ($deletedCount > 0) {
            $this->info('\nDeleted files:');
            foreach ($result['deleted'] as $file) {
                $this->line("- {$file['file']} ({$file['size']} bytes, {$file['date']})");
            }
        }
        
        // Show errors
        if ($errorCount > 0) {
            $this->warn('\nErrors:');
            foreach ($result['errors'] as $error) {
                if (isset($error['file'])) {
                    $this->error("- {$error['file']}: {$error['error']}");
                } else {
                    $this->error("- General error: {$error['general_error']}");
                }
            }
        }
        
        Log::info('Invoice cleanup completed via command', [
            'days_old' => $days,
            'deleted_count' => $deletedCount,
            'error_count' => $errorCount
        ]);
    }
    
    /**
     * Show help information
     */
    private function showHelp()
    {
        $this->info('\n=== Invoice Maintenance Help ===');
        $this->info('Available options:');
        $this->info('  --check     Check invoice service configuration');
        $this->info('  --stats     Show invoice file statistics');
        $this->info('  --cleanup   Clean up old invoice files');
        $this->info('  --days=N    Number of days to keep files (default: 30, minimum: 7)');
        $this->info('');
        $this->info('Examples:');
        $this->info('  php artisan invoice:maintenance --check');
        $this->info('  php artisan invoice:maintenance --stats');
        $this->info('  php artisan invoice:maintenance --cleanup --days=60');
        $this->info('  php artisan invoice:maintenance --check --stats --cleanup');
    }
}