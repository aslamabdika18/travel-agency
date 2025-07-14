<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class EnvironmentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register environment-specific services
        $this->registerEnvironmentServices();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Configure environment-specific settings
        $this->configureEnvironment();
        
        // Configure rate limiting
        $this->configureRateLimiting();
        
        // Configure monitoring
        $this->configureMonitoring();
        
        // Configure file uploads
        $this->configureFileUploads();
    }

    /**
     * Register environment-specific services
     */
    private function registerEnvironmentServices(): void
    {
        $environment = app()->environment();
        
        // Register development-specific services
        if ($environment === 'development') {
            $this->registerDevelopmentServices();
        }
        
        // Register staging-specific services
        if ($environment === 'staging') {
            $this->registerStagingServices();
        }
        
        // Register production-specific services
        if ($environment === 'production') {
            $this->registerProductionServices();
        }
    }

    /**
     * Configure environment-specific settings
     */
    private function configureEnvironment(): void
    {
        $environment = app()->environment();
        $envConfig = config("environments.environments.{$environment}", []);
        
        if (empty($envConfig)) {
            return;
        }

        // Configure logging
        $this->configureLogging($environment, $envConfig);
        
        // Configure caching
        $this->configureCaching($environment, $envConfig);
        
        // Configure database
        $this->configureDatabase($environment);
        
        // Configure performance settings
        $this->configurePerformance($environment, $envConfig);
    }

    /**
     * Configure logging based on environment
     */
    private function configureLogging($environment, $envConfig): void
    {
        $logLevel = $envConfig['log_level'] ?? 'info';
        Config::set('logging.channels.single.level', $logLevel);
        Config::set('logging.channels.daily.level', $logLevel);
        
        // Configure Slack logging for staging and production
        if (in_array($environment, ['staging', 'production'])) {
            $this->configureSlackLogging($environment);
        }
    }

    /**
     * Configure Slack logging
     */
    private function configureSlackLogging($environment): void
    {
        $webhookUrl = env('SLACK_WEBHOOK_URL');
        
        if ($webhookUrl) {
            Config::set('logging.channels.slack', [
                'driver' => 'slack',
                'url' => $webhookUrl,
                'username' => 'Laravel Log',
                'emoji' => ':boom:',
                'level' => $environment === 'production' ? 'error' : 'warning',
            ]);
        }
    }

    /**
     * Configure caching based on environment
     */
    private function configureCaching($environment, $envConfig): void
    {
        $performance = $envConfig['performance'] ?? [];
        
        // Enable/disable various caches
        if ($performance['opcache'] ?? false) {
            ini_set('opcache.enable', '1');
        }
        
        // Configure Redis clustering for production
        if ($environment === 'production' && ($performance['redis_cluster'] ?? false)) {
            $this->configureRedisCluster();
        }
    }

    /**
     * Configure Redis cluster for production
     */
    private function configureRedisCluster(): void
    {
        if (env('REDIS_CLUSTER', false)) {
            Config::set('database.redis.options.cluster', 'redis');
            Config::set('database.redis.clusters.default', [
                [
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'password' => env('REDIS_PASSWORD'),
                    'port' => env('REDIS_PORT', 6379),
                    'database' => 0,
                ],
            ]);
        }
    }

    /**
     * Configure database based on environment
     */
    private function configureDatabase($environment): void
    {
        $dbConfig = config("environments.database.{$environment}", []);
        
        if (empty($dbConfig)) {
            return;
        }

        // Configure read/write splitting for production
        if ($dbConfig['read_write_split'] ?? false) {
            $this->configureReadWriteSplit();
        }
        
        // Configure connection pooling
        if ($dbConfig['connection_pooling'] ?? false) {
            $this->configureConnectionPooling();
        }
    }

    /**
     * Configure read/write database splitting
     */
    private function configureReadWriteSplit(): void
    {
        $readHost = env('DB_READ_HOST');
        $readUsername = env('DB_READ_USERNAME');
        $readPassword = env('DB_READ_PASSWORD');
        
        if ($readHost) {
            Config::set('database.connections.mysql.read', [
                'host' => [$readHost],
                'username' => $readUsername ?: env('DB_USERNAME'),
                'password' => $readPassword ?: env('DB_PASSWORD'),
            ]);
            
            Config::set('database.connections.mysql.write', [
                'host' => [env('DB_HOST', '127.0.0.1')],
                'username' => env('DB_USERNAME'),
                'password' => env('DB_PASSWORD'),
            ]);
        }
    }

    /**
     * Configure database connection pooling
     */
    private function configureConnectionPooling(): void
    {
        // Configure connection pool settings
        Config::set('database.connections.mysql.options', array_merge(
            Config::get('database.connections.mysql.options', []),
            [
                \PDO::ATTR_PERSISTENT => true,
                \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            ]
        ));
    }

    /**
     * Configure performance settings
     */
    private function configurePerformance($environment, $envConfig): void
    {
        $performance = $envConfig['performance'] ?? [];
        
        // Configure CDN
        if ($performance['cdn_enabled'] ?? false) {
            $cdnUrl = env('CDN_URL');
            if ($cdnUrl) {
                Config::set('app.asset_url', $cdnUrl);
            }
        }
        
        // Configure image optimization
        if ($performance['image_optimization'] ?? false) {
            $this->configureImageOptimization();
        }
    }

    /**
     * Configure image optimization
     */
    private function configureImageOptimization(): void
    {
        // Configure image optimization settings
        Config::set('image.optimization', [
            'enabled' => true,
            'quality' => 85,
            'formats' => ['webp', 'jpg', 'png'],
            'sizes' => [
                'thumbnail' => [150, 150],
                'medium' => [300, 300],
                'large' => [800, 600],
            ],
        ]);
    }

    /**
     * Configure rate limiting
     */
    private function configureRateLimiting(): void
    {
        $environment = app()->environment();
        $rateLimitType = config("environments.environments.{$environment}.security.rate_limiting", 'moderate');
        $rateLimits = config("environments.rate_limits.{$rateLimitType}", []);
        
        // Configure API rate limiting
        if (isset($rateLimits['api'])) {
            RateLimiter::for('api', function (Request $request) use ($rateLimits) {
                [$maxAttempts, $decayMinutes] = explode(',', $rateLimits['api']);
                return Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by($request->user()?->id ?: $request->ip());
            });
        }
        
        // Configure web rate limiting
        if (isset($rateLimits['web'])) {
            RateLimiter::for('web', function (Request $request) use ($rateLimits) {
                [$maxAttempts, $decayMinutes] = explode(',', $rateLimits['web']);
                return Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by($request->ip());
            });
        }
        
        // Configure auth rate limiting
        if (isset($rateLimits['auth'])) {
            RateLimiter::for('auth', function (Request $request) use ($rateLimits) {
                [$maxAttempts, $decayMinutes] = explode(',', $rateLimits['auth']);
                return Limit::perMinutes($decayMinutes, $maxAttempts)
                    ->by($request->ip());
            });
        }
    }

    /**
     * Configure monitoring
     */
    private function configureMonitoring(): void
    {
        $environment = app()->environment();
        $monitoring = config("environments.monitoring.{$environment}", []);
        
        // Configure Sentry
        if ($monitoring['sentry'] ?? false) {
            $this->configureSentry();
        }
        
        // Configure New Relic
        if ($monitoring['new_relic'] ?? false) {
            $this->configureNewRelic();
        }
    }

    /**
     * Configure Sentry error tracking
     */
    private function configureSentry(): void
    {
        $sentryDsn = env('SENTRY_LARAVEL_DSN');
        
        if ($sentryDsn) {
            Config::set('sentry.dsn', $sentryDsn);
            Config::set('sentry.environment', app()->environment());
            Config::set('logging.channels.sentry', [
                'driver' => 'sentry',
                'level' => 'error',
            ]);
        }
    }

    /**
     * Configure New Relic monitoring
     */
    private function configureNewRelic(): void
    {
        $newRelicKey = env('NEW_RELIC_LICENSE_KEY');
        
        if ($newRelicKey && extension_loaded('newrelic')) {
            newrelic_set_appname(config('app.name') . ' - ' . ucfirst(app()->environment()));
        }
    }

    /**
     * Configure file uploads
     */
    private function configureFileUploads(): void
    {
        $environment = app()->environment();
        $uploadLimits = config("environments.upload_limits.{$environment}", []);
        
        if (!empty($uploadLimits)) {
            Config::set('filesystems.upload_limits', $uploadLimits);
        }
    }

    /**
     * Register development-specific services
     */
    private function registerDevelopmentServices(): void
    {
        // Register development-only services here
        // Example: Debug tools, profilers, etc.
    }

    /**
     * Register staging-specific services
     */
    private function registerStagingServices(): void
    {
        // Register staging-only services here
        // Example: Testing tools, monitoring services, etc.
    }

    /**
     * Register production-specific services
     */
    private function registerProductionServices(): void
    {
        // Register production-only services here
        // Example: Performance monitoring, error tracking, etc.
    }
}