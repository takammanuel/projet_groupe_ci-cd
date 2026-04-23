# Script de test pour le système de logging
Write-Host "=== Test du système de logging ===" -ForegroundColor Cyan

# 1. Inscription d'un utilisateur
Write-Host "`n1. Test inscription (devrait créer un log INFO)..." -ForegroundColor Yellow
$registerResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/register" -Method Post -ContentType "application/json" -Body (@{
    name = "Test User Logs"
    email = "testlogs@example.com"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json)

$token = $registerResponse.access_token
Write-Host "✓ Utilisateur créé avec succès" -ForegroundColor Green

# 2. Connexion
Write-Host "`n2. Test connexion (devrait créer un log INFO)..." -ForegroundColor Yellow
$loginResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/login" -Method Post -ContentType "application/json" -Body (@{
    email = "testlogs@example.com"
    password = "password123"
} | ConvertTo-Json)
Write-Host "✓ Connexion réussie" -ForegroundColor Green

# 3. Tentative de connexion échouée
Write-Host "`n3. Test connexion échouée (devrait créer un log WARNING)..." -ForegroundColor Yellow
try {
    Invoke-RestMethod -Uri "http://localhost:8000/api/login" -Method Post -ContentType "application/json" -Body (@{
        email = "testlogs@example.com"
        password = "wrongpassword"
    } | ConvertTo-Json)
} catch {
    Write-Host "✓ Échec de connexion détecté (attendu)" -ForegroundColor Green
}

# 4. Créer un abonné
Write-Host "`n4. Test création abonné (devrait créer un log INFO)..." -ForegroundColor Yellow
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

$abonneResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/abonne" -Method Post -Headers $headers -Body (@{
    nom = "Test"
    prenom = "Logging"
    ville = "Yaounde"
    quartier = "Test"
    numeroCompteur = "TEST-LOG-001"
    typeAbonnement = "Domestique"
} | ConvertTo-Json)
Write-Host "✓ Abonné créé avec succès" -ForegroundColor Green

# 5. Créer une facture
Write-Host "`n5. Test création facture (devrait créer un log INFO)..." -ForegroundColor Yellow
$factureResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/factures" -Method Post -Headers $headers -Body (@{
    abonne_id = $abonneResponse.id
    consommation = 10.5
    montantTotal = 5250
    dateEmission = "2026-03-10"
    statut = "Emise"
} | ConvertTo-Json)
Write-Host "✓ Facture créée avec succès" -ForegroundColor Green

# 6. Supprimer la facture
Write-Host "`n6. Test suppression facture (devrait créer un log WARNING)..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "http://localhost:8000/api/factures/$($factureResponse.id)" -Method Delete -Headers $headers
Write-Host "✓ Facture supprimée avec succès" -ForegroundColor Green

# 7. Supprimer l'abonné
Write-Host "`n7. Test suppression abonné (devrait créer un log WARNING)..." -ForegroundColor Yellow
Invoke-RestMethod -Uri "http://localhost:8000/api/abonne/$($abonneResponse.id)" -Method Delete -Headers $headers
Write-Host "✓ Abonné supprimé avec succès" -ForegroundColor Green

# 8. Consulter les logs
Write-Host "`n8. Consultation des logs via API..." -ForegroundColor Yellow
$logsResponse = Invoke-RestMethod -Uri "http://localhost:8000/api/logs?limit=20" -Method Get -Headers $headers
Write-Host "✓ Logs récupérés: $($logsResponse.total) entrées" -ForegroundColor Green

# Afficher les derniers logs
Write-Host "`n=== Derniers logs ===" -ForegroundColor Cyan
$logsResponse.logs | Select-Object -First 10 | ForEach-Object {
    $color = switch ($_.niveau) {
        "INFO" { "Green" }
        "WARNING" { "Yellow" }
        "ERROR" { "Red" }
        default { "White" }
    }
    Write-Host "[$($_.timestamp)] $($_.niveau): $($_.message)" -ForegroundColor $color
}

# 9. Test des filtres
Write-Host "`n9. Test filtres sur les logs..." -ForegroundColor Yellow

# Filtrer par niveau WARNING
$warningLogs = Invoke-RestMethod -Uri "http://localhost:8000/api/logs?niveau=warning&limit=5" -Method Get -Headers $headers
Write-Host "✓ Logs WARNING: $($warningLogs.total) entrées" -ForegroundColor Yellow

# Filtrer par niveau INFO
$infoLogs = Invoke-RestMethod -Uri "http://localhost:8000/api/logs?niveau=info&limit=5" -Method Get -Headers $headers
Write-Host "✓ Logs INFO: $($infoLogs.total) entrées" -ForegroundColor Green

# Filtrer par date
$todayLogs = Invoke-RestMethod -Uri "http://localhost:8000/api/logs?date_debut=2026-03-10&limit=5" -Method Get -Headers $headers
Write-Host "✓ Logs du jour: $($todayLogs.total) entrées" -ForegroundColor Cyan

Write-Host "`n=== Test terminé avec succès ===" -ForegroundColor Green
Write-Host "Tous les logs ont été créés et peuvent être consultés via GET /api/logs" -ForegroundColor Cyan
