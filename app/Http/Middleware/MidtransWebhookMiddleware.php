<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class MidtransWebhookMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Validasi IP address (opsional - untuk production)
        if (app()->environment('production')) {
            $this->validateMidtransIP($request);
        }
        
        // 2. Rate limiting per order_id untuk mencegah duplicate notifications
        $orderId = $request->input('order_id');
        if ($orderId) {
            $this->preventDuplicateNotifications($orderId);
        }
        
        // 3. Log incoming webhook untuk monitoring
        $this->logWebhookRequest($request);
        
        return $next($request);
    }
    
    /**
     * Validate that request comes from Midtrans IP (production only)
     */
    private function validateMidtransIP(Request $request): void
    {
        // Daftar IP Midtrans (update sesuai dokumentasi terbaru)
        $allowedIPs = [
            '103.10.128.0/20',
            '103.127.16.0/20',
            '103.58.103.177',
            '149.129.55.0/24'
        ];
        
        $clientIP = $request->ip();
        $isAllowed = false;
        
        foreach ($allowedIPs as $allowedIP) {
            if ($this->ipInRange($clientIP, $allowedIP)) {
                $isAllowed = true;
                break;
            }
        }
        
        if (!$isAllowed) {
            Log::warning('Webhook request from unauthorized IP', [
                'ip' => $clientIP,
                'user_agent' => $request->userAgent()
            ]);
            
            abort(403, 'Unauthorized IP address');
        }
    }
    
    /**
     * Prevent duplicate notifications for the same order
     */
    private function preventDuplicateNotifications(string $orderId): void
    {
        $cacheKey = "midtrans_webhook_{$orderId}";
        $lockDuration = 60; // 1 minute
        
        if (Cache::has($cacheKey)) {
            Log::warning('Duplicate Midtrans notification detected', [
                'order_id' => $orderId
            ]);
            
            abort(429, 'Duplicate notification - please wait before retrying');
        }
        
        // Set cache lock
        Cache::put($cacheKey, true, $lockDuration);
    }
    
    /**
     * Log webhook request for monitoring
     */
    private function logWebhookRequest(Request $request): void
    {
        Log::info('Midtrans webhook received', [
            'order_id' => $request->input('order_id'),
            'transaction_status' => $request->input('transaction_status'),
            'transaction_id' => $request->input('transaction_id'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Check if IP is in range
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            // Single IP
            return $ip === $range;
        }
        
        // CIDR range
        list($subnet, $bits) = explode('/', $range);
        
        if ($bits === null) {
            $bits = 32;
        }
        
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;
        
        return ($ip & $mask) === $subnet;
    }
}