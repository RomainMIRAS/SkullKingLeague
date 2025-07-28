<?php
require_once '../config/database.php';
require_once '../src/models/Game.php';

$database = new Database();
$db = $database->getConnection();
$game = new Game($db);

$games_in_progress = $game->getGamesInProgress();
?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-play-circle text-primary"></i> Parties en cours</h1>
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour à l'accueil
            </a>
        </div>

        <?php 
        $games_data = $games_in_progress->fetchAll(PDO::FETCH_ASSOC);
        if (empty($games_data)): 
        ?>
        <div class="text-center py-5">
            <div class="card">
                <div class="card-body">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Aucune partie en cours</h4>
                    <p class="text-muted">Toutes les parties ont été terminées ou aucune partie n'a encore été créée.</p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Lancer une nouvelle partie
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle-fill"></i>
            <strong><?php echo count($games_data); ?></strong> partie(s) en cours trouvée(s). 
            Cliquez sur une partie pour la continuer ou voir ses détails.
        </div>

        <div class="row">
            <?php foreach ($games_data as $game_data): 
                $players = $game->getPlayersForGame($game_data['id']);
                $current_round = max(1, ($game_data['derniere_manche'] ?? 0) + 1);
                $progress_percentage = max(0, (($game_data['derniere_manche'] ?? 0) / 10) * 100);
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-primary">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="bi bi-controller"></i> 
                                Partie #<?php echo $game_data['id']; ?>
                            </h6>
                            <span class="badge bg-light text-primary">
                                <?php echo $game_data['nombre_joueurs']; ?> joueurs
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6 class="text-muted">Progression:</h6>
                            <div class="progress mb-2">
                                <div class="progress-bar" 
                                     role="progressbar" 
                                     style="width: <?php echo $progress_percentage; ?>%"
                                     aria-valuenow="<?php echo $game_data['derniere_manche'] ?? 0; ?>" 
                                     aria-valuemin="0" 
                                     aria-valuemax="10">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    Manche <?php echo $current_round; ?>/10
                                </small>
                                <small class="text-muted">
                                    <?php echo round($progress_percentage); ?>%
                                </small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <h6 class="text-muted">Joueurs:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                <?php foreach ($players as $player): ?>
                                <span class="badge bg-secondary">
                                    <?php echo htmlspecialchars($player['pseudo']); ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> 
                                Commencée le <?php echo date('d/m/Y à H:i', strtotime($game_data['date_partie'])); ?>
                            </small>
                        </div>

                        <?php if (!empty($players) && ($game_data['derniere_manche'] ?? 0) > 0): ?>
                        <div class="mb-3">
                            <h6 class="text-muted">Scores actuels:</h6>
                            <div class="small">
                                <?php 
                                // Trier les joueurs par score pour afficher le classement temporaire
                                usort($players, function($a, $b) {
                                    return $b['score_total'] - $a['score_total'];
                                });
                                foreach ($players as $index => $player): 
                                ?>
                                <div class="d-flex justify-content-between">
                                    <span>
                                        <?php echo ($index + 1); ?>. <?php echo htmlspecialchars($player['pseudo']); ?>
                                    </span>
                                    <span class="badge <?php echo $player['score_total'] >= 0 ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $player['score_total']; ?> pts
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php elseif (!empty($players)): ?>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> 
                                Partie pas encore commencée - Aucun score pour le moment
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-grid gap-2">
                            <a href="index.php?page=game&action=play&id=<?php echo $game_data['id']; ?>" 
                               class="btn btn-primary">
                                <i class="bi bi-play-fill"></i> Continuer la partie
                            </a>
                            <a href="index.php?page=game&action=details&id=<?php echo $game_data['id']; ?>" 
                               class="btn btn-outline-info btn-sm">
                                <i class="bi bi-eye"></i> Voir les détails
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>