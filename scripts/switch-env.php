#!/usr/bin/env php
<?php

/**
 * Environment Switcher Script
 * 
 * This script helps you switch between different environments easily.
 * Usage: php scripts/switch-env.php [environment]
 * 
 * Available environments: development, staging, production
 */

require_once __DIR__ . '/../vendor/autoload.php';

class EnvironmentSwitcher
{
    private $basePath;
    private $availableEnvironments = ['development', 'staging', 'production'];
    
    public function __construct()
    {
        $this->basePath = dirname(__DIR__);
    }
    
    public function run($args)
    {
        if (count($args) < 2) {
            $this->showUsage();
            return;
        }
        
        $environment = $args[1];
        
        if (!in_array($environment, $this->availableEnvironments)) {
            $this->error("Invalid environment: {$environment}");
            $this->showUsage();
            return;
        }
        
        $this->switchEnvironment($environment);
    }
    
    private function switchEnvironment($environment)
    {
        $envFile = $this->basePath . "/.env.{$environment}";
        $targetFile = $this->basePath . '/.env';
        
        if (!file_exists($envFile)) {
            $this->error("Environment file not found: {$envFile}");
            return;
        }
        
        // Backup current .env if exists
        if (file_exists($targetFile)) {
            $backupFile = $this->basePath . '/.env.backup.' . date('Y-m-d-H-i-s');
            copy($targetFile, $backupFile);
            $this->info("Current .env backed up to: {$backupFile}");
        }
        
        // Copy environment file
        copy($envFile, $targetFile);
        $this->success("Switched to {$environment} environment");
        
        // Clear caches
        $this->clearCaches();
        
        // Show environment info
        $this->showEnvironmentInfo($environment);
        
        // Show next steps
        $this->showNextSteps($environment);
    }
    
    private function clearCaches()
    {
        $this->info("Clearing caches...");
        
        $commands = [
            'php artisan config:clear',
            'php artisan cache:clear',
            'php artisan route:clear',
            'php artisan view:clear',
        ];
        
        foreach ($commands as $command) {
            $this->executeCommand($command);
        }
        
        $this->success("Caches cleared successfully");
    }
    
    private function showEnvironmentInfo($environment)
    {
        $configFile = $this->basePath . '/config/environments.php';
        
        if (file_exists($configFile)) {
            $config = include $configFile;
            $envConfig = $config['environments'][$environment] ?? null;
            
            if ($envConfig) {
                $this->info("\nEnvironment Information:");
                $this->info("Name: {$envConfig['name']}");
                $this->info("Description: {$envConfig['description']}");
                $this->info("Debug Mode: " . ($envConfig['debug'] ? 'Enabled' : 'Disabled'));
                $this->info("Log Level: {$envConfig['log_level']}");
            }
        }
    }
    
    private function showNextSteps($environment)
    {
        $this->info("\nNext Steps:");
        
        switch ($environment) {
            case 'development':
                $this->info("1. Run: npm run dev (for asset compilation with HMR)");
                $this->info("2. Run: php artisan serve (to start development server)");
                $this->info("3. Run: php artisan migrate:fresh --seed (if needed)");
                break;
                
            case 'staging':
                $this->info("1. Run: npm run build (to compile assets for production)");
                $this->info("2. Run: php artisan migrate (to run pending migrations)");
                $this->info("3. Run: php artisan config:cache (to cache configuration)");
                $this->info("4. Run: php artisan route:cache (to cache routes)");
                break;
                
            case 'production':
                $this->info("1. Run: npm run build (to compile assets for production)");
                $this->info("2. Run: php artisan migrate --force (to run pending migrations)");
                $this->info("3. Run: php artisan optimize (to optimize the application)");
                $this->info("4. Run: php artisan queue:restart (to restart queue workers)");
                break;
        }
        
        $this->info("\nDon't forget to:");
        $this->info("- Update your database credentials");
        $this->info("- Update your API keys and secrets");
        $this->info("- Test the application thoroughly");
    }
    
    private function executeCommand($command)
    {
        $output = [];
        $returnCode = 0;
        
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            $this->warning("Command failed: {$command}");
            foreach ($output as $line) {
                $this->warning("  {$line}");
            }
        }
    }
    
    private function showUsage()
    {
        $this->info("Environment Switcher");
        $this->info("Usage: php scripts/switch-env.php [environment]");
        $this->info("");
        $this->info("Available environments:");
        foreach ($this->availableEnvironments as $env) {
            $this->info("  - {$env}");
        }
        $this->info("");
        $this->info("Examples:");
        $this->info("  php scripts/switch-env.php development");
        $this->info("  php scripts/switch-env.php staging");
        $this->info("  php scripts/switch-env.php production");
    }
    
    private function info($message)
    {
        echo "\033[36m[INFO]\033[0m {$message}\n";
    }
    
    private function success($message)
    {
        echo "\033[32m[SUCCESS]\033[0m {$message}\n";
    }
    
    private function warning($message)
    {
        echo "\033[33m[WARNING]\033[0m {$message}\n";
    }
    
    private function error($message)
    {
        echo "\033[31m[ERROR]\033[0m {$message}\n";
    }
}

// Run the script
$switcher = new EnvironmentSwitcher();
$switcher->run($argv);