<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class EnvironmentSwitchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'env:switch 
                            {environment : The environment to switch to (development, staging, production)}
                            {--no-cache : Skip cache clearing}
                            {--no-optimize : Skip optimization commands}
                            {--backup : Create backup of current .env file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Switch between different environment configurations';

    /**
     * Available environments
     *
     * @var array
     */
    private $availableEnvironments = ['development', 'staging', 'production'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $environment = $this->argument('environment');
        
        // Validate environment
        if (!in_array($environment, $this->availableEnvironments)) {
            $this->error("Invalid environment: {$environment}");
            $this->info('Available environments: ' . implode(', ', $this->availableEnvironments));
            return 1;
        }

        $this->info("Switching to {$environment} environment...");
        
        // Check if environment file exists
        $envFile = base_path(".env.{$environment}");
        if (!File::exists($envFile)) {
            $this->error("Environment file not found: {$envFile}");
            return 1;
        }

        // Backup current .env if requested
        if ($this->option('backup')) {
            $this->backupCurrentEnv();
        }

        // Switch environment
        $this->switchEnvironment($environment, $envFile);

        // Clear caches unless skipped
        if (!$this->option('no-cache')) {
            $this->clearCaches();
        }

        // Run optimization commands for staging/production
        if (!$this->option('no-optimize') && in_array($environment, ['staging', 'production'])) {
            $this->optimizeApplication($environment);
        }

        // Show environment information
        $this->showEnvironmentInfo($environment);

        // Show next steps
        $this->showNextSteps($environment);

        $this->info("\n✅ Successfully switched to {$environment} environment!");
        
        return 0;
    }

    /**
     * Backup current .env file
     */
    private function backupCurrentEnv()
    {
        $currentEnv = base_path('.env');
        
        if (File::exists($currentEnv)) {
            $backupFile = base_path('.env.backup.' . now()->format('Y-m-d-H-i-s'));
            File::copy($currentEnv, $backupFile);
            $this->info("✅ Current .env backed up to: {$backupFile}");
        }
    }

    /**
     * Switch to the specified environment
     */
    private function switchEnvironment($environment, $envFile)
    {
        $targetFile = base_path('.env');
        
        File::copy($envFile, $targetFile);
        $this->info("✅ Environment file copied from .env.{$environment}");
    }

    /**
     * Clear application caches
     */
    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');
        
        $commands = [
            'config:clear' => 'Configuration cache',
            'cache:clear' => 'Application cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'View cache',
        ];

        foreach ($commands as $command => $description) {
            try {
                Artisan::call($command);
                $this->line("  ✅ {$description} cleared");
            } catch (\Exception $e) {
                $this->warn("  ⚠️  Failed to clear {$description}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Optimize application for staging/production
     */
    private function optimizeApplication($environment)
    {
        $this->info('⚡ Optimizing application...');
        
        $commands = [
            'config:cache' => 'Configuration caching',
            'route:cache' => 'Route caching',
            'view:cache' => 'View caching',
        ];

        // Add event caching for production
        if ($environment === 'production') {
            $commands['event:cache'] = 'Event caching';
        }

        foreach ($commands as $command => $description) {
            try {
                Artisan::call($command);
                $this->line("  ✅ {$description} completed");
            } catch (\Exception $e) {
                $this->warn("  ⚠️  Failed to run {$description}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Show environment information
     */
    private function showEnvironmentInfo($environment)
    {
        $configFile = config_path('environments.php');
        
        if (File::exists($configFile)) {
            $config = include $configFile;
            $envConfig = $config['environments'][$environment] ?? null;
            
            if ($envConfig) {
                $this->info("\n📋 Environment Information:");
                $this->line("   Name: {$envConfig['name']}");
                $this->line("   Description: {$envConfig['description']}");
                $this->line("   Debug Mode: " . ($envConfig['debug'] ? '🟢 Enabled' : '🔴 Disabled'));
                $this->line("   Log Level: {$envConfig['log_level']}");
                
                // Show enabled features
                $features = array_filter($envConfig['features'], fn($enabled) => $enabled);
                if (!empty($features)) {
                    $this->line("   Features: " . implode(', ', array_keys($features)));
                }
            }
        }
    }

    /**
     * Show next steps based on environment
     */
    private function showNextSteps($environment)
    {
        $this->info("\n🚀 Next Steps:");
        
        switch ($environment) {
            case 'development':
                $this->line("   1. Run: npm run dev (for asset compilation with HMR)");
                $this->line("   2. Run: php artisan serve (to start development server)");
                $this->line("   3. Run: php artisan migrate:fresh --seed (if needed)");
                $this->line("   4. Access: http://localhost:8000");
                break;
                
            case 'staging':
                $this->line("   1. Run: npm run build (to compile assets for production)");
                $this->line("   2. Run: php artisan migrate (to run pending migrations)");
                $this->line("   3. Update your staging server configuration");
                $this->line("   4. Test all features thoroughly");
                break;
                
            case 'production':
                $this->line("   1. Run: npm run build (to compile assets for production)");
                $this->line("   2. Run: php artisan migrate --force (to run pending migrations)");
                $this->line("   3. Run: php artisan queue:restart (to restart queue workers)");
                $this->line("   4. Monitor application performance and logs");
                break;
        }
        
        $this->warn("\n⚠️  Important Reminders:");
        $this->line("   • Update your database credentials in .env");
        $this->line("   • Update your API keys and secrets");
        $this->line("   • Verify all external service configurations");
        $this->line("   • Test the application thoroughly");
        
        if ($environment === 'production') {
            $this->error("\n🚨 Production Environment Active!");
            $this->line("   • Debug mode is disabled");
            $this->line("   • Error reporting is minimized");
            $this->line("   • Performance optimizations are enabled");
            $this->line("   • Monitor logs and performance metrics");
        }
    }
}