<?php
// Script d'initialisation de la base de données
echo "🏴‍☠️ Initialisation de Skull King League...\n\n";

// Configuration de la base de données
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
$username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'skullking_user';
$password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'SkullKing_2025!';
$db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'skull_king_league';

try {
    echo "🔗 Connexion à MySQL...\n";
    // Connexion sans base de données spécifique pour la créer
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie\n\n";
    
    // Créer la base de données
    echo "🗄️  Création de la base de données...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8 COLLATE utf8_general_ci");
    $pdo->exec("USE $db_name");
    echo "✅ Base de données '$db_name' créée\n\n";
    
    // Table users
    echo "👥 Création de la table users...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pseudo VARCHAR(50) NOT NULL UNIQUE,
        elo INT DEFAULT 1000,
        parties_jouees INT DEFAULT 0,
        victoires INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Table users créée\n";
    
    // Table games
    echo "🎮 Création de la table games...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS games (
        id INT AUTO_INCREMENT PRIMARY KEY,
        date_partie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        gagnant_id INT,
        status ENUM('en_cours', 'terminee') DEFAULT 'en_cours',
        season_id INT DEFAULT NULL,
        is_ranked BOOLEAN DEFAULT TRUE,
        FOREIGN KEY (gagnant_id) REFERENCES users(id),
        INDEX idx_game_season (season_id)
    )");
    echo "✅ Table games créée\n";
    
    // Table game_players
    echo "👤 Création de la table game_players...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS game_players (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        user_id INT,
        score_total INT DEFAULT 0,
        player_order INT DEFAULT 1,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "✅ Table game_players créée\n";
    
    // Table rounds
    echo "🔄 Création de la table rounds...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS rounds (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT,
        numero_manche INT,
        player_id INT,
        score INT,
        starting_player_id INT NULL,
        FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
        FOREIGN KEY (player_id) REFERENCES users(id),
        FOREIGN KEY (starting_player_id) REFERENCES users(id)
    )");
    echo "✅ Table rounds créée\n";

    // Ajouter des index pour optimiser les requêtes
    echo "📊 Ajout des index pour optimisation...\n";
    try {
        $pdo->exec("CREATE INDEX idx_game_players_order ON game_players(game_id, player_order)");
    } catch(PDOException $e) {
        // Index already exists, ignore
    }
    try {
        $pdo->exec("CREATE INDEX idx_rounds_starting_player ON rounds(game_id, numero_manche, starting_player_id)");
    } catch(PDOException $e) {
        // Index already exists, ignore
    }
    echo "✅ Index créés\n";

    // Table elo_history
    echo "📈 Création de la table elo_history...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS elo_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        game_id INT NOT NULL,
        user_id INT NOT NULL,
        old_elo INT NOT NULL,
        new_elo INT NOT NULL,
        elo_change INT NOT NULL,
        `rank` INT NOT NULL COMMENT 'Rang réel du joueur dans la partie',
        season_id INT DEFAULT NULL,
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        INDEX idx_elo_season (season_id)
    )");
    echo "✅ Table elo_history créée\n";

    // Table seasons
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
    
    // Table season_stats
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

    
    // Table admin (pour l'authentification)
    echo "🔐 Création de la table admin...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    )");
    echo "✅ Table admin créée\n";
    
    // Insérer un admin par défaut (mot de passe: admin123)
    echo "👨‍💼 Création de l'administrateur par défaut...\n";
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admin (username, password) VALUES ('admin', '$hashedPassword')");
    echo "✅ Admin créé (user: admin, pass: admin123)\n";
    
    // Insérer quelques utilisateurs de test
    echo "👥 Ajout d'utilisateurs de test...\n";
    $pdo->exec("INSERT IGNORE INTO users (pseudo) VALUES 
        ('Roméo'), 
        ('Asmae'), 
        ('Brice'), 
        ('Noah'), 
        ('Romain')");
    
    // Vérifier le nombre d'utilisateurs ajoutés
    $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    echo "✅ $count utilisateurs dans la base\n\n";
    
    // Créer la première saison
    echo "🌟 Création de la première saison...\n";
    $season_count = $pdo->query("SELECT COUNT(*) FROM seasons")->fetchColumn();
    if ($season_count == 0) {
        $pdo->exec("INSERT INTO seasons (name, is_current) VALUES ('Saison 1', TRUE)");
        $season_id = $pdo->lastInsertId();
        echo "✅ Saison 1 créée (ID: $season_id)\n";
    } else {
        echo "ℹ️  Saisons existantes trouvées ($season_count saisons)\n";
    }
    
    echo "🎉 Base de données initialisée avec succès !\n";
    echo "🌐 Vous pouvez maintenant accéder à l'application\n";
    echo "🔗 Admin: index.php?page=admin (admin/admin123)\n";
    
} catch(PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    echo "💡 Solutions possibles:\n";
    echo "- Vérifiez que MySQL/MariaDB est démarré: systemctl status mysql\n";
    echo "- Vérifiez les identifiants dans ce script\n";
    echo "- Pour MariaDB, essayez: sudo mysql -u root\n";
    echo "- Créez un utilisateur avec mot de passe si nécessaire\n\n";
    
    echo "🛠️  Commandes de dépannage:\n";
    echo "sudo systemctl start mysql\n";
    echo "sudo mysql -u root -e \"ALTER USER 'root'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';\"\n";
}
?>
