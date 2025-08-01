<?php
require_once '../config/database.php';

class Season {
    private $conn;
    private $table_name = "seasons";

    public $id;
    public $name;
    public $start_date;
    public $end_date;
    public $is_current;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get current active season
     */
    public function getCurrentSeason() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_current = TRUE LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all seasons ordered by start date (newest first)
     */
    public function getAllSeasons() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get season by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Start a new season - Admin only function
     * This ends the current season and starts a new one, resetting all player ELOs to 1000
     * It also deletes any in-progress games
     */
    public function startNewSeason($season_name) {
        try {
            $this->conn->beginTransaction();

            // 1. Get current season
            $current_season = $this->getCurrentSeason();
            
            if ($current_season) {
                // End current season
                $this->endSeason($current_season['id']);
                
                // Delete any in-progress games from the current season
                $this->deleteInProgressGames($current_season['id']);
            }

            // 2. Create new season
            $query = "INSERT INTO " . $this->table_name . " (name, is_current) VALUES (?, TRUE)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $season_name);
            $stmt->execute();
            $new_season_id = $this->conn->lastInsertId();

            // 3. Reset all user ELOs to 1000
            $reset_query = "UPDATE users SET elo = 1000";
            $this->conn->exec($reset_query);

            $this->conn->commit();
            return $new_season_id;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }

    /**
     * End a season by setting end_date and is_current to false
     * Also saves final statistics for the season
     */
    private function endSeason($season_id) {
        // 1. Set end date and mark as not current
        $query = "UPDATE " . $this->table_name . " 
                  SET end_date = CURRENT_TIMESTAMP, is_current = FALSE 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $season_id);
        $stmt->execute();

        // 2. Save final season statistics
        $this->saveFinalStats($season_id);
    }

    /**
     * Save final statistics for a completed season
     */
    private function saveFinalStats($season_id) {
        // Get final standings for the season
        $query = "SELECT u.id, u.pseudo, u.elo, u.parties_jouees, u.victoires
                  FROM users u
                  ORDER BY u.elo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Save final stats for each user
        $insert_query = "INSERT INTO season_stats 
                        (season_id, user_id, final_elo, parties_jouees, victoires, final_rank) 
                        VALUES (?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        final_elo = VALUES(final_elo),
                        parties_jouees = VALUES(parties_jouees),
                        victoires = VALUES(victoires),
                        final_rank = VALUES(final_rank)";
        $insert_stmt = $this->conn->prepare($insert_query);

        $rank = 1;
        foreach ($users as $user) {
            $insert_stmt->execute([
                $season_id,
                $user['id'],
                $user['elo'],
                $user['parties_jouees'],
                $user['victoires'],
                $rank
            ]);
            $rank++;
        }
    }

    /**
     * Get season statistics/final standings
     */
    public function getSeasonStats($season_id) {
        $query = "SELECT ss.*, u.pseudo
                  FROM season_stats ss
                  JOIN users u ON ss.user_id = u.id
                  WHERE ss.season_id = ?
                  ORDER BY ss.final_rank ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $season_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get games for a specific season
     * @param int $season_id The ID of the season
     * @param int $limit The maximum number of games to return (0 = no limit)
     * @return PDOStatement The query result
     */
    public function getSeasonGames($season_id, $limit = 50) {
        // Base query without LIMIT clause
        $base_query = "SELECT g.*, u.pseudo as gagnant_pseudo,
                         COUNT(gp.user_id) as nombre_joueurs
                  FROM games g
                  LEFT JOIN users u ON g.gagnant_id = u.id
                  LEFT JOIN game_players gp ON g.id = gp.game_id
                  WHERE g.season_id = ? AND g.status = 'terminee'
                  GROUP BY g.id
                  ORDER BY g.date_partie DESC";
                  
        // If limit is 0, don't apply a limit
        if ($limit <= 0) {
            $query = $base_query;
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $season_id);
        } else {
            $query = $base_query . " LIMIT ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $season_id);
            $stmt->bindParam(2, $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get current season rankings (live standings)
     */
    public function getCurrentSeasonRankings() {
        $current_season = $this->getCurrentSeason();
        if (!$current_season) {
            return false;
        }

        // Get users ranked by current ELO, but only include those who played in current season
        $query = "SELECT DISTINCT u.id, u.pseudo, u.elo, 
                         COUNT(DISTINCT g.id) as parties_jouees,
                         SUM(CASE WHEN g.gagnant_id = u.id THEN 1 ELSE 0 END) as victoires
                  FROM users u
                  LEFT JOIN game_players gp ON u.id = gp.user_id
                  LEFT JOIN games g ON gp.game_id = g.id AND g.season_id = ? AND g.status = 'terminee'
                  GROUP BY u.id, u.pseudo, u.elo
                  HAVING parties_jouees > 0
                  ORDER BY u.elo DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $current_season['id']);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get rankings for a specific season
     * Returns live rankings for current season, saved stats for past seasons
     */
    public function getSeasonRankings($season_id) {
        $current_season = $this->getCurrentSeason();
        
        // If this is the current season, return live rankings
        if ($current_season && $season_id == $current_season['id']) {
            return $this->getCurrentSeasonRankings();
        }
        
        // For past seasons, return saved final stats
        $query = "SELECT ss.user_id as id, u.pseudo, ss.final_elo as elo, 
                         ss.parties_jouees, ss.victoires
                  FROM season_stats ss
                  JOIN users u ON ss.user_id = u.id
                  WHERE ss.season_id = ?
                  ORDER BY ss.final_rank ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $season_id);
        $stmt->execute();
        return $stmt;
    }

    /**
     * Get season summary statistics
     */
    public function getSeasonSummary($season_id) {
        $query = "SELECT 
                    COUNT(DISTINCT g.id) as total_games,
                    COUNT(DISTINCT gp.user_id) as total_players,
                    AVG(ss.final_elo) as avg_final_elo,
                    MAX(ss.final_elo) as max_elo,
                    MIN(ss.final_elo) as min_elo
                  FROM games g
                  LEFT JOIN game_players gp ON g.id = gp.game_id
                  LEFT JOIN season_stats ss ON ss.season_id = g.season_id
                  WHERE g.season_id = ? AND g.status = 'terminee'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $season_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Delete all in-progress games from a specific season
     * 
     * @param int $season_id The ID of the season
     * @return bool True if successful, false otherwise
     */
    private function deleteInProgressGames($season_id) {
        try {
            // Delete associated records in rounds table
            $delete_rounds = "DELETE r FROM rounds r 
                             JOIN games g ON r.game_id = g.id 
                             WHERE g.season_id = ? AND g.status = 'en_cours'";
            $stmt_rounds = $this->conn->prepare($delete_rounds);
            $stmt_rounds->bindParam(1, $season_id);
            $stmt_rounds->execute();
            
            // Delete associated records in game_players table
            $delete_players = "DELETE gp FROM game_players gp 
                              JOIN games g ON gp.game_id = g.id 
                              WHERE g.season_id = ? AND g.status = 'en_cours'";
            $stmt_players = $this->conn->prepare($delete_players);
            $stmt_players->bindParam(1, $season_id);
            $stmt_players->execute();
            
            // Delete the games
            $delete_games = "DELETE FROM games WHERE season_id = ? AND status = 'en_cours'";
            $stmt_games = $this->conn->prepare($delete_games);
            $stmt_games->bindParam(1, $season_id);
            $stmt_games->execute();
            
            return true;
        } catch (Exception $e) {
            error_log("Error deleting in-progress games: " . $e->getMessage());
            return false;
        }
    }
}
?>