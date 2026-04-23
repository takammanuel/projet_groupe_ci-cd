<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbonneController;
use App\Http\Controllers\factureController;
use App\Http\Controllers\reclamationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\CacheStatsController;

// Routes publiques (sans authentification)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées (avec authentification Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Routes d'authentification
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Routes des abonnés
    Route::apiResource('abonne', AbonneController::class);

    // Routes des factures
    Route::apiResource('factures', factureController::class);
    Route::get('/abonne/{abonneId}/factures', [factureController::class, 'getByAbonne']);
    //Route des reclamations
    Route::apiResource('reclamations', reclamationController::class);


    // Routes des logs
    Route::get('/logs', [LogController::class, 'index']);

    // Routes des statistiques de cache et optimisation
    Route::get('/cache/stats', [CacheStatsController::class, 'stats']);
    Route::delete('/cache/clear', [CacheStatsController::class, 'clear']);
    Route::get('/performance/queries', [CacheStatsController::class, 'queryStats']);
});
