<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-controller"></i> Gestion des parties</h2>
    <a href="index.php?page=admin&action=dashboard" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill"></i>
    <?php 
    switch($_GET['success']) {
        case 'game_deleted':
            echo 'Partie supprimée avec succès !';
            break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <?php 
    switch($_GET['error']) {
        case 'delete_failed':
            echo 'Erreur lors de la suppression de la partie.';
            break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($all_games->rowCount() > 0): ?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Gagnant</th>
                        <th>Joueurs</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $all_games->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td>
                            <?php echo date('d/m/Y H:i', strtotime($row['date_partie'])); ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'terminee'): ?>
                                <span class="badge bg-success">Terminée</span>
                            <?php else: ?>
                                <span class="badge bg-warning">En cours</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['gagnant_pseudo']): ?>
                                <i class="bi bi-trophy-fill text-warning"></i>
                                <?php echo htmlspecialchars($row['gagnant_pseudo']); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?php echo $row['nombre_joueurs']; ?> joueurs</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-info" 
                                        onclick="showGameDetails(<?php echo $row['id']; ?>)" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#gameDetailsModal">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-outline-danger" 
                                        onclick="confirmDeleteGame(<?php echo $row['id']; ?>, '<?php echo date('d/m/Y H:i', strtotime($row['date_partie'])); ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
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
    Aucune partie enregistrée pour le moment.
</div>
<?php endif; ?>

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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteGameModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la partie du <strong id="gameToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Attention :</strong> Cette action supprimera toutes les données de la partie (scores, manches) et est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" class="btn btn-danger" id="confirmDeleteGameBtn">
                    <i class="bi bi-trash"></i> Supprimer
                </a>
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

function confirmDeleteGame(gameId, gameDate) {
    document.getElementById('gameToDelete').textContent = gameDate;
    document.getElementById('confirmDeleteGameBtn').href = 'index.php?page=admin&action=delete_game&id=' + gameId;
    new bootstrap.Modal(document.getElementById('deleteGameModal')).show();
}
</script>
