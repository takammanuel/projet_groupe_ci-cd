<?php

namespace App\Http\Controllers;
use App\Models\Abonne;
use App\Services\CacheService;
use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;

class AbonneController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/abonne",
     *     tags={"Abonnés"},
     *     summary="Liste de tous les abonnés",
     *     description="Récupérer tous les abonnés avec leurs factures",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Liste des abonnés récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="nom", type="string", example="Dupont"),
     *                 @OA\Property(property="prenom", type="string", example="Marie"),
     *                 @OA\Property(property="ville", type="string", example="Yaounde"),
     *                 @OA\Property(property="quartier", type="string", example="Bastos"),
     *                 @OA\Property(property="numeroCompteur", type="string", example="YAO-2024-001"),
     *                 @OA\Property(property="typeAbonnement", type="string", example="Domestique"),
     *                 @OA\Property(property="factures", type="array", @OA\Items(type="object"))
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
        $abonne = CacheService::remember(
            CacheService::abonneKey(),
            CacheService::LIST_TTL,
            function() {
                Log::info('Chargement des abonnés depuis la base de données');
                // Eager Loading pour éviter N+1 queries
                return Abonne::with("factures")->get();
            }
        );

        return response()->json($abonne);
    }

    /**
     * @OA\Post(
     *     path="/api/abonne",
     *     tags={"Abonnés"},
     *     summary="Créer un nouvel abonné",
     *     description="Ajouter un nouvel abonné au système",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","prenom","ville","numeroCompteur","typeAbonnement"},
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Marie"),
     *             @OA\Property(property="ville", type="string", enum={"Yaounde","Douala","Bafoussam","Garoua"}, example="Yaounde"),
     *             @OA\Property(property="quartier", type="string", example="Bastos"),
     *             @OA\Property(property="numeroCompteur", type="string", example="YAO-2024-001"),
     *             @OA\Property(property="typeAbonnement", type="string", enum={"Domestique","Professionnel"}, example="Domestique")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Abonné créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Marie"),
     *             @OA\Property(property="ville", type="string", example="Yaounde"),
     *             @OA\Property(property="quartier", type="string", example="Bastos"),
     *             @OA\Property(property="numeroCompteur", type="string", example="YAO-2024-001"),
     *             @OA\Property(property="typeAbonnement", type="string", example="Domestique")
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
            $donneesValidee=$request->validate([
            'nom'=>'required|string|max:255',
            'prenom'=>'required|string|max:255',
            'ville'=>'required|in:Yaounde,Douala,Bafoussam,Garoua',
            'quartier'=>'nullable|string',
            'numeroCompteur'=>'required|unique:abonnes',
            'typeAbonnement'=>'required|in:Domestique,Professionnel'
         ]);
             $abonne= Abonne::create($donneesValidee);
             
             // Invalider le cache après création
             CacheService::invalidateAbonneCache();
             
             Log::info('Abonné créé', [
                 'abonne_id' => $abonne->id,
                 'numeroCompteur' => $abonne->numeroCompteur,
                 'user_id' => $request->user()->id ?? null,
                 'ip' => $request->ip(),
             ]);
             
             return response()->json($abonne,201);
        } catch (\Exception $e) {
            Log::error('Erreur création abonné', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            throw $e;
        }
    }
    /**
     * @OA\Get(
     *     path="/api/abonne/{id}",
     *     tags={"Abonnés"},
     *     summary="Détails d'un abonné",
     *     description="Récupérer les informations d'un abonné spécifique",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonné",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonné récupéré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Marie"),
     *             @OA\Property(property="ville", type="string", example="Yaounde"),
     *             @OA\Property(property="quartier", type="string", example="Bastos"),
     *             @OA\Property(property="numeroCompteur", type="string", example="YAO-2024-001"),
     *             @OA\Property(property="typeAbonnement", type="string", example="Domestique")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonné non trouvé"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
     public function show(Abonne $abonne)
    {
        // Stratégie 1: Mise en cache + Stratégie 2: Eager Loading
        $abonneData = CacheService::remember(
            CacheService::abonneKey($abonne->id),
            CacheService::DETAIL_TTL,
            function() use ($abonne) {
                Log::info('Chargement abonné depuis la base de données', ['id' => $abonne->id]);
                // Eager Loading des factures
                return $abonne->load('factures');
            }
        );
        
        return response()->json($abonneData);
    }
    /**
     * @OA\Put(
     *     path="/api/abonne/{id}",
     *     tags={"Abonnés"},
     *     summary="Modifier un abonné",
     *     description="Mettre à jour les informations d'un abonné",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonné",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nom","prenom","ville","numeroCompteur","typeAbonnement"},
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Marie-Claire"),
     *             @OA\Property(property="ville", type="string", enum={"Yaounde","Douala","Bafoussam","Garoua"}, example="Douala"),
     *             @OA\Property(property="quartier", type="string", example="Akwa"),
     *             @OA\Property(property="numeroCompteur", type="string", example="YAO-2024-001"),
     *             @OA\Property(property="typeAbonnement", type="string", enum={"Domestique","Professionnel"}, example="Professionnel")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonné modifié avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Marie-Claire")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonné non trouvé"
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
      public function update(Request $request, Abonne $abonne)
    {
        try {
            $donneesValidee=
            $request->validate([
            'nom'=>'required|string|max:255',
            'prenom'=>'required|string|max:255',
            'ville'=>'required|in:Yaounde,Douala,Bafoussam,Garoua',
            'quartier'=>'nullable|string',
            'numeroCompteur'=>'required|unique:abonnes,numeroCompteur,'.$abonne->id,
             'typeAbonnement'=>'required|in:Domestique,Professionnel'
            ]);
            $abonne->update($donneesValidee);
            
            // Invalider le cache après modification
            CacheService::invalidateAbonneCache($abonne->id);
            
            Log::info('Abonné modifié', [
                'abonne_id' => $abonne->id,
                'numeroCompteur' => $abonne->numeroCompteur,
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            
            return response()->json($abonne);
        } catch (\Exception $e) {
            Log::error('Erreur modification abonné', [
                'abonne_id' => $abonne->id,
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null,
                'ip' => $request->ip(),
            ]);
            throw $e;
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/abonne/{id}",
     *     tags={"Abonnés"},
     *     summary="Supprimer un abonné",
     *     description="Supprimer un abonné du système",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de l'abonné",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Abonné supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nom", type="string", example="Dupont"),
     *             @OA\Property(property="prenom", type="string", example="Marie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Abonné non trouvé"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function destroy(Abonne $abonne)
    {
        $abonneId = $abonne->id;
        
        Log::warning('Abonné supprimé', [
            'abonne_id' => $abonneId,
            'numeroCompteur' => $abonne->numeroCompteur,
            'user_id' => request()->user()->id ?? null,
            'ip' => request()->ip(),
        ]);
        
        $abonne->delete();
        
        // Invalider le cache après suppression
        CacheService::invalidateAbonneCache($abonneId);
        
        return response()->json($abonne);
    }




}
