<?php
require_once '../config/database.php';

class Game {
    private $conn;
    private $table_name = "games";

    public $id;
    public $date_partie;
    public $gagnant_id;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($player_ids) {
        // Créer la partie
        $query = "INSERT INTO " . $this->table_name . " (status) VALUES ('en_cours')";
        $stmt = $this->conn->prepare($query);
        
        if($stmt->execute()) {
            $game_id = $this->conn->lastInsertId();
            
            // Ajouter les joueurs à la partie avec l'ordre fourni
            // L'ordre est maintenant défini par l'interface utilisateur
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

    public function getPlayers($game_id) {
        $query = "SELECT gp.*, u.pseudo 
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.player_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        return $stmt;
    }

    public function getRounds($game_id) {
        $query = "SELECT r.*, u.pseudo 
                  FROM rounds r
                  JOIN users u ON r.player_id = u.id
                  WHERE r.game_id = ?
                  ORDER BY r.numero_manche, r.player_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        return $stmt;
    }

    public function addRound($game_id, $numero_manche, $scores) {
        // Déterminer qui commence cette manche
        $starting_player = $this->getStartingPlayer($game_id, $numero_manche);
        $starting_player_id = $starting_player ? $starting_player['user_id'] : null;
        
        $query = "INSERT INTO rounds (game_id, numero_manche, player_id, score, starting_player_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        foreach($scores as $player_id => $score) {
            $stmt->bindParam(1, $game_id);
            $stmt->bindParam(2, $numero_manche);
            $stmt->bindParam(3, $player_id);
            $stmt->bindParam(4, $score);
            $stmt->bindParam(5, $starting_player_id);
            $stmt->execute();
            
            // Mettre à jour le score total
            $this->updatePlayerScore($game_id, $player_id, $score);
        }
        return true;
    }

    private function updatePlayerScore($game_id, $player_id, $score) {
        // Au lieu d'ajouter directement le score, recalculons le total à partir de toutes les manches
        // 1. Récupérer tous les scores de ce joueur pour cette partie
        $query_get = "SELECT SUM(score) as total_score FROM rounds 
                      WHERE game_id = ? AND player_id = ?";
        $stmt_get = $this->conn->prepare($query_get);
        $stmt_get->bindParam(1, $game_id);
        $stmt_get->bindParam(2, $player_id);
        $stmt_get->execute();
        $result = $stmt_get->fetch(PDO::FETCH_ASSOC);
        $total_score = $result['total_score'] ?? 0;
        
        // 2. Mettre à jour le score total dans game_players
        $query = "UPDATE game_players 
                  SET score_total = ? 
                  WHERE game_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $total_score);
        $stmt->bindParam(2, $game_id);
        $stmt->bindParam(3, $player_id);
        return $stmt->execute();
    }

    public function finishGame($game_id, $winner_id) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'terminee', gagnant_id = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $winner_id);
        $stmt->bindParam(2, $game_id);
        return $stmt->execute();
    }

    public function getAll($limit = 20) {
        $query = "SELECT g.*, u.pseudo as gagnant_pseudo,
                         COUNT(gp.user_id) as nombre_joueurs
                  FROM " . $this->table_name . " g
                  LEFT JOIN users u ON g.gagnant_id = u.id
                  LEFT JOIN game_players gp ON g.id = gp.game_id
                  WHERE g.status = 'terminee'
                  GROUP BY g.id
                  ORDER BY g.date_partie DESC 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getGamesInProgress($limit = 20) {
        $query = "SELECT g.*, 
                         COUNT(gp.user_id) as nombre_joueurs,
                         MAX(r.numero_manche) as derniere_manche
                  FROM " . $this->table_name . " g
                  LEFT JOIN game_players gp ON g.id = gp.game_id
                  LEFT JOIN rounds r ON g.id = r.game_id
                  WHERE g.status = 'en_cours'
                  GROUP BY g.id
                  ORDER BY g.date_partie DESC 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function getPlayersForGame($game_id) {
        $query = "SELECT gp.*, u.pseudo 
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.player_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCurrentRound($game_id) {
        $query = "SELECT MAX(numero_manche) as derniere_manche 
                  FROM rounds 
                  WHERE game_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['derniere_manche'] ?? 0) + 1;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    public function getStartingPlayer($game_id, $round_number) {
        // Le joueur qui commence est déterminé par l'ordre et le numéro de manche
        $query = "SELECT gp.user_id, gp.player_order, u.pseudo
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.player_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($players)) {
            return null;
        }
        
        // Calculer l'index du joueur qui commence (rotation basée sur la manche)
        $starting_index = ($round_number - 1) % count($players);
        return $players[$starting_index];
    }
    
    public function getPlayersInOrder($game_id) {
        // Récupère les joueurs dans l'ordre de jeu (pour l'affichage)
        $query = "SELECT gp.*, u.pseudo 
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.player_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        return $stmt;
    }
    
    public function getPlayersForRanking($game_id) {
        // Récupère les joueurs triés par score (pour le classement)
        $query = "SELECT gp.*, u.pseudo 
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.score_total DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
