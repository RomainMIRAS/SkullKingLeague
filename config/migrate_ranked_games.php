<?php
// Migration script to add is_ranked column to existing games table
echo "🏴‍☠️ Migration: Ajout du support des parties classées/non-classées...\n\n";

// Configuration de la base de données
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'skullking_user';
$password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'SkullKing_2025!';
$db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'skull_king_league';

try {
    echo "🔗 Connexion à la base de données...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie\n\n";
    
    // Vérifier si la colonne is_ranked existe déjà
    echo "🔍 Vérification de l'existence de la colonne is_ranked...\n";
    $result = $pdo->query("SHOW COLUMNS FROM games LIKE 'is_ranked'")->fetch();
    
    if (!$result) {
        echo "➕ Ajout de la colonne is_ranked à la table games...\n";
        $pdo->exec("ALTER TABLE games ADD COLUMN is_ranked BOOLEAN DEFAULT TRUE AFTER season_id");
        echo "✅ Colonne is_ranked ajoutée avec succès\n";
        echo "ℹ️  Toutes les parties existantes sont marquées comme classées par défaut\n";
    } else {
        echo "ℹ️  La colonne is_ranked existe déjà, aucune action nécessaire\n";
    }
    
    echo "\n🎉 Migration terminée avec succès !\n";
    
} catch(PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    echo "💡 Assurez-vous que la base de données existe et que les identifiants sont corrects.\n";
}
?>