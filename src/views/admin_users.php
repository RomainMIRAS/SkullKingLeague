<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people-fill"></i> Gestion des utilisateurs</h2>
    <div>
        <a href="index.php?page=admin&action=add_user" class="btn btn-success">
            <i class="bi bi-person-plus"></i> Ajouter un utilisateur
        </a>
        <a href="index.php?page=admin&action=dashboard" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle-fill"></i>
    <?php 
    switch($_GET['success']) {
        case 'user_added':
            echo 'Utilisateur ajouté avec succès !';
            break;
        case 'user_deleted':
            echo 'Utilisateur supprimé avec succès !';
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
            echo 'Erreur lors de la suppression de l\'utilisateur.';
            break;
    }
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Pseudo</th>
                        <th>ELO</th>
                        <th>Parties jouées</th>
                        <th>Victoires</th>
                        <th>% Victoires</th>
                        <th>Date d'inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $users->fetch(PDO::FETCH_ASSOC)): 
                        $win_rate = $row['parties_jouees'] > 0 ? round(($row['victoires'] / $row['parties_jouees']) * 100) : 0;
                    ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($row['pseudo']); ?></strong></td>
                        <td>
                            <span class="badge bg-info"><?php echo $row['elo']; ?></span>
                        </td>
                        <td><?php echo $row['parties_jouees']; ?></td>
                        <td><?php echo $row['victoires']; ?></td>
                        <td>
                            <div class="progress" style="height: 20px; width: 80px;">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $win_rate; ?>%">
                                    <?php echo $win_rate; ?>%
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php echo isset($row['created_at']) ? date('d/m/Y', strtotime($row['created_at'])) : 'N/A'; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['pseudo']); ?>')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Attention :</strong> Cette action supprimera également toutes les parties associées à cet utilisateur et est irréversible.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="#" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash"></i> Supprimer
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(userId, userPseudo) {
    document.getElementById('userToDelete').textContent = userPseudo;
    document.getElementById('confirmDeleteBtn').href = 'index.php?page=admin&action=delete_user&id=' + userId;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
