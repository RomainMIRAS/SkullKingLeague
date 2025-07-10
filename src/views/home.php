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
            <div class="col-md-4">
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

            <div class="col-md-4">
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

            <div class="col-md-4">
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
    </div>
</div>

<!-- Modal Nouvelle Partie -->
<div class="modal fade" id="newGameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?page=game&action=create" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Nouvelle Partie</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Sélectionnez les joueurs (1 à 6 joueurs) :</p>
                    <div class="row">
                        <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input player-checkbox" type="checkbox" 
                                       name="players[]" value="<?php echo $row['id']; ?>" 
                                       id="player<?php echo $row['id']; ?>">
                                <label class="form-check-label" for="player<?php echo $row['id']; ?>">
                                    <?php echo htmlspecialchars($row['pseudo']); ?>
                                    <small class="text-muted">(ELO: <?php echo $row['elo']; ?>)</small>
                                </label>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <div id="player-count-warning" class="alert alert-warning mt-3" style="display: none;">
                        Veuillez sélectionner entre 1 et 6 joueurs.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success" id="start-game-btn" disabled>Commencer la partie</button>
                </div>
            </form>
        </div>
    </div>
</div>
