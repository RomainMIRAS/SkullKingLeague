<?php
// Script to apply database migration for ranked/unranked games
echo "🏴‍☠️ Applying migration: Add ranked/unranked game support...\n\n";

// Configuration de la base de données
$host = 'localhost';
$username = 'skullking_user';
$password = 'SkullKing_2025!';
$db_name = 'skull_king_league';

try {
    echo "🔗 Connexion à la base de données...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion réussie\n\n";
    
    // Vérifier si la colonne existe déjà
    echo "🔍 Vérification de l'existence de la colonne is_ranked...\n";
    $check_query = "SHOW COLUMNS FROM games LIKE 'is_ranked'";
    $result = $pdo->query($check_query);
    
    if ($result->rowCount() > 0) {
        echo "ℹ️  La colonne is_ranked existe déjà, migration ignorée.\n";
    } else {
        echo "➕ Ajout de la colonne is_ranked...\n";
        
        // Lire et exécuter le script SQL
        $sql_content = file_get_contents(__DIR__ . '/add_ranked_column.sql');
        $statements = explode(';', $sql_content);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                $pdo->exec($statement);
            }
        }
        
        echo "✅ Colonne is_ranked ajoutée avec succès\n";
        echo "   - Type: BOOLEAN DEFAULT TRUE\n";
        echo "   - Index: idx_games_ranked créé\n";
    }
    
    // Vérifier le nombre de parties existantes
    $count_query = "SELECT COUNT(*) as total, 
                           SUM(CASE WHEN is_ranked = 1 THEN 1 ELSE 0 END) as ranked,
                           SUM(CASE WHEN is_ranked = 0 THEN 1 ELSE 0 END) as unranked
                    FROM games";
    $stats = $pdo->query($count_query)->fetch(PDO::FETCH_ASSOC);
    
    echo "\n📊 Statistiques des parties:\n";
    echo "   - Total: {$stats['total']} parties\n";
    echo "   - Classées: {$stats['ranked']} parties\n";
    echo "   - Amicales: {$stats['unranked']} parties\n";
    
    echo "\n🎉 Migration terminée avec succès !\n";
    echo "🌐 Les nouvelles parties peuvent maintenant être classées ou amicales.\n";
    
} catch(PDOException $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n\n";
    echo "💡 Solutions possibles:\n";
    echo "- Vérifiez que MySQL/MariaDB est démarré\n";
    echo "- Vérifiez les identifiants de connexion\n";
    echo "- Assurez-vous que la base de données existe\n";
    exit(1);
}
?>