<?php
// Test simple de connexion
echo "🔗 Test de connexion à la base de données...\n";

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Test de connexion sans base spécifique
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion MySQL réussie\n";
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS skull_king_league CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "✅ Base de données créée\n";
    
    // Se connecter à la base
    $pdo->exec("USE skull_king_league");
    echo "✅ Connexion à skull_king_league\n";
    
    echo "🎉 Test réussi !\n";
    
} catch(PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "💡 Solutions possibles:\n";
    echo "- Vérifiez que MySQL/MariaDB est démarré\n";
    echo "- Vérifiez les identifiants de connexion\n";
    echo "- Vérifiez les permissions de l'utilisateur\n";
}
?>
