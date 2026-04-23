<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/logs",
     *     tags={"Logs"},
     *     summary="Consulter les logs de l'application",
     *     description="Récupérer les logs avec filtres par niveau, date et type",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="niveau",
     *         in="query",
     *         description="Filtrer par niveau (info, warning, error)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"info", "warning", "error"})
     *     ),
     *     @OA\Parameter(
     *         name="date_debut",
     *         in="query",
     *         description="Date de début (format: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_fin",
     *         in="query",
     *         description="Date de fin (format: Y-m-d)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filtrer par type d'opération",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Nombre de logs à retourner (défaut: 50)",
     *         required=false,
     *         @OA\Schema(type="integer", default=50)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logs récupérés avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example=150),
     *             @OA\Property(property="logs", type="array", @OA\Items(
     *                 @OA\Property(property="timestamp", type="string", example="2026-03-10 14:30:25"),
     *                 @OA\Property(property="niveau", type="string", example="info"),
     *                 @OA\Property(property="message", type="string", example="Connexion réussie"),
     *                 @OA\Property(property="contexte", type="object")
     *             ))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs/laravel.log');
        
        if (!File::exists($logPath)) {
            return response()->json([
                'message' => 'Aucun fichier de log trouvé',
                'logs' => []
            ]);
        }

        $logContent = File::get($logPath);
        $logs = $this->parseLogFile($logContent);

        // Filtres
        $niveau = $request->query('niveau');
        $dateDebut = $request->query('date_debut');
        $dateFin = $request->query('date_fin');
        $type = $request->query('type');
        $limit = $request->query('limit', 50);

        if ($niveau) {
            $logs = array_filter($logs, function($log) use ($niveau) {
                return strtolower($log['niveau']) === strtolower($niveau);
            });
        }

        if ($dateDebut) {
            $logs = array_filter($logs, function($log) use ($dateDebut) {
                return $log['date'] >= $dateDebut;
            });
        }

        if ($dateFin) {
            $logs = array_filter($logs, function($log) use ($dateFin) {
                return $log['date'] <= $dateFin;
            });
        }

        if ($type) {
            $logs = array_filter($logs, function($log) use ($type) {
                return stripos($log['message'], $type) !== false;
            });
        }

        // Trier du plus récent au plus ancien
        $logs = array_values($logs);
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // Limiter les résultats
        $logs = array_slice($logs, 0, $limit);

        return response()->json([
            'total' => count($logs),
            'logs' => $logs
        ]);
    }

    private function parseLogFile($content)
    {
        $logs = [];
        $lines = explode("\n", $content);
        
        foreach ($lines as $line) {
            if (empty(trim($line))) {
                continue;
            }

            // Pattern pour parser les logs Laravel
            $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.+)/';
            
            if (preg_match($pattern, $line, $matches)) {
                $timestamp = $matches[1];
                $niveau = $matches[2];
                $message = $matches[3];
                
                // Extraire le contexte JSON si présent
                $contexte = [];
                if (preg_match('/\{.*\}/', $message, $jsonMatch)) {
                    $contexte = json_decode($jsonMatch[0], true) ?? [];
                    $message = trim(str_replace($jsonMatch[0], '', $message));
                }

                $logs[] = [
                    'timestamp' => $timestamp,
                    'date' => substr($timestamp, 0, 10),
                    'niveau' => strtoupper($niveau),
                    'message' => $message,
                    'contexte' => $contexte
                ];
            }
        }

        return $logs;
    }
}
