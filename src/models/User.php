<?php
require_once '../config/database.php';

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $pseudo;
    public $elo;
    public $parties_jouees;
    public $victoires;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($season_id = null) {
        if ($season_id) {
            // Get season-specific rankings
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
            $stmt->bindParam(1, $season_id);
            $stmt->execute();
            return $stmt;
        } else {
            // Get all-time rankings
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY elo DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (pseudo) VALUES (?)";
        $stmt = $this->conn->prepare($query);
        
        $this->pseudo = htmlspecialchars(strip_tags($this->pseudo));
        
        $stmt->bindParam(1, $this->pseudo);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET elo = ?, parties_jouees = ?, victoires = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $this->elo);
        $stmt->bindParam(2, $this->parties_jouees);
        $stmt->bindParam(3, $this->victoires);
        $stmt->bindParam(4, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function updateElo($new_elo) {
        $query = "UPDATE " . $this->table_name . " SET elo = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $new_elo);
        $stmt->bindParam(2, $this->id);
        return $stmt->execute();
    }

    public function incrementStats($won = false, $season_id = null) {
        $query = "UPDATE " . $this->table_name . " 
                  SET parties_jouees = parties_jouees + 1" . 
                  ($won ? ", victoires = victoires + 1" : "") . " 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([$this->id]);
        // Also update season-specific stats if season_id is provided
        if ($season_id && $result) {
            $this->updateSeasonStats($season_id, $won);
        }
        
        return $result;
    }
    
    private function updateSeasonStats($season_id, $won) {
        // Check if season stats record exists
        $check_query = "SELECT id, final_elo FROM season_stats WHERE season_id = ? AND user_id = ?";
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->execute([$season_id, $this->id]);
        
        if ($check_stmt->fetch()) {
            // Update existing record
            $update_query = "UPDATE season_stats 
                           SET parties_jouees = parties_jouees + 1" .
                           ($won ? ", victoires = victoires + 1" : "") .
                           " WHERE season_id = ? AND user_id = ?";
            $update_stmt = $this->conn->prepare($update_query);
            $update_stmt->execute([$season_id, $this->id]);
        } else {
            // Create new record
            $insert_query = "INSERT INTO season_stats (season_id, user_id, parties_jouees, victoires, final_elo) 
                           VALUES (?, ?, 1, " . ($won ? "1" : "0") . ", ?)";
            $insert_stmt = $this->conn->prepare($insert_query);
            $insert_stmt->execute([$season_id, $this->id, $this->elo]);
        }
    }
}
?>
