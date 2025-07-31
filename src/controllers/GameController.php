<?php
require_once '../config/database.php';
require_once '../src/models/Game.php';
require_once '../src/models/User.php';
require_once '../src/models/EloCalculator.php';

$database = new Database();
$db = $database->getConnection();
$game = new Game($db);
$user = new User($db);

$action = $_GET['action'] ?? 'index';

switch($action) {
    case 'create':
        if ($_POST && isset($_POST['players']) && is_array($_POST['players'])) {
            $player_ids = $_POST['players'];
            $is_ranked = isset($_POST['is_ranked']) ? (bool)$_POST['is_ranked'] : true;
            
            if (count($player_ids) >= 1 && count($player_ids) <= 6) {
                // Les joueurs sont déjà dans l'ordre souhaité grâce à l'interface
                $game_id = $game->create($player_ids, $is_ranked);
                if ($game_id) {
                    header("Location: index.php?page=game&action=play&id=" . $game_id);
                    exit;
                }
            }
        }
        header("Location: index.php?error=invalid_players");
        exit;

    case 'play':
        $game_id = $_GET['id'] ?? null;
        if (!$game_id) {
            header("Location: index.php");
            exit;
        }

        $game_data = $game->getById($game_id);
        if (!$game_data || $game_data['status'] != 'en_cours') {
            header("Location: index.php");
            exit;
        }

        $players = $game->getPlayersInOrder($game_id);
        $current_round = $game->getCurrentRound($game_id);
        $starting_player = $game->getStartingPlayer($game_id, $current_round);
        
        include '../src/views/game_play.php';
        break;

    case 'add_round':
        if ($_POST && isset($_POST['game_id']) && isset($_POST['scores'])) {
            $game_id = $_POST['game_id'];
            $scores = $_POST['scores'];
            $round_number = $_POST['round_number'];
            
            // Validation des scores
            $valid = true;
            $error_message = '';
            
            foreach($scores as $player_id => $score) {
                // Vérifier que c'est numérique
                if (!is_numeric($score)) {
                    $valid = false;
                    $error_message = 'Tous les scores doivent être des nombres.';
                    break;
                }
                
                // Vérifier que c'est un multiple de 10
                $score_int = intval($score);
                if ($score_int % 10 !== 0) {
                    $valid = false;
                    $error_message = 'Tous les scores doivent être des multiples de 10.';
                    break;
                }
            }
            
            if ($valid) {
                $game->addRound($game_id, $round_number, $scores);
                
                // Si c'est la 10ème manche, terminer la partie
                if ($round_number >= 10) {
                    // Récupérer toutes les manches pour calculer les scores réels
                    $rounds = $game->getRounds($game_id);
                    $player_totals = [];
                    
                    // Calculer le total pour chaque joueur
                    while ($round = $rounds->fetch(PDO::FETCH_ASSOC)) {
                        if (!isset($player_totals[$round['player_id']])) {
                            $player_totals[$round['player_id']] = 0;
                        }
                        $player_totals[$round['player_id']] += $round['score'];
                    }
                    
                    // Trouver le gagnant (meilleur score calculé)
                    $winner_id = null;
                    $best_score = PHP_INT_MIN;
                    
                    foreach ($player_totals as $player_id => $total_score) {
                        if ($total_score > $best_score) {
                            $best_score = $total_score;
                            $winner_id = $player_id;
                        }
                    }
                    
                    // Terminer la partie
                    $game->finishGame($game_id, $winner_id);
                    
                    // Récupérer les infos de la partie pour vérifier si elle est classée
                    $game_data = $game->getById($game_id);
                    $season_id = $game_data['season_id'];
                    $is_ranked = $game_data['is_ranked'];
                    
                    // Mettre à jour les statistiques et ELO seulement si la partie est classée
                    if ($is_ranked) {
                        // Mettre à jour les statistiques des joueurs
                        $players = $game->getPlayers($game_id);
                        while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                            $user->id = $player['user_id'];
                            $user->elo = $player['elo'];
                            $user->incrementStats($player['user_id'] == $winner_id, $season_id);
                        }
                        
                        // Calculer les nouveaux ELO pour toutes les parties
                        EloCalculator::updateElosAfterGame($db, $game_id, $winner_id);
                    }
                    
                    header("Location: index.php?page=game&action=finish&id=" . $game_id);
                    exit;
                }
            } else {
                // Redirection avec message d'erreur
                $game_id = $_POST['game_id'] ?? '';
                header("Location: index.php?page=game&action=play&id=" . $game_id . "&error=" . urlencode($error_message));
                exit;
            }
        }
        
        header("Location: index.php?page=game&action=play&id=" . ($_POST['game_id'] ?? ''));
        exit;

    case 'finish':
        $game_id = $_GET['id'] ?? null;
        if (!$game_id) {
            header("Location: index.php");
            exit;
        }

        $game_data = $game->getById($game_id);
        $players = $game->getPlayersForRanking($game_id);
        
        // Récupérer les changements d'ELO pour cette partie
        $elo_changes = [];
        
        try {
            $query_elo = "SELECT * FROM elo_history WHERE game_id = ?";
            $stmt_elo = $db->prepare($query_elo);
            $stmt_elo->bindParam(1, $game_id);
            $stmt_elo->execute();
            
            while ($row = $stmt_elo->fetch(PDO::FETCH_ASSOC)) {
                $elo_changes[$row['user_id']] = $row;
            }
        } catch (PDOException $e) {
            // La table n'existe peut-être pas encore, on continue sans données ELO
            // On les récupérera quand la prochaine partie se terminera
        }
        
        include '../src/views/game_finish.php';
        break;

    case 'details':
        $game_id = $_GET['id'] ?? null;
        if (!$game_id) {
            echo '<div class="alert alert-danger">ID de partie manquant</div>';
            exit;
        }

        $game_data = $game->getById($game_id);
        $players = $game->getPlayersForRanking($game_id);
        $rounds = $game->getRounds($game_id);
        
        // Récupérer l'historique ELO pour cette partie
        $elo_history_query = "SELECT eh.*, u.pseudo 
                              FROM elo_history eh 
                              JOIN users u ON eh.user_id = u.id 
                              WHERE eh.game_id = ? 
                              ORDER BY eh.rank ASC";
        $elo_stmt = $db->prepare($elo_history_query);
        $elo_stmt->bindParam(1, $game_id);
        $elo_stmt->execute();
        $elo_history = $elo_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include '../src/views/game_details.php';
        break;

    default:
        header("Location: index.php");
        exit;
}
?>
