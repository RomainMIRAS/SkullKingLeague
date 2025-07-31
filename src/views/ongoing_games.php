<?php
require_once '../config/database.php';
require_once '../src/models/Game.php';

$database = new Database();
$db = $database->getConnection();
$game = new Game($db);
$ongoing_games = $game->getOngoingGames();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="text-center mb-4">
            <h1><i class="bi bi-play-circle-fill text-primary"></i> Parties en cours</h1>
            <p class="lead">Rejoignez ou consultez les parties en cours</p>
        </div>

        <?php 
        $games_found = false;
        while ($ongoing_game = $ongoing_games->fetch(PDO::FETCH_ASSOC)): 
            $games_found = true;
        ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-gamepad2 text-primary"></i>
                                Partie #<?php echo $ongoing_game['id']; ?>
                                <?php if ($ongoing_game['season_name']): ?>
                                    <span class="badge bg-info ms-2"><?php echo htmlspecialchars($ongoing_game['season_name']); ?></span>
                                <?php endif; ?>
                            </h5>
                        </div>
                        
                        <div class="row text-muted small mb-2">
                            <div class="col-sm-6">
                                <i class="bi bi-calendar-event"></i>
                                Créée le <?php echo date('d/m/Y à H:i', strtotime($ongoing_game['date_partie'])); ?>
                            </div>
                            <div class="col-sm-6">
                                <i class="bi bi-people-fill"></i>
                                <?php echo $ongoing_game['player_count']; ?> joueur(s)
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Joueurs:</strong>
                            <?php echo htmlspecialchars($ongoing_game['players']); ?>
                        </div>
                        
                        <div class="progress mb-2" style="height: 8px;">
                            <?php 
                            $progress = ($ongoing_game['current_round'] / 10) * 100;
                            $progress_class = $progress < 30 ? 'bg-success' : ($progress < 70 ? 'bg-warning' : 'bg-danger');
                            ?>
                            <div class="progress-bar <?php echo $progress_class; ?>" 
                                 role="progressbar" 
                                 style="width: <?php echo $progress; ?>%"></div>
                        </div>
                        
                        <small class="text-muted">
                            <i class="bi bi-arrow-repeat"></i>
                            Manche <?php echo $ongoing_game['current_round']; ?>/10
                        </small>
                    </div>
                    
                    <div class="col-md-4 text-end">
                        <div class="btn-group-vertical gap-2">
                            <a href="index.php?page=game&action=play&id=<?php echo $ongoing_game['id']; ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-play-fill"></i> Jouer / Modifier
                            </a>
                            <a href="index.php?page=game&action=details&id=<?php echo $ongoing_game['id']; ?>" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-eye-fill"></i> Voir détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>

        <?php if (!$games_found): ?>
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-gamepad2" style="font-size: 4rem; color: #dee2e6;"></i>
            </div>
            <h4 class="text-muted">Aucune partie en cours</h4>
            <p class="text-muted">Toutes les parties ont été terminées ou aucune partie n'a encore été créée.</p>
            <a href="index.php" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill"></i> Créer une nouvelle partie
            </a>
        </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-house-fill"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>