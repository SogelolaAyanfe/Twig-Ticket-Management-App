<?php
require_once '../vendor/autoload.php';

// Load Twig
$loader = new \Twig\Loader\FilesystemLoader('../templates');
$twig = new \Twig\Environment($loader);

// Simple routing - adjust based on your app structure
$request = $_SERVER['REQUEST_URI'];

// Remove query string
$path = parse_url($request, PHP_URL_PATH);

switch ($path) {
    case '/':
    case '/login':
        echo $twig->render('login.twig');
        break;
    case '/signup':
        echo $twig->render('signup.twig');
        break;
    case '/dashboard':
        echo $twig->render('dashboard.twig');
        break;
    case '/tickets':
        echo $twig->render('tickets.twig');
        break;
    default:
        // Return 404 for unknown routes
        http_response_code(404);
        echo "Page not found: " . $path;
}
?>