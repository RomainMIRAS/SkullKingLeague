<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Tableau de bord administrateur</h2>
    <div class="d-flex align-items-center">
        <span class="text-muted me-3">Connecté en tant que : <strong><?php echo $_SESSION['admin_username']; ?></strong></span>
        <a href="index.php?page=admin&action=logout" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
        </a>
    </div>
</div>

<!-- Current Season Status -->
<?php if ($current_season): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white d-flex align-items-center">
                <i class="bi bi-calendar-check me-2"></i>
                <strong>Saison Actuelle</strong>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1"><?php echo htmlspecialchars($current_season['name']); ?></h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3"></i> Démarrée le <?php echo date('d/m/Y', strtotime($current_season['start_date'])); ?>
                            <?php 
                            $days_running = floor((time() - strtotime($current_season['start_date'])) / (60 * 60 * 24));
                            echo " · $days_running jours";
                            ?>
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="index.php?page=admin&action=seasons" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear"></i> Gérer les saisons
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <div class="flex-grow-1">
                <strong>Aucune saison active</strong> - Créez une nouvelle saison pour commencer la compétition
            </div>
            <a href="index.php?page=admin&action=start_new_season" class="btn btn-warning btn-sm">
                <i class="bi bi-plus-circle"></i> Créer une saison
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main Statistics -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-people-fill text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold"><?php echo $stats['total_users']; ?></h3>
                <p class="text-muted mb-0">Utilisateurs inscrits</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-controller text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold"><?php echo $stats['total_games']; ?></h3>
                <p class="text-muted mb-0">Parties terminées</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold"><?php echo $stats['games_in_progress']; ?></h3>
                <p class="text-muted mb-0">Parties en cours</p>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="d-flex justify-content-center align-items-center mb-3">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="bi bi-calendar-event text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
                <h3 class="fw-bold"><?php echo $stats['total_seasons']; ?></h3>
                <p class="text-muted mb-0">Saisons créées</p>
            </div>
        </div>
    </div>
</div>

<!-- Management Actions -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3"><i class="bi bi-gear-fill"></i> Gestion de la Ligue</h4>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0 d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 rounded me-2 p-2">
                        <i class="bi bi-calendar-event text-primary"></i>
                    </div>
                    Saisons
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Gérez les saisons, démarrez de nouvelles compétitions et consultez l'historique.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=seasons" class="btn btn-outline-primary">
                        <i class="bi bi-list"></i> Voir toutes les saisons
                    </a>
                    <?php if ($current_season): ?>
                    <a href="index.php?page=admin&action=start_new_season" class="btn btn-warning">
                        <i class="bi bi-plus-circle"></i> Nouvelle saison
                    </a>
                    <?php else: ?>
                    <a href="index.php?page=admin&action=start_new_season" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Créer première saison
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0 d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 rounded me-2 p-2">
                        <i class="bi bi-people-fill text-success"></i>
                    </div>
                    Utilisateurs
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Ajoutez, modifiez ou supprimez des utilisateurs de la ligue.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=users" class="btn btn-outline-success">
                        <i class="bi bi-list"></i> Voir les utilisateurs
                    </a>
                    <a href="index.php?page=admin&action=add_user" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Ajouter un utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0 d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 rounded me-2 p-2">
                        <i class="bi bi-controller text-info"></i>
                    </div>
                    Parties
                </h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Consultez et gérez l'historique des parties jouées.</p>
                <div class="d-grid gap-2">
                    <a href="index.php?page=admin&action=games" class="btn btn-outline-info">
                        <i class="bi bi-list"></i> Voir les parties
                    </a>
                    <a href="index.php?page=history" class="btn btn-info">
                        <i class="bi bi-eye"></i> Vue utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Links & System Actions -->
<div class="row">
    <div class="col-lg-8 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0"><i class="bi bi-lightning-fill"></i> Accès Rapide</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Classements</h6>
                        <p class="text-muted small mb-2">Consultez les classements et statistiques des joueurs.</p>
                        <a href="index.php?page=ranking" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-trophy"></i> Voir le classement
                        </a>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold">Historique</h6>
                        <p class="text-muted small mb-2">Parcourez l'historique complet des parties.</p>
                        <a href="index.php?page=history" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-clock-history"></i> Voir l'historique
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Interface utilisateur</h6>
                        <p class="text-muted small mb-2">Retourner à l'interface principale.</p>
                        <a href="index.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-house"></i> Accueil utilisateur
                        </a>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold">Nouvelle partie</h6>
                        <p class="text-muted small mb-2">Démarrer une nouvelle partie rapidement.</p>
                        <a href="index.php?page=game&action=create" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Créer une partie
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0"><i class="bi bi-gear-fill"></i> Système</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-bold">Base de données</h6>
                    <p class="text-muted small mb-2">Réinitialiser la structure de la base de données.</p>
                    <a href="../config/init_db.php" target="_blank" class="btn btn-outline-warning btn-sm">
                        <i class="bi bi-database"></i> Réinit. DB
                    </a>
                </div>
                <div>
                    <h6 class="fw-bold">Documentation</h6>
                    <p class="text-muted small mb-2">Consultez la documentation technique.</p>
                    <a href="../DEVELOPERS.md" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-book"></i> Guide dev
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
