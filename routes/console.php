<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\TestEmailCommand;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule payment monitoring to run every 30 minutes
Schedule::command('payment:monitor')->everyThirtyMinutes();

// Schedule payment status fix to run daily at 2 AM
Schedule::command('payment:fix-status --dry-run')->dailyAt('02:00');

// Schedule automatic refund processing to run daily at 3 AM
Schedule::command('refund:process-automatic --dry-run')->dailyAt('03:00');

// Schedule invoice cleanup to run weekly on Sunday at 4 AM
Schedule::command('invoice:maintenance --cleanup --days=30')->weekly()->sundays()->at('04:00');

// Manual commands for payment management
Artisan::command('payment:check {payment_id}', function ($paymentId) {
    $this->call('payment:monitor', ['--payment-id' => $paymentId]);
})->purpose('Check specific payment status');

Artisan::command('payment:fix-all', function () {
    $this->call('payment:fix-status');
})->purpose('Fix all payment status inconsistencies');

// Manual commands for refund management
Artisan::command('refund:process-dry-run', function () {
    $this->call('refund:process-automatic', ['--dry-run' => true]);
})->purpose('Process automatic refunds (dry run)');

Artisan::command('refund:process-live', function () {
    $this->call('refund:process-automatic');
})->purpose('Process automatic refunds (live)');

Artisan::command('refund:process-urgent', function () {
    $this->call('refund:process-automatic', ['--days' => 1, '--limit' => 10]);
})->purpose('Process urgent refunds (1 day threshold)');

// Manual commands for invoice management
Artisan::command('invoice:check', function () {
    $this->call('invoice:maintenance', ['--check' => true]);
})->purpose('Check invoice service configuration');

Artisan::command('invoice:stats', function () {
    $this->call('invoice:maintenance', ['--stats' => true]);
})->purpose('Show invoice statistics');

Artisan::command('invoice:cleanup {days=7}', function ($days) {
    $this->call('invoice:maintenance', ['--cleanup' => true, '--days' => $days]);
})->purpose('Cleanup old invoice files');

Artisan::command('email:test {email?}', function ($email = null) {
    $this->call(TestEmailCommand::class, ['email' => $email]);
})->purpose('Test email configuration by sending test email');
