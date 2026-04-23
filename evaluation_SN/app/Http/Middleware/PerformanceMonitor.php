<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Démarrer le chronomètre
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        // Activer le log des requêtes SQL
        DB::enableQueryLog();
        
        // Exécuter la requête
        $response = $next($request);
        
        // Calculer les métriques
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = ($endTime - $startTime) * 1000; // en millisecondes
        $memoryUsed = ($endMemory - $startMemory) / 1024; // en KB
        $queryCount = count(DB::getQueryLog());
        
        // Logger les performances
        Log::info('Performance Metrics', [
            'route' => $request->path(),
            'method' => $request->method(),
            'execution_time_ms' => round($executionTime, 2),
            'memory_used_kb' => round($memoryUsed, 2),
            'sql_queries_count' => $queryCount,
            'status_code' => $response->getStatusCode()
        ]);
        
        // Ajouter les headers de performance
        $response->headers->set('X-Execution-Time', round($executionTime, 2) . 'ms');
        $response->headers->set('X-Memory-Usage', round($memoryUsed, 2) . 'KB');
        $response->headers->set('X-Query-Count', $queryCount);
        
        return $response;
    }
}

