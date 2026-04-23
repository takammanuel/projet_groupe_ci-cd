<?php

/**
 * Script pour créer la base de données MySQL
 */

$host = '127.0.0.1';
$username = 'root';
$password = '';
$database = 'evaluation_sn';

try {
    // Connexion sans spécifier de base de données
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données
    $sql = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $pdo->exec($sql);
    
    echo "✅ Base de données '$database' créée avec succès!\n";
    echo "📊 Vous pouvez maintenant voir la base dans phpMyAdmin\n";
    echo "🔗 URL: http://localhost/phpmyadmin\n";
    
} catch (PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "\n";
    echo "Vérifiez que:\n";
    echo "1. WampServer est démarré\n";
    echo "2. MySQL est en cours d'exécution\n";
    echo "3. Le mot de passe root est correct (par défaut vide)\n";
    exit(1);
}
