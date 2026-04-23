# Projet La Table - Application de Gestion de Recettes

Application full-stack avec backend Laravel et frontend React pour la gestion de recettes culinaires.

## 📋 Prérequis

- PHP 8.2+
- Composer
- Node.js 18+
- npm ou yarn

## 🚀 Installation

### 1. Cloner le projet

```bash
git clone https://github.com/takammanuel/projet_groupe_ci-cd.git
cd projet_groupe_ci-cd
```

### 2. Installation du Backend (Laravel)

```bash
cd backend

# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données MySQL
# Le fichier .env est configuré pour MySQL
# Assurez-vous que MySQL est installé et en cours d'exécution

# Créer la base de données
mysql -u root -p
CREATE DATABASE evaluation_sn;
exit;

# Exécuter les migrations avec les seeders
php artisan migrate:fresh --seed

# Lancer le serveur backend
php artisan serve
```

Le backend sera accessible sur `http://localhost:8000`

### 3. Installation du Frontend (React)

Ouvrir un nouveau terminal :

```bash
cd frontend

# Installer les dépendances
npm install

# Lancer le serveur de développement
npm run dev
```

Le frontend sera accessible sur `http://localhost:5173` (ou un autre port si 5173 est occupé)

## 🔐 Connexion

Utilisez ces identifiants pour vous connecter :

- **Email:** `test@test.com` ou `test@example.com`
- **Mot de passe:** `password`

## 📁 Structure du Projet

```
.
├── evaluation_SN/          # Backend Laravel
│   ├── app/
│   ├── database/
│   ├── routes/
│   └── public/
│
└── frontend/               # Frontend React
    ├── src/
    │   ├── components/     # Composants React
    │   ├── pages/          # Pages (Login, Home)
    │   ├── services/       # Services API
    │   └── context/        # Context API
    └── public/
```

## 🎨 Fonctionnalités

### Backend (Laravel)
- API REST avec Laravel 11
- Authentification avec Laravel Sanctum
- Base de données MySQL
- Gestion des abonnés, factures et réclamations
- Système de cache optimisé
- Monitoring des performances
- Documentation API complète

### Frontend (React)
- Page de connexion avec authentification
- Page d'accueil avec liste de recettes
- Filtrage par catégorie (Toutes, Entrées, Desserts, Plats)
- Barre de recherche
- Design responsive
- Gestion d'état avec Context API

## 🛠️ Technologies Utilisées

### Backend
- Laravel 11
- PHP 8.2+
- MySQL
- Laravel Sanctum (authentification)

### Frontend
- React 18
- Vite
- React Router v6
- Axios
- CSS Modules

## 📝 API Endpoints

### Authentification
- `POST /api/register` - Inscription
- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion
- `GET /api/me` - Profil utilisateur

### Abonnés
- `GET /api/abonne` - Liste des abonnés
- `POST /api/abonne` - Créer un abonné
- `GET /api/abonne/{id}` - Détails d'un abonné
- `PUT /api/abonne/{id}` - Modifier un abonné
- `DELETE /api/abonne/{id}` - Supprimer un abonné

### Factures
- `GET /api/factures` - Liste des factures
- `POST /api/factures` - Créer une facture
- `GET /api/factures/{id}` - Détails d'une facture
- `GET /api/abonne/{abonneId}/factures` - Factures d'un abonné

### Réclamations
- `GET /api/reclamations` - Liste des réclamations
- `POST /api/reclamations` - Créer une réclamation
- `GET /api/reclamations/{id}` - Détails d'une réclamation

### Performance & Cache
- `GET /api/cache/stats` - Statistiques du cache
- `DELETE /api/cache/clear` - Vider le cache
- `GET /api/performance/queries` - Statistiques des requêtes
- `GET /api/logs` - Logs de l'application

## 🎨 Design

L'application utilise une palette de couleurs moderne :
- Orange principal: `#FF6B35`
- Noir: `#1A1A1A`
- Gris clair: `#F5F5F5`
- Blanc: `#FFFFFF`

## 🐛 Dépannage

### Le backend ne démarre pas
- Vérifiez que PHP est installé : `php -v`
- Vérifiez que le port 8000 n'est pas déjà utilisé

### Le frontend ne démarre pas
- Supprimez `node_modules` et `package-lock.json`
- Réinstallez : `npm install`

### Erreur de connexion
- Vérifiez que le backend tourne sur `http://localhost:8000`
- Vérifiez que l'utilisateur existe dans la base de données
- Consultez les logs du backend

## 👥 Équipe

Projet développé dans le cadre d'un travail d'équipe.

## 📄 Licence

Ce projet est sous licence MIT.
