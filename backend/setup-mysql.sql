-- Script de configuration MySQL pour l'API Gestion Abonnés et Factures
-- À exécuter dans phpMyAdmin ou la console MySQL

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS evaluation_sn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Utiliser la base de données
USE evaluation_sn;

-- Accorder tous les privilèges à root
GRANT ALL PRIVILEGES ON evaluation_sn.* TO 'root'@'localhost';
GRANT ALL PRIVILEGES ON evaluation_sn.* TO 'root'@'127.0.0.1';
GRANT ALL PRIVILEGES ON evaluation_sn.* TO 'root'@'%';

-- Recharger les privilèges
FLUSH PRIVILEGES;

-- Afficher un message de confirmation
SELECT 'Base de données créée et privilèges accordés avec succès!' AS Message;
