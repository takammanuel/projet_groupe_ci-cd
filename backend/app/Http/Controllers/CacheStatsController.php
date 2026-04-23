<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheStatsController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/cache/stats",
     *     tags={"Optimisation"},
     *     summary="Statistiques du cache",
     *     description="Obtenir des statistiques sur l'utilisation du cache",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="cache_driver", type="string", example="database"),
     *             @OA\Property(property="cached_items", type="integer", example=15),
     *             @OA\Property(property="cache_keys", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function stats()
    {
        $cacheDriver = config('cache.default');
        
        // Récupérer les clés de cache depuis la base de données
        $cachedItems = [];
        if ($cacheDriver === 'database') {
            $cachedItems = DB::table('cache')
                ->select('key', 'expiration')
                ->get()
                ->map(function($item) {
                    return [
                        'key' => $item->key,
                        'expires_at' => date('Y-m-d H:i:s', $item->expiration),
                        'ttl_seconds' => $item->expiration - time()
                    ];
                });
        }
        
        return response()->json([
            'cache_driver' => $cacheDriver,
            'total_cached_items' => count($cachedItems),
            'cached_items' => $cachedItems,
            'optimisations' => [
                'strategie_1' => 'Mise en cache (Caching)',
                'strategie_2' => 'Eager Loading',
                'cache_ttl_listes' => '30 minutes',
                'cache_ttl_details' => '1 heure'
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/cache/clear",
     *     tags={"Optimisation"},
     *     summary="Vider le cache",
     *     description="Supprimer toutes les entrées du cache",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cache vidé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Cache vidé avec succès")
     *         )
     *     )
     * )
     */
    public function clear()
    {
        Cache::flush();
        
        return response()->json([
            'message' => 'Cache vidé avec succès',
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/performance/queries",
     *     tags={"Optimisation"},
     *     summary="Statistiques des requêtes",
     *     description="Obtenir le nombre de requêtes SQL exécutées",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Statistiques récupérées avec succès"
     *     )
     * )
     */
    public function queryStats()
    {
        // Activer le compteur de requêtes
        DB::enableQueryLog();
        
        return response()->json([
            'message' => 'Query logging activé',
            'note' => 'Utilisez DB::getQueryLog() pour voir les requêtes exécutées',
            'optimisations' => [
                'eager_loading' => 'Réduit le nombre de requêtes SQL',
                'caching' => 'Évite les requêtes répétées'
            ]
        ]);
    }
}
