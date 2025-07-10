<?php
require_once '../config/database.php';
require_once '../src/models/User.php';
require_once '../src/models/Game.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$game = new Game($db);

$action = $_GET['action'] ?? 'login';

// Vérifier si l'utilisateur est connecté
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

switch($action) {
    case 'login':
        if ($_POST && isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            
            // Vérifier les identifiants
            $query = "SELECT * FROM admin WHERE username = ? LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $username);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: index.php?page=admin&action=dashboard");
                exit;
            } else {
                $error = "Identifiants incorrects";
            }
        }
        
        include '../src/views/admin_login.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php");
        exit;

    case 'dashboard':
        if (!isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=login");
            exit;
        }
        
        // Statistiques pour le dashboard
        $stats_query = "SELECT 
            (SELECT COUNT(*) FROM users) as total_users,
            (SELECT COUNT(*) FROM games WHERE status = 'terminee') as total_games,
            (SELECT COUNT(*) FROM games WHERE status = 'en_cours') as games_in_progress
        ";
        $stats = $db->query($stats_query)->fetch(PDO::FETCH_ASSOC);
        
        include '../src/views/admin_dashboard.php';
        break;

    case 'users':
        if (!isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=login");
            exit;
        }
        
        $users = $user->getAll();
        include '../src/views/admin_users.php';
        break;

    case 'add_user':
        if (!isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=login");
            exit;
        }
        
        if ($_POST && isset($_POST['pseudo'])) {
            $user->pseudo = trim($_POST['pseudo']);
            if (!empty($user->pseudo)) {
                if ($user->create()) {
                    header("Location: index.php?page=admin&action=users&success=user_added");
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout de l'utilisateur (pseudo déjà existant ?)";
                }
            } else {
                $error = "Le pseudo ne peut pas être vide";
            }
        }
        
        include '../src/views/admin_add_user.php';
        break;

    case 'delete_user':
        if (!isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=login");
            exit;
        }
        
        if (isset($_GET['id'])) {
            $user->id = $_GET['id'];
            if ($user->delete()) {
                header("Location: index.php?page=admin&action=users&success=user_deleted");
                exit;
            }
        }
        
        header("Location: index.php?page=admin&action=users&error=delete_failed");
        exit;

    case 'games':
        if (!isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=login");
            exit;
        }
        
        $all_games = $game->getAll(100);
        include '../src/views/admin_games.php';
        break;

    case 'delete_game':
        if (!isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=login");
            exit;
        }
        
        if (isset($_GET['id'])) {
            $game->id = $_GET['id'];
            if ($game->delete()) {
                header("Location: index.php?page=admin&action=games&success=game_deleted");
                exit;
            }
        }
        
        header("Location: index.php?page=admin&action=games&error=delete_failed");
        exit;

    default:
        if (isAdminLoggedIn()) {
            header("Location: index.php?page=admin&action=dashboard");
        } else {
            header("Location: index.php?page=admin&action=login");
        }
        exit;
}
?>
