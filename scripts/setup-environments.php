#!/usr/bin/env php
<?php

/**
 * Environment Setup Script
 * 
 * This script helps you set up different environments for your Laravel application.
 * It will guide you through the configuration process and generate the necessary files.
 */

require_once __DIR__ . '/../vendor/autoload.php';

class EnvironmentSetup
{
    private $basePath;
    private $environments = ['development', 'staging', 'production'];
    
    public function __construct()
    {
        $this->basePath = dirname(__DIR__);
    }
    
    public function run()
    {
        $this->showWelcome();
        
        // Check if setup is needed
        if ($this->isAlreadySetup()) {
            $this->info("Environment files already exist.");
            if (!$this->confirm("Do you want to reconfigure?")) {
                return;
            }
        }
        
        // Setup each environment
        foreach ($this->environments as $environment) {
            $this->setupEnvironment($environment);
        }
        
        // Register service provider
        $this->registerServiceProvider();
        
        // Register middleware
        $this->registerMiddleware();
        
        // Generate application keys
        $this->generateApplicationKeys();
        
        // Show completion message
        $this->showCompletion();
    }
    
    private function showWelcome()
    {
        $this->info("🚀 Laravel Environment Setup Wizard");
        $this->info("===================================\n");
        $this->info("This wizard will help you set up different environments for your Laravel application.");
        $this->info("We'll configure: Development, Staging, and Production environments.\n");
    }
    
    private function isAlreadySetup()
    {
        foreach ($this->environments as $env) {
            if (file_exists($this->basePath . "/.env.{$env}")) {
                return true;
            }
        }
        return false;
    }
    
    private function setupEnvironment($environment)
    {
        $this->info("\n📋 Configuring {$environment} environment...");
        
        $config = $this->getEnvironmentConfig($environment);
        
        // Get user input for critical settings
        $config = $this->getUserInput($environment, $config);
        
        // Generate environment file
        $this->generateEnvironmentFile($environment, $config);
        
        $this->success("✅ {$environment} environment configured successfully!");
    }
    
    private function getEnvironmentConfig($environment)
    {
        $baseConfig = [
            'APP_NAME' => 'Sumatra Tour Travel',
            'APP_ENV' => $environment,
            'APP_DEBUG' => $environment === 'development' ? 'true' : 'false',
            'APP_TIMEZONE' => 'Asia/Jakarta',
            'APP_LOCALE' => 'id',
            'APP_FALLBACK_LOCALE' => 'en',
        ];
        
        switch ($environment) {
            case 'development':
                return array_merge($baseConfig, [
                    'APP_URL' => 'http://localhost:8000',
                    'DB_CONNECTION' => 'sqlite',
                    'DB_DATABASE' => 'database/database.sqlite',
                    'CACHE_STORE' => 'database',
                    'MAIL_MAILER' => 'log',
                    'MIDTRANS_IS_PRODUCTION' => 'false',
                ]);
                
            case 'staging':
                return array_merge($baseConfig, [
                    'APP_URL' => 'https://staging.sumatratour.com',
                    'DB_CONNECTION' => 'mysql',
                    'DB_HOST' => '127.0.0.1',
                    'DB_PORT' => '3306',
                    'CACHE_STORE' => 'redis',
                    'MAIL_MAILER' => 'smtp',
                    'MIDTRANS_IS_PRODUCTION' => 'false',
                ]);
                
            case 'production':
                return array_merge($baseConfig, [
                    'APP_URL' => 'https://sumatratour.com',
                    'DB_CONNECTION' => 'mysql',
                    'CACHE_STORE' => 'redis',
                    'MAIL_MAILER' => 'ses',
                    'MIDTRANS_IS_PRODUCTION' => 'true',
                ]);
        }
        
        return $baseConfig;
    }
    
