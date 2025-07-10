<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-dark text-white text-center">
                <h4><i class="bi bi-shield-lock-fill"></i> Administration</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form action="index.php?page=admin&action=login" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-box-arrow-in-right"></i> Se connecter
                        </button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <strong>Identifiants par défaut :</strong><br>
                        Utilisateur : admin<br>
                        Mot de passe : admin123
                    </small>
                </div>

                <div class="mt-4 text-center">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
