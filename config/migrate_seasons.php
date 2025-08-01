<?php
// Migration script for Season System
// Configuration du fuseau horaire
require_once __DIR__ . '/timezone.php';
echo "🏴‍☠️ Migration: Ajout du système de saisons...\n\n";

// Configuration de la base de données
$host = 'localhost';
$username = 'skullking_user';
$password = 'SkullKing_2025!';
$db_name = 'skull_king_league';

try {
    echo "🔗 Connexion à MySQL...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie\n\n";
    
    // 1. Créer la table seasons
    echo "🏆 Création de la table seasons...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS seasons (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        end_date TIMESTAMP NULL,
        is_current BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_current_season (is_current),
        INDEX idx_season_dates (start_date, end_date)
    )");
    echo "✅ Table seasons créée\n";
    
    // 2. Ajouter la colonne season_id à la table games
    echo "🎮 Mise à jour de la table games...\n";
    
    // Vérifier si la colonne existe déjà
    $result = $pdo->query("SHOW COLUMNS FROM games LIKE 'season_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE games ADD COLUMN season_id INT DEFAULT NULL");
        $pdo->exec("ALTER TABLE games ADD INDEX idx_game_season (season_id)");
        echo "✅ Colonne season_id ajoutée à games\n";
    } else {
        echo "ℹ️  Colonne season_id existe déjà\n";
    }
    
    // 3. Créer la première saison si aucune saison n'existe
    echo "🌟 Vérification des saisons existantes...\n";
    $count = $pdo->query("SELECT COUNT(*) FROM seasons")->fetchColumn();
    
    if ($count == 0) {
        // Créer la saison 1
        $pdo->exec("INSERT INTO seasons (name, is_current) VALUES ('Saison 1', TRUE)");
        $season_id = $pdo->lastInsertId();
        echo "✅ Saison 1 créée (ID: $season_id)\n";
        
        // Associer toutes les parties existantes à la saison 1
        $pdo->exec("UPDATE games SET season_id = $season_id WHERE season_id IS NULL");
        $updated_games = $pdo->query("SELECT COUNT(*) FROM games WHERE season_id = $season_id")->fetchColumn();
        echo "✅ $updated_games parties associées à la Saison 1\n";
    } else {
        echo "ℹ️  Saisons existantes trouvées ($count saisons)\n";
    }
    
    // 4. Ajouter season_id à elo_history si elle n'existe pas
    echo "📈 Mise à jour de la table elo_history...\n";
    $result = $pdo->query("SHOW COLUMNS FROM elo_history LIKE 'season_id'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE elo_history ADD COLUMN season_id INT DEFAULT NULL");
        $pdo->exec("ALTER TABLE elo_history ADD INDEX idx_elo_season (season_id)");
        echo "✅ Colonne season_id ajoutée à elo_history\n";
        
        // Associer l'historique ELO existant à la saison courante
        $current_season = $pdo->query("SELECT id FROM seasons WHERE is_current = TRUE LIMIT 1")->fetchColumn();
        if ($current_season) {
            $pdo->exec("UPDATE elo_history SET season_id = $current_season WHERE season_id IS NULL");
            echo "✅ Historique ELO associé à la saison courante\n";
        }
    } else {
        echo "ℹ️  Colonne season_id existe déjà dans elo_history\n";
    }
    
    // 5. Créer la table season_stats pour les statistiques par saison
    echo "📊 Création de la table season_stats...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS season_stats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        season_id INT NOT NULL,
        user_id INT NOT NULL,
        final_elo INT NOT NULL,
        initial_elo INT DEFAULT 1000,
        parties_jouees INT DEFAULT 0,
        victoires INT DEFAULT 0,
        final_rank INT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (season_id) REFERENCES seasons(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE KEY unique_season_user (season_id, user_id),
        INDEX idx_season_rank (season_id, final_rank)
    )");
    echo "✅ Table season_stats créée\n";
    
    echo "\n🎉 Migration terminée avec succès !\n";
    echo "🌐 Le système de saisons est maintenant prêt\n\n";
    
} catch(PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    echo "💡 Vérifiez que la base de données est accessible et que l'utilisateur a les permissions nécessaires.\n";
}
?>