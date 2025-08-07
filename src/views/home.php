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
            <div class="col-md-3">
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

            <div class="col-md-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-play-circle-fill text-primary" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Parties en cours</h5>
                        <p class="card-text">Rejoignez les parties en cours</p>
                        <a href="index.php?page=ongoing" class="btn btn-primary">
                            Voir les parties
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                        <h5 class="card-title mt-3">Classement</h5>
                        <p class="card-text">Voir le classement par points</p>
                        <a href="index.php?page=ranking" class="btn btn-warning">
                            Voir le classement
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
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

        <!-- Section Règles -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card bg-light border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5><i class="bi bi-book-fill"></i> Règles du Jeu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-trophy-fill text-warning" style="font-size: 2rem;"></i>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Règles Officielles</h6>
                                        <small class="text-muted">Pour les parties classées et le système ELO</small>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="index.php?page=rules" class="btn btn-warning btn-sm">
                                            <i class="bi bi-arrow-right"></i> Consulter
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-gear-fill text-secondary" style="font-size: 2rem;"></i>
                                    <div class="ms-3">
                                        <h6 class="mb-1">Règles Custom</h6>
                                        <small class="text-muted">Extensions et variantes pour parties amicales</small>
                                    </div>
                                    <div class="ms-auto">
                                        <a href="index.php?page=custom_rules" class="btn btn-secondary btn-sm">
                                            <i class="bi bi-arrow-right"></i> Découvrir
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info mb-0 mt-2">
                            <i class="bi bi-info-circle-fill"></i>
                            <strong>Nouveau joueur ?</strong> Consultez d'abord les règles officielles pour comprendre les bases du Skull King.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Statistiques rapides
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM games WHERE status = 'terminee') as total_games,
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
                            <div class="col-md-4">
                                <h3 class="text-primary"><?php echo $stats['total_users']; ?></h3>
                                <p>Joueurs inscrits</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-success"><?php echo $stats['total_games']; ?></h3>
                                <p>Parties jouées</p>
                            </div>
                            <div class="col-md-4">
                                <h3 class="text-warning"><?php echo $stats['top_player']; ?></h3>
                                <p>Joueur #1</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        // Afficher les parties en cours
        require_once '../src/models/Game.php';
        $game = new Game($db);
        $ongoing_games = $game->getOngoingGames(3); // Limite à 3 parties
        ?>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="bi bi-play-circle-fill text-primary"></i> Parties en cours</h5>
                        <a href="index.php?page=ongoing" class="btn btn-sm btn-outline-primary">
                            Voir toutes
                        </a>
                    </div>
                    <div class="card-body">
                        <?php 
                        $ongoing_found = false;
                        while ($ongoing_game = $ongoing_games->fetch(PDO::FETCH_ASSOC)): 
                            $ongoing_found = true;
                        ?>
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <div>
                                <strong>Partie #<?php echo $ongoing_game['id']; ?></strong>
                                <span class="text-muted ms-2">
                                    <?php echo htmlspecialchars($ongoing_game['players']); ?>
                                </span>
                                <span class="badge bg-info ms-2">
                                    Manche <?php echo $ongoing_game['current_round']; ?>/10
                                </span>
                            </div>
                            <div>
                                <a href="index.php?page=game&action=play&id=<?php echo $ongoing_game['id']; ?>" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-play-fill"></i> Rejoindre
                                </a>
                            </div>
                        </div>
                        <?php endwhile; ?>

                        <?php if (!$ongoing_found): ?>
                        <div class="text-center text-muted py-3">
                            <i class="bi bi-gamepad2"></i>
                            Aucune partie en cours actuellement
                        </div>
                        <?php endif; ?>
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
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6><i class="bi bi-people-fill"></i> Joueurs disponibles</h6>
                            </div>
                            
                            <!-- Barre de recherche -->
                            <div class="input-group mb-3">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control" id="player-search" 
                                       placeholder="Rechercher un joueur..." 
                                       autocomplete="off">
                                <button class="btn btn-outline-secondary" type="button" id="clear-search" title="Effacer la recherche">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            
                            <p class="text-muted small mb-2">Cliquez pour sélectionner (1 à 6 joueurs)</p>
                            
                            <div id="available-players" class="list-group" style="max-height: 300px; overflow-y: auto;">
                                <?php 
                                $users->execute(); // Reset le curseur
                                while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                                ?>
                                <div class="list-group-item list-group-item-action available-player" 
                                     data-player-id="<?php echo $row['id']; ?>"
                                     data-player-name="<?php echo htmlspecialchars($row['pseudo']); ?>"
                                     data-player-elo="<?php echo $row['elo']; ?>"
                                     data-player-created="<?php echo $row['created_at'] ?? ''; ?>"
                                     style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <div class="player-avatar me-2">
                                                <div class="avatar-circle" style="background-color: <?php echo sprintf('#%06X', crc32($row['pseudo'])); ?>">
                                                    <?php echo strtoupper(substr($row['pseudo'], 0, 1)); ?>
                                                </div>
                                            </div>
                                            <div>
                                                <strong class="player-name">
                                                    <?php echo htmlspecialchars($row['pseudo']); ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge <?php echo $row['elo'] >= 1500 ? 'bg-warning' : 'bg-info'; ?> me-2"><?php echo $row['elo']; ?></span>
                                            <i class="bi bi-plus-circle text-success"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            
                            <!-- Message quand aucun joueur trouvé -->
                            <div id="no-players-found" class="text-center text-muted py-4" style="display: none;">
                                <i class="bi bi-search" style="font-size: 2rem;"></i>
                                <p class="mt-2">Aucun joueur trouvé</p>
                            </div>
                        </div>
                        
                        <!-- Ordre de jeu -->
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6><i class="bi bi-list-ol"></i> Ordre de jeu</h6>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="randomizeOrder" title="Mélanger l'ordre">
                                        <i class="bi bi-shuffle"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="clearAll" title="Tout effacer">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <p class="text-muted small mb-3">
                                <i class="bi bi-info-circle"></i>
                                <span class="d-none d-md-inline">Glissez-déposez pour réorganiser ou </span>
                                utilisez les boutons ↑↓
                            </p>
                            
                            <div id="selected-players" class="mb-3" style="min-height: 250px; max-height: 350px; overflow-y: auto;">
                                <div id="empty-placeholder" class="text-center text-muted p-5 border border-dashed rounded bg-light">
                                    <i class="bi bi-arrow-left" style="font-size: 2rem; opacity: 0.5;"></i>
                                    <p class="mt-3 mb-0">Sélectionnez des joueurs pour commencer</p>
                                    <small class="text-muted">Cliquez sur les joueurs disponibles à gauche</small>
                                </div>
                            </div>
                            
                            <!-- Statistiques de la sélection -->
                            <div id="selection-stats" class="card bg-light" style="display: none;">
                                <div class="card-body py-2">
                                    <div class="row text-center">
                                        <div class="col-3">
                                            <small class="text-muted">Joueurs</small>
                                            <div class="fw-bold" id="stats-count">0</div>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">ELO Moyen</small>
                                            <div class="fw-bold text-primary" id="stats-avg-elo">0</div>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">Écart</small>
                                            <div class="fw-bold text-info" id="stats-elo-range">0</div>
                                        </div>
                                        <div class="col-3">
                                            <small class="text-muted">Expérience</small>
                                            <div class="fw-bold text-success" id="stats-total-games">0</div>
                                        </div>
                                    </div>
                                </div>
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

<!-- Styles CSS pour l'amélioration visuelle -->
<link rel="stylesheet" href="../assets/css/new-game.css">
<!-- Inclure le JavaScript pour la nouvelle interface de création de partie -->
<script src="../assets/js/new-game.js"></script>
