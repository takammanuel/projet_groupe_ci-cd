# Script PowerShell pour tester l'API

# Test GET - Récupérer tous les abonnés
Write-Host "=== TEST GET ===" -ForegroundColor Green
Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/abonne" -Method Get

Write-Host "`n=== TEST POST ===" -ForegroundColor Green
# Test POST - Créer un nouvel abonné
$body = @{
    nom = "test"
    prenom = "user"
    ville = "Douala"
    quartier = "akwa"
    numeroCompteur = "AA0010"
    typeAbonnement = "Domestique"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/abonne" -Method Post -ContentType "application/json" -Body $body
