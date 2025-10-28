<?php
// src/config/session.php

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    
    session_start();
}

// Initialize session data if not exists
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = [
        'email' => '',
        'password' => ''
    ];
}

if (!isset($_SESSION['session'])) {
    $_SESSION['session'] = [];
}

if (!isset($_SESSION['tickets'])) {
    $_SESSION['tickets'] = [];
}
?>