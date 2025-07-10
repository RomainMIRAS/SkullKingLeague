<?php
class EloCalculator {
    const K_FACTOR = 32;
    
    public static function calculateNewElo($player_elo, $opponent_elos, $result) {
        // result: 1 pour victoire, 0 pour défaite
        // opponent_elos: array des ELO des adversaires
        
        $average_opponent_elo = array_sum($opponent_elos) / count($opponent_elos);
        
        // Calcul de l'espérance
        $expected = 1 / (1 + pow(10, ($average_opponent_elo - $player_elo) / 400));
        
        // Nouveau ELO
        $new_elo = $player_elo + self::K_FACTOR * ($result - $expected);
        
        return round($new_elo);
    }
    
    public static function updateElosAfterGame($db, $game_id, $winner_id) {
        // Récupérer tous les joueurs et leurs ELO actuels
        $query = "SELECT gp.user_id, u.elo, gp.score_total
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $elos = [];
        foreach($players as $player) {
            $elos[$player['user_id']] = $player['elo'];
        }
        
        // Calculer les nouveaux ELO
        foreach($players as $player) {
            $player_id = $player['user_id'];
            $player_elo = $player['elo'];
            $is_winner = ($player_id == $winner_id) ? 1 : 0;
            
            // ELO des adversaires
            $opponent_elos = array_filter($elos, function($key) use ($player_id) {
                return $key != $player_id;
            }, ARRAY_FILTER_USE_KEY);
            
            $new_elo = self::calculateNewElo($player_elo, array_values($opponent_elos), $is_winner);
            
            // Mettre à jour l'ELO en base
            $update_query = "UPDATE users SET elo = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(1, $new_elo);
            $update_stmt->bindParam(2, $player_id);
            $update_stmt->execute();
        }
    }
}
?>
