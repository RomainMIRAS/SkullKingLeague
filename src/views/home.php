<?php
require_once '../config/database.php';
require_once '../src/models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$users = $user->getAll();
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="text-center mb-5">
            <h1 class="display-4"><i class="bi bi-suit-spade-fill text-danger"></i> Skull King League</h1>
            <p class="lead">Gérez vos parties et suivez votre progression !</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-plus-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Nouvelle Partie</h5>
                        <p class="card-text">Lancez une partie avec vos amis</p>
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#newGameModal">
                            Commencer
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-play-circle-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Parties en cours</h5>
                        <p class="card-text">Continuez une partie en cours</p>
                        <a href="index.php?page=games_in_progress" class="btn btn-primary">
                            Voir les parties
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Classement</h5>
                        <p class="card-text">Voir le classement ELO</p>
                        <a href="index.php?page=ranking" class="btn btn-warning">
                            Voir le classement
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-clock-history text-info" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Historique</h5>
                        <p class="card-text">Consultez les parties passées</p>
                        <a href="index.php?page=history" class="btn btn-info">
                            Voir l'historique
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Statistiques rapides
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM games WHERE status = 'terminee') as total_games,
            (SELECT COUNT(*) FROM games WHERE status = 'en_cours') as games_in_progress,
            (SELECT pseudo FROM users ORDER BY elo DESC LIMIT 1) as top_player
        ";
        $stats = $db->query($stats_query)->fetch(PDO::FETCH_ASSOC);
        ?>

        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-bar-chart-fill"></i> Statistiques de la ligue</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <h3 class="text-primary"><?php echo $stats['total_users']; ?></h3>
                                <p>Joueurs inscrits</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-success"><?php echo $stats['total_games']; ?></h3>
                                <p>Parties jouées</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-info"><?php echo $stats['games_in_progress']; ?></h3>
                                <p>Parties en cours</p>
                            </div>
                            <div class="col-md-3">
                                <h3 class="text-warning"><?php echo $stats['top_player']; ?></h3>
                                <p>Joueur #1</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nouvelle Partie -->
<div class="modal fade" id="newGameModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="index.php?page=game&action=create" method="POST" id="newGameForm">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle-fill"></i> Nouvelle Partie
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Liste des joueurs disponibles -->
                        <div class="col-md-6">
                            <h6><i class="bi bi-people-fill"></i> Joueurs disponibles</h6>
                            <p class="text-muted small">Cliquez pour sélectionner (1 à 6 joueurs)</p>
                            <div id="available-players" class="list-group">
                                <?php 
                                $users->execute(); // Reset le curseur
                                while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <div class="list-group-item list-group-item-action available-player" 
                                     data-player-id="<?php echo $row['id']; ?>"
                                     data-player-name="<?php echo htmlspecialchars($row['pseudo']); ?>"
                                     data-player-elo="<?php echo $row['elo']; ?>"
                                     style="cursor: pointer;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?php echo htmlspecialchars($row['pseudo']); ?></strong>
                                        </div>
                                        <div>
                                            <span class="badge bg-info"><?php echo $row['elo']; ?> ELO</span>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <!-- Ordre de jeu -->
                        <div class="col-md-6">
                            <h6><i class="bi bi-list-ol"></i> Ordre de jeu</h6>
                            <p class="text-muted small">
                                <span class="d-none d-md-inline">Glissez-déposez pour réorganiser ou </span>
                                utilisez les boutons ↑↓
                            </p>
                            <div id="selected-players" class="mb-3" style="min-height: 200px;">
                                <div id="empty-placeholder" class="text-center text-muted p-4 border border-dashed rounded">
                                    <i class="bi bi-arrow-left"></i>
                                    <br>Sélectionnez des joueurs
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="randomizeOrder">
                                    <i class="bi bi-shuffle"></i> Ordre aléatoire
                                </button>
                                <button type="button" class="btn btn-outline-danger btn-sm" id="clearAll">
                                    <i class="bi bi-x-circle"></i> Tout effacer
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="player-count-info" class="alert alert-info mt-3" style="display: none;">
                        <i class="bi bi-info-circle-fill"></i>
                        <span id="selected-count">0</span> joueur(s) sélectionné(s)
                    </div>
                    
                    <div id="player-count-warning" class="alert alert-warning mt-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Veuillez sélectionner entre 1 et 6 joueurs.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="start-game-btn" disabled>
                        <i class="bi bi-play-fill"></i> Commencer la partie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Inclure le JavaScript pour la nouvelle interface de création de partie -->
<script src="../assets/js/new-game.js"></script>