    private function getUserInput($environment, $config)
    {
        $this->info("\nPlease provide the following information for {$environment}:");
        
        // App URL
        $config['APP_URL'] = $this->ask("App URL", $config['APP_URL']);
        
        // Database configuration
        if ($environment !== 'development') {
            $config['DB_HOST'] = $this->ask("Database Host", $config['DB_HOST'] ?? '127.0.0.1');
            $config['DB_PORT'] = $this->ask("Database Port", $config['DB_PORT'] ?? '3306');
            $config['DB_DATABASE'] = $this->ask("Database Name", "sumatra_tour_{$environment}");
            $config['DB_USERNAME'] = $this->ask("Database Username", "{$environment}_user");
            $config['DB_PASSWORD'] = $this->askSecret("Database Password");
        }
        
        // Redis configuration for staging/production
        if (in_array($environment, ['staging', 'production'])) {
            if ($this->confirm("Do you want to configure Redis?")) {
                $config['REDIS_HOST'] = $this->ask("Redis Host", '127.0.0.1');
                $config['REDIS_PASSWORD'] = $this->askSecret("Redis Password (leave empty if none)");
                $config['REDIS_PORT'] = $this->ask("Redis Port", '6379');
            }
        }
        
        // Mail configuration
        if ($environment !== 'development') {
            if ($this->confirm("Do you want to configure mail settings?")) {
                if ($environment === 'staging') {
                    $config['MAIL_HOST'] = $this->ask("SMTP Host", 'smtp.mailtrap.io');
                    $config['MAIL_PORT'] = $this->ask("SMTP Port", '587');
                    $config['MAIL_USERNAME'] = $this->ask("SMTP Username");
                    $config['MAIL_PASSWORD'] = $this->askSecret("SMTP Password");
                } else {
                    $config['MAIL_HOST'] = $this->ask("SES Host", 'email-smtp.ap-southeast-1.amazonaws.com');
                    $config['MAIL_USERNAME'] = $this->ask("SES Username");
                    $config['MAIL_PASSWORD'] = $this->askSecret("SES Password");
                }
            }
        }
        
        // AWS configuration for staging/production
        if (in_array($environment, ['staging', 'production'])) {
            if ($this->confirm("Do you want to configure AWS S3?")) {
                $config['AWS_ACCESS_KEY_ID'] = $this->ask("AWS Access Key ID");
                $config['AWS_SECRET_ACCESS_KEY'] = $this->askSecret("AWS Secret Access Key");
                $config['AWS_DEFAULT_REGION'] = $this->ask("AWS Region", 'ap-southeast-1');
                $config['AWS_BUCKET'] = $this->ask("S3 Bucket Name", "sumatra-tour-{$environment}");
            }
        }
        
        // Midtrans configuration
        if ($this->confirm("Do you want to configure Midtrans payment?")) {
            $isProduction = $environment === 'production';
            $keyType = $isProduction ? 'production' : 'sandbox';
            
            $config['MIDTRANS_SERVER_KEY'] = $this->askSecret("Midtrans {$keyType} Server Key");
            $config['MIDTRANS_CLIENT_KEY'] = $this->ask("Midtrans {$keyType} Client Key");
        }
        
        // Monitoring for staging/production
        if (in_array($environment, ['staging', 'production'])) {
            if ($this->confirm("Do you want to configure monitoring (Sentry, Slack)?")) {
                $config['SENTRY_LARAVEL_DSN'] = $this->ask("Sentry DSN (optional)");
                $config['SLACK_WEBHOOK_URL'] = $this->ask("Slack Webhook URL (optional)");
                
                if ($environment === 'production') {
                    $config['NEW_RELIC_LICENSE_KEY'] = $this->ask("New Relic License Key (optional)");
                }
            }
        }
        
        return $config;
    }
    
    private function generateEnvironmentFile($environment, $config)
    {
        $envFile = $this->basePath . "/.env.{$environment}";
        $template = $this->getEnvironmentTemplate($environment);
        
        // Replace placeholders with actual values
        foreach ($config as $key => $value) {
            $template = str_replace("{{$key}}", $value, $template);
        }
        
        // Generate a unique app key placeholder
        $template = str_replace('{APP_KEY}', 'base64:' . base64_encode(random_bytes(32)), $template);
        
        file_put_contents($envFile, $template);
    }
    
    private function getEnvironmentTemplate($environment)
    {
        $templateFile = $this->basePath . "/.env.{$environment}";
        
        if (file_exists($templateFile)) {
            return file_get_contents($templateFile);
        }
        
        // Fallback to basic template
        return $this->getBasicTemplate($environment);
    }
    
