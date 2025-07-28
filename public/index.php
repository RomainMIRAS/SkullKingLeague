<?php
session_start();

// Autoloader simple
spl_autoload_register(function ($class_name) {
    $paths = [
        '../src/models/',
        '../src/controllers/',
        '../config/'
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});

// Routage simple
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Inclure le header
include '../src/views/header.php';

// Routage des pages
switch($page) {
    case 'home':
        include '../src/views/home.php';
        break;
    case 'game':
        include '../src/controllers/GameController.php';
        break;
    case 'games_in_progress':
        include '../src/views/games_in_progress.php';
        break;
    case 'ranking':
        include '../src/views/ranking.php';
        break;
    case 'history':
        include '../src/views/history.php';
        break;
    case 'admin':
        include '../src/controllers/AdminController.php';
        break;
    default:
        include '../src/views/home.php';
}

// Inclure le footer
include '../src/views/footer.php';
?>
