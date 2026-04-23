<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Abonne;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class factureController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/factures",
     *     tags={"Factures"},
     *     summary="Liste de toutes les factures",
     *     description="Récupérer toutes les factures avec les informations des abonnés",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des factures récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="abonne_id", type="integer", example=1),
     *                 @OA\Property(property="consommation", type="number", format="float", example=15.5),
     *                 @OA\Property(property="montantTotal", type="number", format="float", example=7750),
     *                 @OA\Property(property="dateEmission", type="string", format="date", example="2026-03-01"),
     *                 @OA\Property(property="statut", type="string", example="Payee"),
     *                 @OA\Property(property="abonne", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function index()
    {
        // Stratégie 1: Mise en cache + Stratégie 2: Eager Loading
        $factures = CacheService::remember(
            CacheService::factureKey(),
            CacheService::LIST_TTL,
            function() {
                Log::info('Chargement des factures depuis la base de données');
                // Eager Loading pour éviter N+1 queries
                return Facture::with('abonne')->get();
            }
        );
        
        return response()->json($factures);
    }

    /**
     * @OA\Post(
     *     path="/api/factures",
     *     tags={"Factures"},
     *     summary="Créer une nouvelle facture",
     *     description="Ajouter une nouvelle facture pour un abonné",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"abonne_id","consommation","montantTotal","dateEmission","statut"},
     *             @OA\Property(property="abonne_id", type="integer", example=1),
     *             @OA\Property(property="consommation", type="number", format="float", example=15.5),
     *             @OA\Property(property="montantTotal", type="number", format="float", example=7750),
     *             @OA\Property(property="dateEmission", type="string", format="date", example="2026-03-01"),
     *             @OA\Property(property="statut", type="string", enum={"Emise","Payee"}, example="Emise")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Facture créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="abonne_id", type="integer", example=1),
     *             @OA\Property(property="consommation", type="number", format="float", example=15.5),
     *             @OA\Property(property="montantTotal", type="number", format="float", example=7750),
     *             @OA\Property(property="dateEmission", type="string", format="date", example="2026-03-01"),
     *             @OA\Property(property="statut", type="string", example="Emise")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $donneesValidee = $request->validate([
                'abonne_id' => 'required|exists:abonnes,id',
                'consommation' => 'required|numeric|min:0',
                'montantTotal' => 'required|numeric|min:0',
                'dateEmission' => 'required|date',
                'statut' => 'required|in:Emise,Payee',
            ]);

            $facture = Facture::create($donneesValidee);
            
            // Invalider le cache après création
            CacheService::invalidateFactureCache(null, $facture->abonne_id);
            
            Log::info('Facture créée', [
                'facture_id' => $facture->id,
                'abonne_id' => $facture->abonne_id,
                'montantTotal' => $facture->montantTotal,
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            
            return response()->json($facture, 201);
        } catch (\Exception $e) {
            Log::error('Erreur création facture', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            throw $e;
        }
    }

    /**
     * @OA\Get(
     *     path="/api/factures/{id}",
     *     tags={"Factures"},
     *     summary="Détails d'une facture",
     *     description="Récupérer les informations d'une facture spécifique avec l'abonné",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la facture",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Facture récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="abonne_id", type="integer", example=1),
     *             @OA\Property(property="consommation", type="number", format="float", example=15.5),
     *             @OA\Property(property="montantTotal", type="number", format="float", example=7750),
     *             @OA\Property(property="dateEmission", type="string", format="date", example="2026-03-01"),
     *             @OA\Property(property="statut", type="string", example="Payee"),
     *             @OA\Property(property="abonne", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Facture non trouvée"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function show(Facture $facture)
    {
        // Stratégie 1: Mise en cache + Stratégie 2: Eager Loading
        $factureData = CacheService::remember(
            CacheService::factureKey($facture->id),
            CacheService::DETAIL_TTL,
            function() use ($facture) {
                Log::info('Chargement facture depuis la base de données', ['id' => $facture->id]);
                // Eager Loading de l'abonné
                return $facture->load('abonne');
            }
        );
        
        return response()->json($factureData);
    }

    /**
     * @OA\Put(
     *     path="/api/factures/{id}",
     *     tags={"Factures"},
     *     summary="Modifier une facture",
     *     description="Mettre à jour les informations d'une facture (mise à jour partielle possible)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la facture",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="statut", type="string", enum={"Emise","Payee"}, example="Payee"),
     *             @OA\Property(property="consommation", type="number", format="float", example=16.0),
     *             @OA\Property(property="montantTotal", type="number", format="float", example=8000),
     *             @OA\Property(property="dateEmission", type="string", format="date", example="2026-03-02")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Facture modifiée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="abonne_id", type="integer", example=1),
     *             @OA\Property(property="consommation", type="number", format="float", example=16.0),
     *             @OA\Property(property="montantTotal", type="number", format="float", example=8000),
     *             @OA\Property(property="dateEmission", type="string", format="date", example="2026-03-02"),
     *             @OA\Property(property="statut", type="string", example="Payee")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Facture non trouvée"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function update(Request $request, Facture $facture)
    {
        try {
            $donneesValidee = $request->validate([
                'statut' => 'sometimes|in:Emise,Payee',
                'consommation' => 'sometimes|numeric|min:0',
                'montantTotal' => 'sometimes|numeric|min:0',
                'dateEmission' => 'sometimes|date',
            ]);

            $facture->update($donneesValidee);
            
            // Invalider le cache après modification
            CacheService::invalidateFactureCache($facture->id, $facture->abonne_id);
            
            Log::info('Facture modifiée', [
                'facture_id' => $facture->id,
                'abonne_id' => $facture->abonne_id,
                'montantTotal' => $facture->montantTotal,
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            
            return response()->json($facture);
        } catch (\Exception $e) {
            Log::error('Erreur modification facture', [
                'facture_id' => $facture->id,
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            throw $e;
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/factures/{id}",
     *     tags={"Factures"},
     *     summary="Supprimer une facture",
     *     description="Supprimer une facture du système",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la facture",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Facture supprimée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Facture supprimée avec succès")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Facture non trouvée"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function destroy(Facture $facture)
    {
        $factureId = $facture->id;
        $abonneId = $facture->abonne_id;
        
        Log::warning('Facture supprimée', [
            'facture_id' => $factureId,
            'abonne_id' => $abonneId,
            'montantTotal' => $facture->montantTotal,
            'user_id' => request()->user()->id ?? null,
            'ip' => request()->ip(),
        ]);
        
        $facture->delete();
        
        // Invalider le cache après suppression
        CacheService::invalidateFactureCache($factureId, $abonneId);
        
        return response()->json(['message' => 'Facture supprimée avec succès']);
    }

    public function getByAbonne($abonneId)
    {
        // Stratégie 1: Mise en cache + Stratégie 2: Eager Loading
        $factures = CacheService::remember(
            CacheService::factureKey(null, $abonneId),
            CacheService::LIST_TTL,
            function() use ($abonneId) {
                $abonne = Abonne::findOrFail($abonneId);
                Log::info('Chargement factures par abonné depuis la base de données', ['abonne_id' => $abonneId]);
                return $abonne->factures;
            }
        );
        
        return response()->json($factures);
    }
}
