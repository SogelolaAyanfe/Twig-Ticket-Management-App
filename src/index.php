<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader);


echo $twig->render('landing.twig', [
    'title' => 'Welcome Page',
    'heading' => 'Hello from Twig!',
    'message' => 'Your Twig environment is working perfectly 🎉'
]);
