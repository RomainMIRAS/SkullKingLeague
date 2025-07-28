<?php
// Script d'initialisation de la base de données
echo "🏴‍☠️ Initialisation de Skull King League...\n\n";

// Configuration de la base de données
$host = 'localhost';
$username = 'skullking_user';
$password = 'SkullKing_2025!'; // Utilisateur dédié pour l'application
$db_name = 'skull_king_league';

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
        is_ranked BOOLEAN DEFAULT TRUE COMMENT 'Whether the game affects ELO ratings',
        FOREIGN KEY (gagnant_id) REFERENCES users(id)
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
    $pdo->exec("CREATE INDEX idx_game_players_order ON game_players(game_id, player_order)");
    $pdo->exec("CREATE INDEX idx_rounds_starting_player ON rounds(game_id, numero_manche, starting_player_id)");
    $pdo->exec("CREATE INDEX idx_games_ranked ON games(is_ranked)");
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
        rank INT NOT NULL COMMENT 'Rang réel du joueur dans la partie',
        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (game_id) REFERENCES games(id),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "✅ Table elo_history créée\n";

    
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
