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
cd evaluation_SN

# Installer les dépendances
composer install

# Copier le fichier d'environnement
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données (SQLite par défaut)
# Le fichier .env est déjà configuré pour SQLite

# Exécuter les migrations
php artisan migrate:fresh --seed

# Créer un utilisateur de test
php artisan tinker --execute="App\Models\User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => Hash::make('admin123')]);"

# Lancer le serveur backend
php -S localhost:8000 -t public
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

- **Email:** `admin@test.com`
- **Mot de passe:** `admin123`

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
- Base de données SQLite
- Gestion des utilisateurs
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
- SQLite
- Laravel Sanctum (authentification)

### Frontend
- React 18
- Vite
- React Router v6
- Axios
- CSS Modules

## 📝 API Endpoints

### Authentification
- `POST /api/login` - Connexion
- `POST /api/logout` - Déconnexion
- `GET /api/me` - Profil utilisateur

### Recettes (à venir)
- `GET /api/recettes` - Liste des recettes
- `POST /api/recettes` - Créer une recette
- `GET /api/recettes/{id}` - Détails d'une recette

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
