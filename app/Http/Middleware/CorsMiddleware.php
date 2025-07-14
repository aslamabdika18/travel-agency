<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            return $this->handlePreflightRequest();
        }
        
        $response = $next($request);
        
        return $this->addCorsHeaders($response);
    }
    
    /**
     * Handle preflight OPTIONS request
     */
    private function handlePreflightRequest(): Response
    {
        return response('', 200)
            ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin())
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Max-Age', '86400'); // 24 hours
    }
    
    /**
     * Add CORS headers to response
     */
    private function addCorsHeaders(Response $response): Response
    {
        return $response
            ->header('Access-Control-Allow-Origin', $this->getAllowedOrigin())
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Access-Control-Expose-Headers', 'X-CSRF-TOKEN');
    }
    
    /**
     * Get allowed origin based on environment
     */
    private function getAllowedOrigin(): string
    {
        $allowedOrigins = [
            'http://localhost:3000',
            'http://localhost:5173',
            'http://127.0.0.1:3000',
            'http://127.0.0.1:5173',
        ];
        
        $origin = request()->header('Origin');
        
        if (in_array($origin, $allowedOrigins)) {
            return $origin;
        }
        
        // Default untuk development
        return 'http://localhost:5173';
    }
}