<?php
require_once '../config/database.php';
require_once '../src/models/Game.php';

$database = new Database();
$db = $database->getConnection();
$game = new Game($db);
$games = $game->getAll(50);
?>

<div class="row">
    <div class="col-md-12">
        <h2><i class="bi bi-clock-history text-info"></i> Historique des Parties</h2>
        <p class="lead">Retrouvez toutes les parties terminées</p>

        <?php if ($games->rowCount() > 0): ?>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Gagnant</th>
                                <th>Joueurs</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $games->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td>
                                    <i class="bi bi-calendar3"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($row['date_partie'])); ?>
                                </td>
                                <td>
                                    <i class="bi bi-trophy-fill text-warning"></i>
                                    <strong><?php echo htmlspecialchars($row['gagnant_pseudo']); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?php echo $row['nombre_joueurs']; ?> joueurs</span>
                                </td>
                                <td>
                                    <?php if (isset($row['is_ranked']) && $row['is_ranked']): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-trophy"></i> Classée
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-heart"></i> Amicale
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="showGameDetails(<?php echo $row['id']; ?>)" 
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
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle-fill"></i>
            Aucune partie terminée pour le moment. 
            <a href="index.php" class="alert-link">Lancez votre première partie !</a>
        </div>
        <?php endif; ?>
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
