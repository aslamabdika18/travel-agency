<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Exception;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Email address to send test email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Email Configuration...');
        $this->newLine();

        // Get email address
        $email = $this->argument('email') ?? $this->ask('Enter email address to send test email');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        // Display current configuration
        $this->displayConfiguration();
        
        // Send test email
        try {
            $this->info('Sending test email...');
            
            Mail::raw('This is a test email from ' . config('app.name') . '. If you receive this, your email configuration is working correctly!', function (Message $message) use ($email) {
                $message->to($email)
                        ->subject('Test Email from ' . config('app.name'))
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->newLine();
            $this->info('✅ Test email sent successfully!');
            $this->info('📧 Email sent to: ' . $email);
            $this->info('Please check your inbox (and spam folder) for the test email.');
            
            return 0;
            
        } catch (Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to send test email!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            // Provide troubleshooting tips
            $this->warn('Troubleshooting tips:');
            $this->line('1. Check your MAIL_USERNAME and MAIL_PASSWORD in .env file');
            $this->line('2. Make sure you are using App Password for Gmail (not regular password)');
            $this->line('3. Verify MAIL_HOST and MAIL_PORT are correct');
            $this->line('4. Check if MAIL_ENCRYPTION is set to "tls" for Gmail');
            $this->line('5. Ensure your Gmail account has "Less secure app access" enabled or use App Password');
            
            return 1;
        }
    }
    
    /**
     * Display current email configuration
     */
    private function displayConfiguration()
    {
        $this->info('=== Current Email Configuration ===');
        $this->table(
            ['Setting', 'Value'],
            [
                ['MAIL_MAILER', config('mail.default')],
                ['MAIL_HOST', config('mail.mailers.smtp.host')],
                ['MAIL_PORT', config('mail.mailers.smtp.port')],
                ['MAIL_ENCRYPTION', config('mail.mailers.smtp.encryption')],
                ['MAIL_USERNAME', config('mail.mailers.smtp.username') ? '***' . substr(config('mail.mailers.smtp.username'), -10) : 'Not set'],
                ['MAIL_PASSWORD', config('mail.mailers.smtp.password') ? '***' . str_repeat('*', strlen(config('mail.mailers.smtp.password')) - 3) : 'Not set'],
                ['MAIL_FROM_ADDRESS', config('mail.from.address')],
                ['MAIL_FROM_NAME', config('mail.from.name')],
            ]
        );
        $this->newLine();
    }
}