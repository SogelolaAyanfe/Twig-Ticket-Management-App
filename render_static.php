<?php
require_once __DIR__ . '/vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Initialize Twig
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

// Create /dist directory if it doesn’t exist
$distDir = __DIR__ . '/dist';
if (!is_dir($distDir)) {
    mkdir($distDir, 0777, true);
}

// Define your templates and corresponding output filenames
$pages = [
    'index.html' => 'index.html',
    'login.html' => 'login.html',
    'dashboard.html' => 'dashboard.html'
];

foreach ($pages as $template => $outputFile) {
    $html = $twig->render($template);
    file_put_contents($distDir . '/' . $outputFile, $html);
    echo "Rendered: $outputFile\n";
}

echo "✅ All Twig templates rendered to static HTML in /dist folder!\n";