    private function getBasicTemplate($environment)
    {
        return "APP_NAME=\"{APP_NAME} (" . ucfirst($environment) . ")\"\n" .
               "APP_ENV={APP_ENV}\n" .
               "APP_KEY={APP_KEY}\n" .
               "APP_DEBUG={APP_DEBUG}\n" .
               "APP_URL={APP_URL}\n\n" .
               "# Add other configuration as needed\n";
    }
    
    private function registerServiceProvider()
    {
        $this->info("\n📦 Registering EnvironmentServiceProvider...");
        
        $configFile = $this->basePath . '/config/app.php';
        
        if (file_exists($configFile)) {
            $content = file_get_contents($configFile);
            
            if (strpos($content, 'EnvironmentServiceProvider') === false) {
                $pattern = "/(App\\\\Providers\\\\RouteServiceProvider::class,)/";
                $replacement = "$1\n        App\\Providers\\EnvironmentServiceProvider::class,";
                $content = preg_replace($pattern, $replacement, $content);
                
                file_put_contents($configFile, $content);
                $this->success("✅ EnvironmentServiceProvider registered");
            } else {
                $this->info("ℹ️  EnvironmentServiceProvider already registered");
            }
        }
    }
    
    private function registerMiddleware()
    {
        $this->info("\n🛡️  Registering EnvironmentMiddleware...");
        
        $kernelFile = $this->basePath . '/app/Http/Kernel.php';
        
        if (file_exists($kernelFile)) {
            $content = file_get_contents($kernelFile);
            
            if (strpos($content, 'EnvironmentMiddleware') === false) {
                $pattern = "/(protected \$middleware = \[)/";
                $replacement = "$1\n        \\App\\Http\\Middleware\\EnvironmentMiddleware::class,";
                $content = preg_replace($pattern, $replacement, $content);
                
                file_put_contents($kernelFile, $content);
                $this->success("✅ EnvironmentMiddleware registered");
            } else {
                $this->info("ℹ️  EnvironmentMiddleware already registered");
            }
        }
    }
    
    private function generateApplicationKeys()
    {
        $this->info("\n🔑 Generating application keys...");
        
        foreach ($this->environments as $environment) {
            $envFile = $this->basePath . "/.env.{$environment}";
            
            if (file_exists($envFile)) {
                $content = file_get_contents($envFile);
                $newKey = 'base64:' . base64_encode(random_bytes(32));
                $content = preg_replace('/APP_KEY=.*/', "APP_KEY={$newKey}", $content);
                file_put_contents($envFile, $content);
                
                $this->info("  ✅ Generated key for {$environment}");
            }
        }
    }
    
    private function showCompletion()
    {
        $this->success("\n🎉 Environment setup completed successfully!");
        $this->info("\nNext steps:");
        $this->info("1. Review and update the generated .env files");
        $this->info("2. Switch to your desired environment:");
        $this->info("   php artisan env:switch development");
        $this->info("3. Run migrations:");
        $this->info("   php artisan migrate");
        $this->info("4. Start development:");
        $this->info("   npm run dev");
        $this->info("   php artisan serve");
        $this->info("\nFor more information, see ENVIRONMENT_SETUP.md");
    }
    
    private function ask($question, $default = null)
    {
        $prompt = $default ? "{$question} [{$default}]: " : "{$question}: ";
        echo $prompt;
        $input = trim(fgets(STDIN));
        return $input ?: $default;
    }
    
    private function askSecret($question)
    {
        echo "{$question}: ";
        
        // Hide input for passwords
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $input = trim(fgets(STDIN));
        } else {
            system('stty -echo');
            $input = trim(fgets(STDIN));
            system('stty echo');
            echo "\n";
        }
        
        return $input;
    }
    
    private function confirm($question)
    {
        echo "{$question} [y/N]: ";
        $input = trim(fgets(STDIN));
        return strtolower($input) === 'y' || strtolower($input) === 'yes';
    }
    
    private function info($message)
    {
        echo "\033[36m{$message}\033[0m\n";
    }
    
    private function success($message)
    {
        echo "\033[32m{$message}\033[0m\n";
    }
    
    private function warning($message)
    {
        echo "\033[33m{$message}\033[0m\n";
    }
    
    private function error($message)
    {
        echo "\033[31m{$message}\033[0m\n";
    }
}

// Run the setup
$setup = new EnvironmentSetup();
$setup->run();