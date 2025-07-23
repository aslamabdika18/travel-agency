<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class InvoiceActivityLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log request start
        $this->logRequestStart($request);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        // Log request completion
        $this->logRequestEnd($request, $response, $duration);
        
        return $response;
    }
    
    /**
     * Log request start
     */
    private function logRequestStart(Request $request)
    {
        $logData = [
            'event' => 'invoice_request_start',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'user_id' => Auth::id(),
            'session_id' => $request->session()?->getId(),
            'timestamp' => now()->toISOString()
        ];
        
        // Add route parameters if available
        if ($request->route()) {
            $parameters = $request->route()->parameters();
            if (!empty($parameters)) {
                $logData['route_parameters'] = $parameters;
                
                // Extract payment ID if available
                if (isset($parameters['payment'])) {
                    $payment = $parameters['payment'];
                    if (is_object($payment) && method_exists($payment, 'getKey')) {
                        $logData['payment_id'] = $payment->getKey();
                        $logData['booking_reference'] = $payment->booking?->booking_reference;
                    }
                }
            }
        }
        
        // Add request headers for debugging
        $logData['headers'] = [
            'accept' => $request->header('Accept'),
            'content_type' => $request->header('Content-Type'),
            'referer' => $request->header('Referer')
        ];
        
        Log::info('Invoice activity request started', $logData);
    }
    
    /**
     * Log request completion
     */
    private function logRequestEnd(Request $request, Response $response, float $duration)
    {
        $logData = [
            'event' => 'invoice_request_end',
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => $request->route()?->getName(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $duration,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'timestamp' => now()->toISOString()
        ];
        
        // Add route parameters if available
        if ($request->route()) {
            $parameters = $request->route()->parameters();
            if (!empty($parameters)) {
                $logData['route_parameters'] = $parameters;
                
                // Extract payment ID if available
                if (isset($parameters['payment'])) {
                    $payment = $parameters['payment'];
                    if (is_object($payment) && method_exists($payment, 'getKey')) {
                        $logData['payment_id'] = $payment->getKey();
                        $logData['booking_reference'] = $payment->booking?->booking_reference;
                    }
                }
            }
        }
        
        // Add response information
        $logData['response'] = [
            'content_type' => $response->headers->get('Content-Type'),
            'content_length' => $response->headers->get('Content-Length'),
            'cache_control' => $response->headers->get('Cache-Control')
        ];
        
        // Check if it's a file download
        $contentDisposition = $response->headers->get('Content-Disposition');
        if ($contentDisposition && strpos($contentDisposition, 'attachment') !== false) {
            $logData['is_download'] = true;
            $logData['download_filename'] = $this->extractFilenameFromContentDisposition($contentDisposition);
        }
        
        // Log level based on status code
        if ($response->getStatusCode() >= 400) {
            Log::warning('Invoice activity request completed with error', $logData);
        } elseif ($duration > 5000) { // More than 5 seconds
            Log::warning('Invoice activity request completed slowly', $logData);
        } else {
            Log::info('Invoice activity request completed successfully', $logData);
        }
        
        // Additional logging for specific status codes
        if ($response->getStatusCode() === 403) {
            Log::warning('Unauthorized invoice access attempt', [
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent')
            ]);
        }
        
        if ($response->getStatusCode() === 404) {
            Log::info('Invoice not found', [
                'user_id' => Auth::id(),
                'url' => $request->fullUrl(),
                'route_parameters' => $request->route()?->parameters()
            ]);
        }
    }
    
    /**
     * Extract filename from Content-Disposition header
     */
    private function extractFilenameFromContentDisposition(string $contentDisposition): ?string
    {
        if (preg_match('/filename="([^"]+)"/', $contentDisposition, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/filename=([^;\s]+)/', $contentDisposition, $matches)) {
            return trim($matches[1], '"');
        }
        
        return null;
    }
}