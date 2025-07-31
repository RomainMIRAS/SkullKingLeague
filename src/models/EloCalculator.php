<?php
class EloCalculator {
    const K_FACTOR = 200;
    
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
        // Récupérer tous les joueurs et leurs ELO actuels, triés par score décroissant
        $query = "SELECT gp.user_id, u.elo, gp.score_total
                  FROM game_players gp
                  JOIN users u ON gp.user_id = u.id
                  WHERE gp.game_id = ?
                  ORDER BY gp.score_total DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $game_id);
        $stmt->execute();
        $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $total_players = count($players);
        $elo_changes = [];
        
        // Gestion des égalités : créer un tableau de rangs réels
        $real_ranks = [];
        $last_score = null;
        $last_rank = 0;
        $tied_players = 0;
        
        // Premier passage : identifier les égalités et attribuer les rangs
        foreach ($players as $index => $player) {
            $score = $player['score_total'];
            
            if ($last_score !== null && $score == $last_score) {
                // Égalité avec le joueur précédent
                $tied_players++;
                $real_ranks[$index] = $last_rank;
            } else {
                // Nouveau score
                $last_rank = $index + 1 - $tied_players;
                $real_ranks[$index] = $last_rank;
                $tied_players = 0;
            }
            
            $last_score = $score;
        }
        
        // Calculer les nouveaux ELO basés sur le classement final
        foreach($players as $index => $player) {
            $player_id = $player['user_id'];
            $player_elo = $player['elo'];
            
            // Utiliser le rang réel qui tient compte des égalités
            $player_rank = $real_ranks[$index];
            
            // Calculer le résultat normalisé entre 0 et 1 basé sur la position
            // Position 1 = 1.0, dernière position = 0.0, milieu = 0.5
            // Pour les égalités, utiliser la moyenne des positions qu'ils auraient occupées
            $tied_positions = [];
            foreach ($real_ranks as $other_index => $other_rank) {
                if ($other_rank == $player_rank) {
                    $tied_positions[] = $other_index + 1; // +1 car les indices commencent à 0
                }
            }
            
            // Calculer la position moyenne pour les égalités
            $average_position = array_sum($tied_positions) / count($tied_positions);
            
            // Normaliser entre 0 et 1 (position 1 = 1.0, dernière = 0.0)
            $normalized_result = ($total_players - $average_position) / ($total_players - 1);
            
            // Gérer le cas où il n'y a qu'un joueur
            if ($total_players == 1) {
                $normalized_result = 1.0;
            }
            
            $new_elo = self::calculateNewElo($player_elo, array_column($players, 'elo'), $normalized_result);
            
            // Stocker l'ancien et le nouveau ELO pour affichage ultérieur
            $elo_changes[$player_id] = [
                'old_elo' => $player_elo,
                'new_elo' => $new_elo,
                'change' => $new_elo - $player_elo,
                'rank' => $player_rank // Stocker le rang pour le débogage
            ];
            
            // Mettre à jour l'ELO en base
            $update_query = "UPDATE users SET elo = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(1, $new_elo);
            $update_stmt->bindParam(2, $player_id);
            $update_stmt->execute();
        }
        
        // Vérifier si la table elo_history existe, sinon la créer
        try {
            $check_table = $db->query("SELECT 1 FROM elo_history LIMIT 1");
        } catch (PDOException $e) {
            // Table n'existe pas, on la crée
            $sql_file = file_get_contents(__DIR__ . '/../../config/create_elo_history_table.sql');
            $db->exec($sql_file);
        }
        
        // Stocker les changements d'ELO dans la base de données pour référence ultérieure
        $query_elo_history = "INSERT INTO elo_history (game_id, user_id, old_elo, new_elo, elo_change, `rank`) 
                              VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_elo_history = $db->prepare($query_elo_history);
        
        foreach($elo_changes as $player_id => $change) {
            $stmt_elo_history->bindParam(1, $game_id);
            $stmt_elo_history->bindParam(2, $player_id);
            $stmt_elo_history->bindParam(3, $change['old_elo']);
            $stmt_elo_history->bindParam(4, $change['new_elo']);
            $stmt_elo_history->bindParam(5, $change['change']);
            $stmt_elo_history->bindParam(6, $change['rank']);
            $stmt_elo_history->execute();
        }
    }
}
?>
