<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Skull King League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-suit-spade-fill"></i> Skull King League
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : ''; ?>" href="index.php">
                            <i class="bi bi-house-fill"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'ongoing') ? 'active' : ''; ?>" href="index.php?page=ongoing">
                            <i class="bi bi-play-circle-fill"></i> Parties en cours
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'ranking') ? 'active' : ''; ?>" href="index.php?page=ranking">
                            <i class="bi bi-trophy-fill"></i> Classement
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'history') ? 'active' : ''; ?>" href="index.php?page=history">
                            <i class="bi bi-clock-history"></i> Historique
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (isset($_GET['page']) && ($_GET['page'] == 'rules' || $_GET['page'] == 'custom_rules')) ? 'active' : ''; ?>" 
                           href="#" id="rulesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-book-fill"></i> Règles
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="rulesDropdown">
                            <li>
                                <a class="dropdown-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'rules') ? 'active' : ''; ?>" 
                                   href="index.php?page=rules">
                                    <i class="bi bi-trophy text-warning"></i> Règles Officielles
                                    <small class="d-block text-muted">Pour les parties classées</small>
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item <?php echo (isset($_GET['page']) && $_GET['page'] == 'custom_rules') ? 'active' : ''; ?>" 
                                   href="index.php?page=custom_rules">
                                    <i class="bi bi-gear text-secondary"></i> Règles Custom
                                    <small class="d-block text-muted">Extensions & variantes</small>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo (isset($_GET['page']) && $_GET['page'] == 'admin') ? 'active' : ''; ?>" href="index.php?page=admin">
                            <i class="bi bi-gear-fill"></i> Admin
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4"><?php
