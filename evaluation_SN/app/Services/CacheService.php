<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    // Durée de cache par défaut : 1 heure
    const DEFAULT_TTL = 3600;
    
    // Durée de cache pour les listes : 30 minutes
    const LIST_TTL = 1800;
    
    // Durée de cache pour les détails : 1 heure
    const DETAIL_TTL = 3600;

    /**
     * Récupérer ou mettre en cache des données
     */
    public static function remember(string $key, $ttl, callable $callback)
    {
        $startTime = microtime(true);
        
        $data = Cache::remember($key, $ttl, function() use ($callback, $key, $startTime) {
            Log::info('Cache MISS', [
                'key' => $key,
                'time' => microtime(true) - $startTime
            ]);
            return $callback();
        });
        
        if (Cache::has($key)) {
            Log::info('Cache HIT', [
                'key' => $key,
                'time' => microtime(true) - $startTime
            ]);
        }
        
        return $data;
    }

    /**
     * Invalider le cache pour une ressource
     */
    public static function forget(string $key): bool
    {
        Log::info('Cache invalidé', ['key' => $key]);
        return Cache::forget($key);
    }

    /**
     * Invalider plusieurs clés de cache
     */
    public static function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            self::forget($key);
        }
    }

    /**
     * Générer une clé de cache pour les abonnés
     */
    public static function abonneKey(?int $id = null): string
    {
        return $id ? "abonne:{$id}" : "abonnes:all";
    }

    /**
     * Générer une clé de cache pour les factures
     */
    public static function factureKey(?int $id = null, ?int $abonneId = null): string
    {
        if ($id) {
            return "facture:{$id}";
        }
        if ($abonneId) {
            return "factures:abonne:{$abonneId}";
        }
        return "factures:all";
    }

    /**
     * Invalider tout le cache des abonnés
     */
    public static function invalidateAbonneCache(?int $id = null): void
    {
        $keys = [self::abonneKey()];
        
        if ($id) {
            $keys[] = self::abonneKey($id);
        }
        
        self::forgetMany($keys);
    }

    /**
     * Invalider tout le cache des factures
     */
    public static function invalidateFactureCache(?int $id = null, ?int $abonneId = null): void
    {
        $keys = [self::factureKey()];
        
        if ($id) {
            $keys[] = self::factureKey($id);
        }
        
        if ($abonneId) {
            $keys[] = self::factureKey(null, $abonneId);
        }
        
        self::forgetMany($keys);
    }
}
