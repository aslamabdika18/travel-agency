<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class EnvironmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $environment = app()->environment();
        
        // Load environment-specific configurations
        $this->loadEnvironmentConfig($environment);
        
        // Apply environment-specific security headers
        $response = $next($request);
        
        // Add environment-specific headers
        $this->addEnvironmentHeaders($response, $environment);
        
        // Apply environment-specific security measures
        $this->applySecurityMeasures($response, $environment);
        
        return $response;
    }

    /**
     * Load environment-specific configurations
     */
    private function loadEnvironmentConfig($environment)
    {
        $envConfig = config("environments.environments.{$environment}", []);
        
        if (empty($envConfig)) {
            return;
        }

        // Apply rate limiting configuration
        $this->configureRateLimiting($environment, $envConfig);
        
        // Apply file upload limits
        $this->configureFileUploads($environment);
        
        // Apply performance settings
        $this->configurePerformance($envConfig);
    }

    /**
     * Configure rate limiting based on environment
     */
    private function configureRateLimiting($environment, $envConfig)
    {
        $rateLimitType = $envConfig['security']['rate_limiting'] ?? 'moderate';
        $rateLimits = config("environments.rate_limits.{$rateLimitType}", []);
        
        if (!empty($rateLimits)) {
            Config::set('rate_limits', $rateLimits);
        }
    }

    /**
     * Configure file upload limits based on environment
     */
    private function configureFileUploads($environment)
    {
        $uploadLimits = config("environments.upload_limits.{$environment}", []);
        
        if (!empty($uploadLimits)) {
            Config::set('filesystems.upload_limits', $uploadLimits);
        }
    }

    /**
     * Configure performance settings
     */
    private function configurePerformance($envConfig)
    {
        $performance = $envConfig['performance'] ?? [];
        
        // Set CDN configuration
        if ($performance['cdn_enabled'] ?? false) {
            Config::set('app.asset_url', env('CDN_URL', env('APP_URL')));
        }
    }

    /**
     * Add environment-specific headers
     */
    private function addEnvironmentHeaders(Response $response, $environment)
    {
        // Add environment indicator for non-production
        if ($environment !== 'production') {
            $response->headers->set('X-Environment', ucfirst($environment));
        }
        
        // Add cache control headers based on environment
        $this->setCacheHeaders($response, $environment);
    }

    /**
     * Set cache headers based on environment
     */
    private function setCacheHeaders(Response $response, $environment)
    {
        switch ($environment) {
            case 'development':
                $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', '0');
                break;
                
            case 'staging':
                $response->headers->set('Cache-Control', 'public, max-age=300'); // 5 minutes
                break;
                
            case 'production':
                // Let Laravel handle cache headers based on route configuration
                break;
        }
    }

    /**
     * Apply environment-specific security measures
     */
    private function applySecurityMeasures(Response $response, $environment)
    {
        $envConfig = config("environments.environments.{$environment}.security", []);
        
        // Force HTTPS in staging and production
        if ($envConfig['force_https'] ?? false) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
        
        // Add security headers for staging and production
        if (in_array($environment, ['staging', 'production'])) {
            $this->addSecurityHeaders($response);
        }
        
        // Add development-specific headers
        if ($environment === 'development') {
            $this->addDevelopmentHeaders($response);
        }
    }

    /**
     * Add security headers for staging and production
     */
    private function addSecurityHeaders(Response $response)
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; font-src 'self' https:; connect-src 'self' https:;",
        ];
        
        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }
    }

    /**
     * Add development-specific headers
     */
    private function addDevelopmentHeaders(Response $response)
    {
        // Add CORS headers for development
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        
        // Add development indicator
        $response->headers->set('X-Debug-Mode', 'enabled');
    }
}