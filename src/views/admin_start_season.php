<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="bi bi-exclamation-triangle"></i> Commencer une Nouvelle Saison
                </h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i>
                    <strong>Attention :</strong> Cette action va :
                    <ul class="mb-0 mt-2">
                        <li>Terminer la saison actuelle et sauvegarder les résultats finaux</li>
                        <li>Supprimer toutes les parties en cours non terminées</li>
                        <li>Remettre tous les ELO des joueurs à 1000</li>
                        <li>Créer une nouvelle saison</li>
                    </ul>
                    <p class="mt-2 mb-0"><strong>Cette action est irréversible !</strong></p>
                </div>

                <?php if ($current_season): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h6><i class="bi bi-calendar-event"></i> Saison Actuelle</h6>
                    </div>
                    <div class="card-body">
                        <h5><?php echo htmlspecialchars($current_season['name']); ?></h5>
                        <p class="text-muted">
                            Commencée le <?php echo date('d/m/Y à H:i', strtotime($current_season['start_date'])); ?>
                        </p>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="season_name" class="form-label">
                            <i class="bi bi-tag"></i> Nom de la nouvelle saison *
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="season_name" 
                               name="season_name" 
                               placeholder="ex: Saison Été 2025, Championnat Automne..."
                               required
                               maxlength="100">
                        <div class="form-text">
                            Choisissez un nom descriptif pour cette saison (maximum 100 caractères)
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <a href="index.php?page=admin&action=seasons" class="btn btn-secondary w-100">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirmNewSeason()">
                                <i class="bi bi-plus-circle"></i> Commencer la Saison
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Preview of what will happen -->
        <div class="card mt-4">
            <div class="card-header">
                <h6><i class="bi bi-list-check"></i> Que va-t-il se passer ?</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-danger">Saison actuelle</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle text-success"></i> Les résultats finaux seront sauvegardés</li>
                            <li><i class="bi bi-check-circle text-success"></i> Le classement final sera archivé</li>
                            <li><i class="bi bi-check-circle text-success"></i> La saison sera marquée comme terminée</li>
                            <li><i class="bi bi-trash text-danger"></i> Les parties en cours seront supprimées</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Nouvelle saison</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-arrow-clockwise text-primary"></i> Tous les ELO remis à 1000</li>
                            <li><i class="bi bi-calendar-plus text-primary"></i> Nouvelle saison créée</li>
                            <li><i class="bi bi-trophy text-primary"></i> Nouveau classement vierge</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmNewSeason() {
    const seasonName = document.getElementById('season_name').value.trim();
    if (!seasonName) {
        alert('Veuillez entrer un nom pour la nouvelle saison.');
        return false;
    }
    
    return confirm(
        `Êtes-vous sûr de vouloir commencer la nouvelle saison "${seasonName}" ?\n\n` +
        `Cette action va :\n` +
        `- Terminer la saison actuelle\n` +
        `- Supprimer toutes les parties en cours non terminées\n` +
        `- Remettre tous les ELO à 1000\n` +
        `- Créer la nouvelle saison\n\n` +
        `Cette action est IRRÉVERSIBLE !`
    );
}
</script>