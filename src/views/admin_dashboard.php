<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Tableau de bord administrateur</h2>
    <div>
        <span class="text-muted">Connecté en tant que : <strong><?php echo $_SESSION['admin_username']; ?></strong></span>
        <a href="index.php?page=admin&action=logout" class="btn btn-outline-danger btn-sm ms-2">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['total_users']; ?></h3>
                <p class="text-muted">Utilisateurs</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-controller text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['total_games']; ?></h3>
                <p class="text-muted">Parties terminées</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                <h3 class="mt-2"><?php echo $stats['games_in_progress']; ?></h3>
                <p class="text-muted">Parties en cours</p>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-people-fill"></i> Gestion des utilisateurs</h5>
            </div>
            <div class="card-body">
                <p>Ajoutez, modifiez ou supprimez des utilisateurs de la ligue.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=users" class="btn btn-primary">
                        <i class="bi bi-list"></i> Voir les utilisateurs
                    </a>
                    <a href="index.php?page=admin&action=add_user" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Ajouter un utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-controller"></i> Gestion des parties</h5>
            </div>
            <div class="card-body">
                <p>Consultez et gérez l'historique des parties jouées.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=games" class="btn btn-info">
                        <i class="bi bi-list"></i> Voir les parties
                    </a>
                    <a href="index.php?page=history" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Vue utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions système -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-gear-fill"></i> Actions système</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Base de données</h6>
                        <p class="text-muted small">Assurez-vous que la base de données est correctement initialisée.</p>
                        <a href="../config/init_db.php" target="_blank" class="btn btn-outline-warning btn-sm">
                            <i class="bi bi-database"></i> Réinitialiser la DB
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h6>Navigation</h6>
                        <p class="text-muted small">Retourner à l'interface utilisateur.</p>
                        <a href="index.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-house"></i> Accueil utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
