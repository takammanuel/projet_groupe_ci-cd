# Script PowerShell pour tester l'authentification API

$baseUrl = "http://127.0.0.1:8000/api"

Write-Host "=== 1. INSCRIPTION (REGISTER) ===" -ForegroundColor Green
$registerBody = @{
    name = "Admin User"
    email = "admin@example.com"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

try {
    $registerResponse = Invoke-RestMethod -Uri "$baseUrl/register" -Method Post -ContentType "application/json" -Body $registerBody
    Write-Host "Inscription réussie!" -ForegroundColor Green
    $registerResponse | ConvertTo-Json -Depth 3
    $token = $registerResponse.access_token
} catch {
    Write-Host "Erreur lors de l'inscription (peut-être que l'utilisateur existe déjà)" -ForegroundColor Yellow
    Write-Host $_.Exception.Message
}

Write-Host "`n=== 2. CONNEXION (LOGIN) ===" -ForegroundColor Green
$loginBody = @{
    email = "admin@example.com"
    password = "password123"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$baseUrl/login" -Method Post -ContentType "application/json" -Body $loginBody
    Write-Host "Connexion réussie!" -ForegroundColor Green
    $loginResponse | ConvertTo-Json -Depth 3
    $token = $loginResponse.access_token
    Write-Host "`nToken d'accès: $token" -ForegroundColor Cyan
} catch {
    Write-Host "Erreur lors de la connexion" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit
}

Write-Host "`n=== 3. RÉCUPÉRER PROFIL UTILISATEUR (ME) ===" -ForegroundColor Green
try {
    $headers = @{
        "Authorization" = "Bearer $token"
        "Accept" = "application/json"
    }
    $meResponse = Invoke-RestMethod -Uri "$baseUrl/me" -Method Get -Headers $headers
    Write-Host "Profil récupéré!" -ForegroundColor Green
    $meResponse | ConvertTo-Json -Depth 3
} catch {
    Write-Host "Erreur lors de la récupération du profil" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "`n=== 4. CRÉER UN ABONNÉ (AVEC TOKEN) ===" -ForegroundColor Green
$abonneBody = @{
    nom = "Secure"
    prenom = "User"
    ville = "Yaounde"
    quartier = "Bastos"
    numeroCompteur = "SEC001"
    typeAbonnement = "Professionnel"
} | ConvertTo-Json

try {
    $headers = @{
        "Authorization" = "Bearer $token"
        "Accept" = "application/json"
        "Content-Type" = "application/json"
    }
    $abonneResponse = Invoke-RestMethod -Uri "$baseUrl/abonne" -Method Post -Headers $headers -Body $abonneBody
    Write-Host "Abonné créé avec succès!" -ForegroundColor Green
    $abonneResponse | ConvertTo-Json -Depth 3
} catch {
    Write-Host "Erreur lors de la création de l'abonné" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "`n=== 5. TESTER ACCÈS SANS TOKEN (DOIT ÉCHOUER) ===" -ForegroundColor Green
try {
    $unauthorizedResponse = Invoke-RestMethod -Uri "$baseUrl/abonne" -Method Get -ContentType "application/json"
    Write-Host "ERREUR: L'accès sans token devrait être refusé!" -ForegroundColor Red
} catch {
    Write-Host "Accès refusé comme prévu (401 Unauthorized)" -ForegroundColor Green
    Write-Host $_.Exception.Message
}

Write-Host "`n=== 6. DÉCONNEXION (LOGOUT) ===" -ForegroundColor Green
try {
    $headers = @{
        "Authorization" = "Bearer $token"
        "Accept" = "application/json"
    }
    $logoutResponse = Invoke-RestMethod -Uri "$baseUrl/logout" -Method Post -Headers $headers
    Write-Host "Déconnexion réussie!" -ForegroundColor Green
    $logoutResponse | ConvertTo-Json -Depth 3
} catch {
    Write-Host "Erreur lors de la déconnexion" -ForegroundColor Red
    Write-Host $_.Exception.Message
}

Write-Host "`n=== TEST TERMINÉ ===" -ForegroundColor Cyan
