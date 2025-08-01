<?php
require_once '../config/database.php';
require_once '../src/models/Game.php';
require_once '../src/models/Season.php';

$database = new Database();
$db = $database->getConnection();
$game = new Game($db);
$season = new Season($db);

// Get current season and all seasons
$current_season = $season->getCurrentSeason();
$all_seasons = $season->getAllSeasons();

// Get current season games (ranked only) - get all games without limit (passing 0 as limit)
$current_season_games = $current_season ? $season->getSeasonGames($current_season['id'], 0) : null;
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="bi bi-clock-history text-info"></i> Historique des Saisons</h2>
        <p class="lead">Retrouvez les parties et classements de chaque saison</p>

        <!-- Current Season -->
        <?php if ($current_season): ?>
        <div class="card mb-4 border-success">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="bi bi-star-fill"></i> 
                    <?php echo htmlspecialchars($current_season['name']); ?> (Saison Actuelle)
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="mb-1">
                            <i class="bi bi-calendar3"></i> 
                            Commencée le <?php echo date('d/m/Y à H:i', strtotime($current_season['start_date'])); ?>
                        </p>
                        <p class="text-muted mb-3">Parties classées uniquement</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="index.php?page=ranking&season=<?php echo $current_season['id']; ?>" class="btn btn-outline-light">
                            <i class="bi bi-trophy"></i> Voir le classement
                        </a>
                    </div>
                </div>
                
                <?php if ($current_season_games && $current_season_games->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Gagnant</th>
                                <th>Joueurs</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($game_row = $current_season_games->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-calendar3"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($game_row['date_partie'])); ?>
                                </td>
                                <td>
                                    <i class="bi bi-trophy-fill text-warning"></i>
                                    <strong><?php echo htmlspecialchars($game_row['gagnant_pseudo']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $game_row['nombre_joueurs']; ?> joueurs</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="showGameDetails(<?php echo $game_row['id']; ?>)" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#gameDetailsModal">
                                        <i class="bi bi-eye"></i> Détails
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Aucune partie classée jouée dans cette saison pour le moment.
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Past Seasons -->
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-archive"></i> Saisons Précédentes</h5>
            </div>
            <div class="card-body">
                <?php if ($all_seasons): ?>
                    <div class="accordion" id="pastSeasonsAccordion">
                        <?php 
                        $accordion_count = 0;
                        while ($season_row = $all_seasons->fetch(PDO::FETCH_ASSOC)): 
                            if ($season_row['is_current']) continue; // Skip current season
                            $accordion_count++;
                            
                            // Get season stats and games
                            $season_stats = $season->getSeasonStats($season_row['id']);
                            $season_games = $season->getSeasonGames($season_row['id'], 10);
                            $season_summary = $season->getSeasonSummary($season_row['id']);
                        ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $accordion_count; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapse<?php echo $accordion_count; ?>" 
                                        aria-expanded="false" aria-controls="collapse<?php echo $accordion_count; ?>">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?php echo htmlspecialchars($season_row['name']); ?></strong>
                                                <small class="text-muted ms-2">
                                                    <?php echo date('d/m/Y', strtotime($season_row['start_date'])); ?>
                                                    - 
                                                    <?php echo $season_row['end_date'] ? date('d/m/Y', strtotime($season_row['end_date'])) : 'En cours'; ?>
                                                </small>
                                            </div>
                                            <div class="text-muted">
                                                <?php if ($season_summary): ?>
                                                    <small><?php echo $season_summary['total_games']; ?> parties • <?php echo $season_summary['total_players']; ?> joueurs</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $accordion_count; ?>" class="accordion-collapse collapse" 
                                 aria-labelledby="heading<?php echo $accordion_count; ?>" data-bs-parent="#pastSeasonsAccordion">
                                <div class="accordion-body">
                                    <div class="row">
                                        <!-- Final Rankings -->
                                        <div class="col-md-6">
                                            <h6><i class="bi bi-trophy"></i> Classement Final</h6>
                                            <?php if ($season_stats && $season_stats->rowCount() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Rang</th>
                                                            <th>Joueur</th>
                                                            <th>ELO</th>
                                                            <th>Parties</th>
                                                            <th>Victoires</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        $count = 0;
                                                        while ($stat = $season_stats->fetch(PDO::FETCH_ASSOC)): 
                                                            if ($count >= 5) break; // Show only top 5
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php if ($stat['final_rank'] == 1): ?>
                                                                    <i class="bi bi-trophy-fill text-warning"></i>
                                                                <?php elseif ($stat['final_rank'] == 2): ?>
                                                                    <i class="bi bi-award-fill text-secondary"></i>
                                                                <?php elseif ($stat['final_rank'] == 3): ?>
                                                                    <i class="bi bi-award-fill text-warning"></i>
                                                                <?php else: ?>
                                                                    <?php echo $stat['final_rank']; ?>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><strong><?php echo htmlspecialchars($stat['pseudo']); ?></strong></td>
                                                            <td><span class="badge bg-info"><?php echo $stat['final_elo']; ?></span></td>
                                                            <td><?php echo $stat['parties_jouees']; ?></td>
                                                            <td><?php echo $stat['victoires']; ?></td>
                                                        </tr>
                                                        <?php 
                                                        $count++;
                                                        endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-info">
                                                <small>Aucune statistique disponible</small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Recent Games -->
                                        <div class="col-md-6">
                                            <h6><i class="bi bi-clock-history"></i> Dernières Parties</h6>
                                            <?php if ($season_games && $season_games->rowCount() > 0): ?>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Gagnant</th>
                                                            <th>Joueurs</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($game_row = $season_games->fetch(PDO::FETCH_ASSOC)): ?>
                                                        <tr>
                                                            <td class="small">
                                                                <?php echo date('d/m H:i', strtotime($game_row['date_partie'])); ?>
                                                            </td>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($game_row['gagnant_pseudo']); ?></strong>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary"><?php echo $game_row['nombre_joueurs']; ?></span>
                                                            </td>
                                                        </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-info">
                                                <small>Aucune partie trouvée</small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- View Full Season Button -->
                                    <div class="text-center mt-3">
                                        <a href="index.php?page=ranking&season=<?php echo $season_row['id']; ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-eye"></i> Voir la saison complète
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                        
                        <?php if ($accordion_count == 0): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            Aucune saison précédente trouvée.
                        </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Aucune saison trouvée.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Détails de la partie -->
<div class="modal fade" id="gameDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la partie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="gameDetailsContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
function showGameDetails(gameId) {
    const content = document.getElementById('gameDetailsContent');
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    `;
    
    fetch(`index.php?page=game&action=details&id=${gameId}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
        })
        .catch(error => {
            content.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des détails.</div>';
        });
}
</script>
