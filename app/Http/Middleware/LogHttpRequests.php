<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogHttpRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log the incoming request
        $this->logRequest($request);
        
        return $next($request);
    }
    
    /**
     * Log the HTTP request details
     *
     * @param \Illuminate\Http\Request $request
     */
    private function logRequest(Request $request)
    {
        $logData = [
            'method' => $request->method(),
            'uri' => $request->getRequestUri(),
            'full_url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'headers' => $request->headers->all(),
            'query_params' => $request->query(),
            'body' => $request->getContent(),
            'form_data' => $request->all(),
            'time' => now()->toIso8601String(),
        ];
        
        Storage::append('http_requests.log', json_encode($logData, JSON_PRETTY_PRINT) . "\n" . str_repeat('-', 80) . "\n");
    }
} 