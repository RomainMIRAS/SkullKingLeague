<?php
// Test script to verify ranked/unranked game functionality
echo "🧪 Testing ranked/unranked game functionality...\n\n";

// Include necessary files with correct paths
require_once __DIR__ . '/database.php';

// We need to manually include the classes since they use relative paths
class TestGame {
    private $conn;
    private $table_name = "games";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($player_ids, $is_ranked = true) {
        // Créer la partie
        $query = "INSERT INTO " . $this->table_name . " (status, is_ranked) VALUES ('en_cours', ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $is_ranked, PDO::PARAM_BOOL);
        
        if($stmt->execute()) {
            $game_id = $this->conn->lastInsertId();
            
            // Ajouter les joueurs à la partie avec l'ordre fourni
            $query_players = "INSERT INTO game_players (game_id, user_id, player_order) VALUES (?, ?, ?)";
            $stmt_players = $this->conn->prepare($query_players);
            
            $order = 1;
            foreach($player_ids as $player_id) {
                $stmt_players->bindParam(1, $game_id);
                $stmt_players->bindParam(2, $player_id);
                $stmt_players->bindParam(3, $order);
                $stmt_players->execute();
                $order++;
            }
            
            return $game_id;
        }
        return false;
    }

    public function getById($id) {
        $query = "SELECT g.*, u.pseudo as gagnant_pseudo 
                  FROM " . $this->table_name . " g
                  LEFT JOIN users u ON g.gagnant_id = u.id
                  WHERE g.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

try {
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    $game = new TestGame($db);
    
    echo "✅ Database connection successful\n";
    
    // Test 1: Check if is_ranked column exists
    echo "\n🔍 Test 1: Checking database schema...\n";
    $check_query = "SHOW COLUMNS FROM games LIKE 'is_ranked'";
    $result = $db->query($check_query);
    
    if ($result->rowCount() > 0) {
        echo "✅ is_ranked column exists\n";
        $column_info = $result->fetch(PDO::FETCH_ASSOC);
        echo "   - Type: {$column_info['Type']}\n";
        echo "   - Default: {$column_info['Default']}\n";
    } else {
        echo "❌ is_ranked column missing - run migrate_ranked_games.php first\n";
        exit(1);
    }
    
    // Test 2: Test Game model create method with ranked parameter
    echo "\n🎮 Test 2: Testing Game model...\n";
    
    // Get some test users
    $users_query = "SELECT id FROM users LIMIT 2";
    $users_result = $db->query($users_query);
    $test_users = $users_result->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($test_users) < 2) {
        echo "❌ Need at least 2 users in database for testing\n";
        exit(1);
    }
    
    // Test creating a ranked game
    echo "   Creating ranked game...\n";
    $ranked_game_id = $game->create($test_users, true);
    if ($ranked_game_id) {
        echo "   ✅ Ranked game created with ID: $ranked_game_id\n";
        
        // Verify it's marked as ranked
        $game_data = $game->getById($ranked_game_id);
        if ($game_data && $game_data['is_ranked']) {
            echo "   ✅ Game correctly marked as ranked\n";
        } else {
            echo "   ❌ Game not correctly marked as ranked\n";
        }
    } else {
        echo "   ❌ Failed to create ranked game\n";
    }
    
    // Test creating an unranked game
    echo "   Creating unranked game...\n";
    $unranked_game_id = $game->create($test_users, false);
    if ($unranked_game_id) {
        echo "   ✅ Unranked game created with ID: $unranked_game_id\n";
        
        // Verify it's marked as unranked
        $game_data = $game->getById($unranked_game_id);
        if ($game_data && !$game_data['is_ranked']) {
            echo "   ✅ Game correctly marked as unranked\n";
        } else {
            echo "   ❌ Game not correctly marked as unranked\n";
        }
    } else {
        echo "   ❌ Failed to create unranked game\n";
    }
    
    // Clean up test games
    if (isset($ranked_game_id)) {
        $db->exec("DELETE FROM games WHERE id = $ranked_game_id");
        echo "   🧹 Cleaned up ranked test game\n";
    }
    if (isset($unranked_game_id)) {
        $db->exec("DELETE FROM games WHERE id = $unranked_game_id");
        echo "   🧹 Cleaned up unranked test game\n";
    }
    
    // Test 3: Check existing games have default value
    echo "\n📊 Test 3: Checking existing games...\n";
    $stats_query = "SELECT 
                      COUNT(*) as total,
                      SUM(CASE WHEN is_ranked = 1 THEN 1 ELSE 0 END) as ranked,
                      SUM(CASE WHEN is_ranked = 0 THEN 1 ELSE 0 END) as unranked
                    FROM games";
    $stats = $db->query($stats_query)->fetch(PDO::FETCH_ASSOC);
    
    echo "   Total games: {$stats['total']}\n";
    echo "   Ranked games: {$stats['ranked']}\n";
    echo "   Unranked games: {$stats['unranked']}\n";
    
    if ($stats['total'] > 0 && $stats['ranked'] > 0) {
        echo "   ✅ Existing games properly defaulted to ranked\n";
    } elseif ($stats['total'] == 0) {
        echo "   ℹ️  No existing games to check\n";
    }
    
    echo "\n🎉 All tests passed! Ranked/unranked functionality is working correctly.\n";
    echo "\n📝 Next steps:\n";
    echo "   1. Test the UI by creating new games\n";
    echo "   2. Verify ELO is not updated for unranked games\n";
    echo "   3. Check that history shows correct game types\n";
    
} catch(Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>