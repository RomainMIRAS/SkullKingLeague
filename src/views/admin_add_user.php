<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="bi bi-person-plus"></i> Ajouter un utilisateur</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form action="index.php?page=admin&action=add_user" method="POST">
                    <div class="mb-3">
                        <label for="pseudo" class="form-label">Pseudo du joueur</label>
                        <input type="text" class="form-control" id="pseudo" name="pseudo" 
                               placeholder="Entrez le pseudo" required maxlength="50">
                        <div class="form-text">Le pseudo doit être unique et ne peut pas être modifié par la suite.</div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i>
                        <strong>Information :</strong> Le nouvel utilisateur commencera avec un ELO de 1000 points.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php?page=admin&action=users" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-person-plus"></i> Ajouter l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
