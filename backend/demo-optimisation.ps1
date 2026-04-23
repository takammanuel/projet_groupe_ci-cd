# Démonstration visuelle des optimisations
Write-Host @"

╔═══════════════════════════════════════════════════════════════╗
║                                                               ║
║     DÉMONSTRATION DES STRATÉGIES D'OPTIMISATION              ║
║                                                               ║
║  Stratégie 1: Mise en cache (Caching)                        ║
║  Stratégie 2: Eager Loading                                  ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝

"@ -ForegroundColor Cyan

# Configuration
$baseUrl = "http://localhost:8000/api"

# Fonction pour afficher les headers de performance
function Show-Performance {
    param($response)
    
    $execTime = $response.Headers['X-Execution-Time']
    $memUsage = $response.Headers['X-Memory-Usage']
    $queryCount = $response.Headers['X-Query-Count']
    
    Write-Host "  ⏱️  Temps d'exécution: " -NoNewline -ForegroundColor Yellow
    Write-Host "$execTime" -ForegroundColor White
    Write-Host "  💾 Mémoire utilisée: " -NoNewline -ForegroundColor Yellow
    Write-Host "$memUsage" -ForegroundColor White
    Write-Host "  🔍 Requêtes SQL: " -NoNewline -ForegroundColor Yellow
    Write-Host "$queryCount" -ForegroundColor White
}

# 1. Connexion
Write-Host "`n[1/6] Authentification..." -ForegroundColor Magenta
try {
    $login = Invoke-WebRequest -Uri "$baseUrl/login" -Method Post -ContentType "application/json" -Body (@{
        email = "test@example.com"
        password = "password123"
    } | ConvertTo-Json) -ErrorAction Stop
    
    $token = ($login.Content | ConvertFrom-Json).access_token
    Write-Host "✓ Authentifié" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Utilisateur non trouvé, création..." -ForegroundColor Yellow
    $register = Invoke-WebRequest -Uri "$baseUrl/register" -Method Post -ContentType "application/json" -Body (@{
        name = "Demo User"
        email = "test@example.com"
        password = "password123"
        password_confirmation = "password123"
    } | ConvertTo-Json)
    
    $token = ($register.Content | ConvertFrom-Json).access_token
    Write-Host "✓ Utilisateur créé et authentifié" -ForegroundColor Green
}

$headers = @{
    "Authorization" = "Bearer $token"
}

# 2. Vider le cache pour commencer proprement
Write-Host "`n[2/6] Nettoyage du cache..." -ForegroundColor Magenta
Invoke-WebRequest -Uri "$baseUrl/cache/clear" -Method Delete -Headers $headers | Out-Null
Write-Host "✓ Cache vidé" -ForegroundColor Green

# 3. Premier appel - CACHE MISS
Write-Host "`n[3/6] Premier appel GET /api/abonne (CACHE MISS attendu)" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkGray
$response1 = Invoke-WebRequest -Uri "$baseUrl/abonne" -Method Get -Headers $headers
Show-Performance $response1
$data1 = $response1.Content | ConvertFrom-Json
Write-Host "  📊 Abonnés récupérés: $($data1.Count)" -ForegroundColor Cyan

# 4. Deuxième appel - CACHE HIT
Write-Host "`n[4/6] Deuxième appel GET /api/abonne (CACHE HIT attendu)" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkGray
$response2 = Invoke-WebRequest -Uri "$baseUrl/abonne" -Method Get -Headers $headers
Show-Performance $response2

# Calculer l'amélioration
$time1 = [float]($response1.Headers['X-Execution-Time'] -replace 'ms','')
$time2 = [float]($response2.Headers['X-Execution-Time'] -replace 'ms','')
$improvement = [math]::Round((($time1 - $time2) / $time1) * 100, 1)

Write-Host "`n  🚀 Amélioration: " -NoNewline -ForegroundColor Green
Write-Host "$improvement% plus rapide!" -ForegroundColor White

# 5. Test Eager Loading avec factures
Write-Host "`n[5/6] Test Eager Loading GET /api/factures" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkGray
Invoke-WebRequest -Uri "$baseUrl/cache/clear" -Method Delete -Headers $headers | Out-Null
$response3 = Invoke-WebRequest -Uri "$baseUrl/factures" -Method Get -Headers $headers
Show-Performance $response3
$data3 = $response3.Content | ConvertFrom-Json
Write-Host "  📊 Factures récupérées: $($data3.Count)" -ForegroundColor Cyan
Write-Host "  ✨ Avec Eager Loading, les abonnés sont chargés en 1 seule requête!" -ForegroundColor Green

# 6. Statistiques du cache
Write-Host "`n[6/6] Statistiques du cache" -ForegroundColor Magenta
Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor DarkGray
$stats = Invoke-WebRequest -Uri "$baseUrl/cache/stats" -Method Get -Headers $headers
$statsData = $stats.Content | ConvertFrom-Json
Write-Host "  📦 Éléments en cache: $($statsData.total_cached_items)" -ForegroundColor Cyan
Write-Host "  🔧 Driver de cache: $($statsData.cache_driver)" -ForegroundColor Cyan

# Résumé
Write-Host @"

╔═══════════════════════════════════════════════════════════════╗
║                      RÉSUMÉ                                   ║
╠═══════════════════════════════════════════════════════════════╣
║                                                               ║
║  ✓ Stratégie 1 (Caching): Amélioration de $improvement%          ║
║  ✓ Stratégie 2 (Eager Loading): Requêtes SQL optimisées      ║
║  ✓ Monitoring: Headers de performance ajoutés                ║
║  ✓ Logs: Toutes les opérations sont tracées                  ║
║                                                               ║
║  Les deux stratégies travaillent ensemble pour:              ║
║  • Réduire le temps de réponse                               ║
║  • Diminuer la charge sur la base de données                 ║
║  • Améliorer la scalabilité de l'application                 ║
║                                                               ║
╚═══════════════════════════════════════════════════════════════╝

"@ -ForegroundColor Green

Write-Host "Pour voir les logs détaillés:" -ForegroundColor Yellow
Write-Host "  GET $baseUrl/logs" -ForegroundColor White
Write-Host "`nPour voir les statistiques du cache:" -ForegroundColor Yellow
Write-Host "  GET $baseUrl/cache/stats" -ForegroundColor White
