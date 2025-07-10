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
            
            if (count($player_ids) >= 1 && count($player_ids) <= 6) {
                $game_id = $game->create($player_ids);
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

        $players = $game->getPlayers($game_id);
        $current_round = $game->getCurrentRound($game_id);
        
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
                    // Trouver le gagnant (meilleur score)
                    $players = $game->getPlayers($game_id);
                    $winner_id = null;
                    $best_score = PHP_INT_MIN;
                    
                    while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                        if ($player['score_total'] > $best_score) {
                            $best_score = $player['score_total'];
                            $winner_id = $player['user_id'];
                        }
                    }
                    
                    // Terminer la partie
                    $game->finishGame($game_id, $winner_id);
                    
                    // Mettre à jour les statistiques des joueurs
                    $players = $game->getPlayers($game_id);
                    while ($player = $players->fetch(PDO::FETCH_ASSOC)) {
                        $user->id = $player['user_id'];
                        $user->incrementStats($player['user_id'] == $winner_id);
                    }
                    
                    // Calculer les nouveaux ELO
                    EloCalculator::updateElosAfterGame($db, $game_id, $winner_id);
                    
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
        $players = $game->getPlayers($game_id);
        
        include '../src/views/game_finish.php';
        break;

    case 'details':
        $game_id = $_GET['id'] ?? null;
        if (!$game_id) {
            echo '<div class="alert alert-danger">ID de partie manquant</div>';
            exit;
        }

        $game_data = $game->getById($game_id);
        $players = $game->getPlayers($game_id);
        $rounds = $game->getRounds($game_id);
        
        include '../src/views/game_details.php';
        break;

    default:
        header("Location: index.php");
        exit;
}
?>
