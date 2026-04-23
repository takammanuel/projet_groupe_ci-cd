# Documentation API - Gestion des Abonnés et Factures

##  Table des Matières

- [Vue d'ensemble](#-vue-densemble)
- [Configuration](#%EF%B8%8F-configuration)
- [Authentification](#-authentification)
- [Endpoints](#-endpoints)
  - [Authentification](#authentification-1)
  - [Abonnés](#abonnés)
  - [Factures](#factures)
- [Modèles de données](#-modèles-de-données)
- [Codes d'erreur](#-codes-derreur)
- [Exemples d'utilisation](#-exemples-dutilisation)

---

##  Vue d'ensemble

API REST sécurisée pour la gestion des abonnés et de leurs factures de consommation d'eau.

### Informations générales

- **Base URL**: `http://localhost:8000/api`
- **Format**: JSON
- **Authentification**: Bearer Token (Laravel Sanctum)
- **Version Laravel**: 11.x
- **Base de données**: MySQL

### Statistiques

-  26 tests passent avec succès
-  Tous les endpoints protégés par authentification
-  3 contrôleurs: Auth, Abonné, Facture
-  2 modèles principaux: Abonne, Facture

---

##  Configuration

### Prérequis

- PHP 8.2+
- MySQL 8.0+
- Composer
- WampServer ou équivalent

### Installation rapide

```bash
# Installation des dépendances
composer install

# Configuration de l'environnement
cp .env.example .env
php artisan key:generate

# Configuration de la base de données
php create-database.php
php artisan migrate:fresh --seed

# Lancement des tests
php artisan test

# Démarrage du serveur
php artisan serve
```

### Configuration .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=evaluation_sn
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_password
```

---

##  Authentification

L'API utilise **Laravel Sanctum** avec des tokens Bearer.

### Workflow d'authentification

1. **Inscription** ou **Connexion** → Récupération du token
2. Utilisation du token dans le header: `Authorization: Bearer {token}`
3. **Déconnexion** → Révocation du token

### Headers requis

```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer {votre_token}
```

---

##  Endpoints

### Authentification

#### 1. Inscription

Créer un nouveau compte utilisateur.

```http
POST /api/register
```

**Body**:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Réponse (201)**:
```json
{
  "message": "Utilisateur créé avec succès",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-03-10T10:00:00.000000Z",
    "updated_at": "2026-03-10T10:00:00.000000Z"
  },
  "access_token": "1|abcdef123456...",
  "token_type": "Bearer"
}
```

**Validations**:
- `name`: requis, string, max 255 caractères
- `email`: requis, email valide, unique
- `password`: requis, min 8 caractères, confirmation requise

---

#### 2. Connexion

Se connecter avec un compte existant.

```http
POST /api/login
```

**Body**:
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Réponse (200)**:
```json
{
  "message": "Connexion réussie",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-03-10T10:00:00.000000Z",
    "updated_at": "2026-03-10T10:00:00.000000Z"
  },
  "access_token": "2|xyz789...",
  "token_type": "Bearer"
}
```

**Erreur (422)**:
```json
{
  "message": "Les identifiants fournis sont incorrects.",
  "errors": {
    "email": ["Les identifiants fournis sont incorrects."]
  }
}
```

---

#### 3. Déconnexion

Révoquer le token actuel.

```http
POST /api/logout
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
{
  "message": "Déconnexion réussie"
}
```

---

#### 4. Profil utilisateur

Récupérer les informations de l'utilisateur connecté.

```http
GET /api/me
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "created_at": "2026-03-10T10:00:00.000000Z",
    "updated_at": "2026-03-10T10:00:00.000000Z"
  }
}
```

---

### Abonnés

Tous les endpoints nécessitent l'authentification.

#### 1. Liste des abonnés

Récupérer tous les abonnés avec leurs factures.

```http
GET /api/abonne
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
[
  {
    "id": 1,
    "nom": "Dupont",
    "prenom": "Marie",
    "ville": "Yaounde",
    "quartier": "Bastos",
    "numeroCompteur": "YAO-2024-001",
    "typeAbonnement": "Domestique",
    "created_at": "2026-03-10T10:00:00.000000Z",
    "updated_at": "2026-03-10T10:00:00.000000Z",
    "factures": [
      {
        "id": 1,
        "abonne_id": 1,
        "consommation": 15.5,
        "montantTotal": 7750,
        "dateEmission": "2026-03-01",
        "statut": "Payee"
      }
    ]
  }
]
```

---

#### 2. Créer un abonné

Ajouter un nouvel abonné.

```http
POST /api/abonne
```

**Headers**: `Authorization: Bearer {token}`

**Body**:
```json
{
  "nom": "Dupont",
  "prenom": "Marie",
  "ville": "Yaounde",
  "quartier": "Bastos",
  "numeroCompteur": "YAO-2024-001",
  "typeAbonnement": "Domestique"
}
```

**Réponse (201)**:
```json
{
  "id": 1,
  "nom": "Dupont",
  "prenom": "Marie",
  "ville": "Yaounde",
  "quartier": "Bastos",
  "numeroCompteur": "YAO-2024-001",
  "typeAbonnement": "Domestique",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:00:00.000000Z"
}
```

**Validations**:
- `nom`: requis, string, max 255 caractères
- `prenom`: requis, string, max 255 caractères
- `ville`: requis, valeurs: `Yaounde`, `Douala`, `Bafoussam`, `Garoua`
- `quartier`: optionnel, string
- `numeroCompteur`: requis, unique
- `typeAbonnement`: requis, valeurs: `Domestique`, `Professionnel`

---

#### 3. Détails d'un abonné

Récupérer un abonné spécifique.

```http
GET /api/abonne/{id}
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
{
  "id": 1,
  "nom": "Dupont",
  "prenom": "Marie",
  "ville": "Yaounde",
  "quartier": "Bastos",
  "numeroCompteur": "YAO-2024-001",
  "typeAbonnement": "Domestique",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:00:00.000000Z"
}
```

**Erreur (404)**:
```json
{
  "message": "No query results for model [App\\Models\\Abonne] {id}"
}
```

---

#### 4. Modifier un abonné

Mettre à jour les informations d'un abonné.

```http
PUT /api/abonne/{id}
```

**Headers**: `Authorization: Bearer {token}`

**Body**:
```json
{
  "nom": "Dupont",
  "prenom": "Marie-Claire",
  "ville": "Douala",
  "quartier": "Akwa",
  "numeroCompteur": "YAO-2024-001",
  "typeAbonnement": "Professionnel"
}
```

**Réponse (200)**:
```json
{
  "id": 1,
  "nom": "Dupont",
  "prenom": "Marie-Claire",
  "ville": "Douala",
  "quartier": "Akwa",
  "numeroCompteur": "YAO-2024-001",
  "typeAbonnement": "Professionnel",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:15:00.000000Z"
}
```

---

#### 5. Supprimer un abonné

Supprimer un abonné.

```http
DELETE /api/abonne/{id}
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
{
  "id": 1,
  "nom": "Dupont",
  "prenom": "Marie",
  "ville": "Yaounde",
  "quartier": "Bastos",
  "numeroCompteur": "YAO-2024-001",
  "typeAbonnement": "Domestique",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:00:00.000000Z"
}
```

---

### Factures

Tous les endpoints nécessitent l'authentification.

#### 1. Liste des factures

Récupérer toutes les factures avec les informations des abonnés.

```http
GET /api/factures
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
[
  {
    "id": 1,
    "abonne_id": 1,
    "consommation": 15.5,
    "montantTotal": 7750,
    "dateEmission": "2026-03-01",
    "statut": "Payee",
    "created_at": "2026-03-10T10:00:00.000000Z",
    "updated_at": "2026-03-10T10:00:00.000000Z",
    "abonne": {
      "id": 1,
      "nom": "Dupont",
      "prenom": "Marie",
      "ville": "Yaounde",
      "numeroCompteur": "YAO-2024-001"
    }
  }
]
```

---

#### 2. Créer une facture

Ajouter une nouvelle facture.

```http
POST /api/factures
```

**Headers**: `Authorization: Bearer {token}`

**Body**:
```json
{
  "abonne_id": 1,
  "consommation": 15.5,
  "montantTotal": 7750,
  "dateEmission": "2026-03-01",
  "statut": "Emise"
}
```

**Réponse (201)**:
```json
{
  "id": 1,
  "abonne_id": 1,
  "consommation": 15.5,
  "montantTotal": 7750,
  "dateEmission": "2026-03-01",
  "statut": "Emise",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:00:00.000000Z"
}
```

**Validations**:
- `abonne_id`: requis, doit exister dans la table abonnes
- `consommation`: requis, numérique, minimum 0
- `montantTotal`: requis, numérique, minimum 0
- `dateEmission`: requis, format date valide
- `statut`: requis, valeurs: `Emise`, `Payee`

---

#### 3. Détails d'une facture

Récupérer une facture spécifique avec les informations de l'abonné.

```http
GET /api/factures/{id}
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
{
  "id": 1,
  "abonne_id": 1,
  "consommation": 15.5,
  "montantTotal": 7750,
  "dateEmission": "2026-03-01",
  "statut": "Payee",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:00:00.000000Z",
  "abonne": {
    "id": 1,
    "nom": "Dupont",
    "prenom": "Marie",
    "ville": "Yaounde",
    "quartier": "Bastos",
    "numeroCompteur": "YAO-2024-001",
    "typeAbonnement": "Domestique"
  }
}
```

---

#### 4. Modifier une facture

Mettre à jour une facture (mise à jour partielle possible).

```http
PUT /api/factures/{id}
```

**Headers**: `Authorization: Bearer {token}`

**Body** (tous les champs sont optionnels):
```json
{
  "statut": "Payee",
  "consommation": 16.0,
  "montantTotal": 8000,
  "dateEmission": "2026-03-02"
}
```

**Réponse (200)**:
```json
{
  "id": 1,
  "abonne_id": 1,
  "consommation": 16.0,
  "montantTotal": 8000,
  "dateEmission": "2026-03-02",
  "statut": "Payee",
  "created_at": "2026-03-10T10:00:00.000000Z",
  "updated_at": "2026-03-10T10:20:00.000000Z"
}
```

---

#### 5. Supprimer une facture

Supprimer une facture.

```http
DELETE /api/factures/{id}
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
{
  "message": "Facture supprimée avec succès"
}
```

---

#### 6. Factures d'un abonné

Récupérer toutes les factures d'un abonné spécifique.

```http
GET /api/abonne/{abonneId}/factures
```

**Headers**: `Authorization: Bearer {token}`

**Réponse (200)**:
```json
[
  {
    "id": 1,
    "abonne_id": 1,
    "consommation": 15.5,
    "montantTotal": 7750,
    "dateEmission": "2026-03-01",
    "statut": "Payee",
    "created_at": "2026-03-10T10:00:00.000000Z",
    "updated_at": "2026-03-10T10:00:00.000000Z"
  },
  {
    "id": 2,
    "abonne_id": 1,
    "consommation": 18.2,
    "montantTotal": 9100,
    "dateEmission": "2026-02-01",
    "statut": "Emise",
    "created_at": "2026-02-10T10:00:00.000000Z",
    "updated_at": "2026-02-10T10:00:00.000000Z"
  }
]
```

---

##  Modèles de données

### User

```json
{
  "id": "integer",
  "name": "string",
  "email": "string (unique)",
  "email_verified_at": "timestamp|null",
  "password": "string (hashed)",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

### Abonne

```json
{
  "id": "integer",
  "nom": "string (max: 255)",
  "prenom": "string (max: 255)",
  "ville": "enum (Yaounde, Douala, Bafoussam, Garoua)",
  "quartier": "string|null",
  "numeroCompteur": "string (unique)",
  "typeAbonnement": "enum (Domestique, Professionnel)",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Relations**:
- `factures`: hasMany → Facture

### Facture

```json
{
  "id": "integer",
  "abonne_id": "integer (foreign key)",
  "consommation": "decimal (8,2)",
  "montantTotal": "decimal (10,2)",
  "dateEmission": "date",
  "statut": "enum (Emise, Payee)",
  "created_at": "timestamp",
  "updated_at": "timestamp"
}
```

**Relations**:
- `abonne`: belongsTo → Abonne

---

##  Codes d'erreur

| Code | Signification | Description |
|------|---------------|-------------|
| 200 | OK | Requête réussie |
| 201 | Created | Ressource créée avec succès |
| 401 | Unauthorized | Token manquant ou invalide |
| 404 | Not Found | Ressource non trouvée |
| 422 | Unprocessable Entity | Erreur de validation |
| 500 | Internal Server Error | Erreur serveur |

### Format des erreurs de validation (422)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": [
      "The email field is required."
    ],
    "password": [
      "The password must be at least 8 characters."
    ]
  }
}
```

### Erreur d'authentification (401)

```json
{
  "message": "Unauthenticated."
}
```

---

##  Exemples d'utilisation

### Exemple complet avec cURL

#### 1. Inscription

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Marie Dupont",
    "email": "marie@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### 2. Connexion

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "marie@example.com",
    "password": "password123"
  }'
```

Réponse:
```json
{
  "access_token": "1|abcdef123456..."
}
```

#### 3. Créer un abonné

```bash
curl -X POST http://localhost:8000/api/abonne \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abcdef123456..." \
  -d '{
    "nom": "Kamga",
    "prenom": "Paul",
    "ville": "Douala",
    "quartier": "Akwa",
    "numeroCompteur": "DLA-2024-001",
    "typeAbonnement": "Domestique"
  }'
```

#### 4. Créer une facture

```bash
curl -X POST http://localhost:8000/api/factures \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abcdef123456..." \
  -d '{
    "abonne_id": 1,
    "consommation": 20.5,
    "montantTotal": 10250,
    "dateEmission": "2026-03-10",
    "statut": "Emise"
  }'
```

#### 5. Récupérer les factures d'un abonné

```bash
curl -X GET http://localhost:8000/api/abonne/1/factures \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|abcdef123456..."
```

### Exemple avec JavaScript (Fetch API)

```javascript
// Configuration de base
const API_URL = 'http://localhost:8000/api';
let token = null;

// Fonction helper pour les requêtes
async function apiRequest(endpoint, options = {}) {
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...options.headers
  };

  if (token) {
    headers['Authorization'] = `Bearer ${token}`;
  }

  const response = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers
  });

  return response.json();
}

// 1. Connexion
async function login() {
  const data = await apiRequest('/login', {
    method: 'POST',
    body: JSON.stringify({
      email: 'marie@example.com',
      password: 'password123'
    })
  });
  
  token = data.access_token;
  console.log('Connecté:', data.user);
}

// 2. Créer un abonné
async function createAbonne() {
  const abonne = await apiRequest('/abonne', {
    method: 'POST',
    body: JSON.stringify({
      nom: 'Kamga',
      prenom: 'Paul',
      ville: 'Douala',
      quartier: 'Akwa',
      numeroCompteur: 'DLA-2024-001',
      typeAbonnement: 'Domestique'
    })
  });
  
  console.log('Abonné créé:', abonne);
  return abonne;
}

// 3. Récupérer tous les abonnés
async function getAbonnes() {
  const abonnes = await apiRequest('/abonne');
  console.log('Liste des abonnés:', abonnes);
  return abonnes;
}

// 4. Créer une facture
async function createFacture(abonneId) {
  const facture = await apiRequest('/factures', {
    method: 'POST',
    body: JSON.stringify({
      abonne_id: abonneId,
      consommation: 20.5,
      montantTotal: 10250,
      dateEmission: '2026-03-10',
      statut: 'Emise'
    })
  });
  
  console.log('Facture créée:', facture);
  return facture;
}

// Utilisation
(async () => {
  await login();
  const abonne = await createAbonne();
  await createFacture(abonne.id);
  await getAbonnes();
})();
```

### Exemple avec Python (requests)

```python
import requests

API_URL = 'http://localhost:8000/api'
token = None

def api_request(endpoint, method='GET', data=None):
    headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
    
    if token:
        headers['Authorization'] = f'Bearer {token}'
    
    url = f'{API_URL}{endpoint}'
    
    if method == 'GET':
        response = requests.get(url, headers=headers)
    elif method == 'POST':
        response = requests.post(url, json=data, headers=headers)
    elif method == 'PUT':
        response = requests.put(url, json=data, headers=headers)
    elif method == 'DELETE':
        response = requests.delete(url, headers=headers)
    
    return response.json()

# 1. Connexion
def login():
    global token
    data = api_request('/login', 'POST', {
        'email': 'marie@example.com',
        'password': 'password123'
    })
    token = data['access_token']
    print('Connecté:', data['user'])

# 2. Créer un abonné
def create_abonne():
    abonne = api_request('/abonne', 'POST', {
        'nom': 'Kamga',
        'prenom': 'Paul',
        'ville': 'Douala',
        'quartier': 'Akwa',
        'numeroCompteur': 'DLA-2024-001',
        'typeAbonnement': 'Domestique'
    })
    print('Abonné créé:', abonne)
    return abonne

# 3. Récupérer tous les abonnés
def get_abonnes():
    abonnes = api_request('/abonne')
    print('Liste des abonnés:', abonnes)
    return abonnes

# Utilisation
if __name__ == '__main__':
    login()
    abonne = create_abonne()
    get_abonnes()
```

---

##  Tests

### Lancer les tests

```bash
# Tous les tests
php artisan test

# Tests spécifiques
php artisan test --filter AuthTest
php artisan test --filter AbonneTest
php artisan test --filter FactureTest

# Avec couverture
php artisan test --coverage
```

### Résultats des tests

```
PASS  Tests\Feature\AuthTest
✓ user can register
✓ user can login
✓ user can logout
✓ user can get profile

PASS  Tests\Feature\AbonneTest
✓ can list abonnes
✓ can create abonne
✓ can show abonne
✓ can update abonne
✓ can delete abonne

PASS  Tests\Feature\FactureTest
✓ can list factures
✓ can create facture
✓ can show facture
✓ can update facture
✓ can delete facture
✓ can get factures by abonne

Tests:    26 passed (26 assertions)
Duration: 2.45s
```

---

##  Notes importantes

### Sécurité

- Tous les endpoints (sauf `/register` et `/login`) nécessitent une authentification
- Les mots de passe sont hashés avec bcrypt
- Les tokens sont révoqués lors de la déconnexion
- Validation stricte des données entrantes

### Bonnes pratiques

- Toujours inclure les headers `Content-Type` et `Accept`
- Gérer les erreurs 401 (token expiré/invalide)
- Valider les données côté client avant l'envoi
- Utiliser HTTPS en production

### Limitations

- Pas de pagination implémentée (à ajouter pour de grandes quantités de données)
- Pas de filtrage/recherche avancée
- Pas de gestion des rôles/permissions

---

##  Ressources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [REST API Best Practices](https://restfulapi.net/)

---

**Version**: 1.0.0  
**Dernière mise à jour**: 10 Mars 2026  
**Auteur**: Équipe de développement
