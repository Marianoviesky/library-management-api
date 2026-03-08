<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequestDuration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    // public function terminate(Request $request, Response $response): void
    // {
    //     // LARAVEL_START est défini tout au début du cycle de vie de l'application
    //     $duration = microtime(true) - LARAVEL_START;

    //     Log::info("Requête vers {$request->url()} a pris " . round($duration * 1000, 2) . " ms");
    // }

    public function terminate(Request $request, Response $response): void
    {
        if (defined('LARAVEL_START')) {
            $duration = microtime(true) - LARAVEL_START;
            Log::info("Requête vers {$request->url()} a pris " . round($duration * 1000, 2) . " ms");
        }
    }
}
