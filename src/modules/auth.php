<?php
// src/modules/auth.php

require_once __DIR__ . '/bcrypt.php';
require_once __DIR__ . '/../config/session.php';

const TWENTY_FOUR_HOURS_IN_MILLISECONDS = 24 * 60 * 60 * 1000;

class Auth {
    
    /**
     * Login function
     * Returns: ['success' => bool, 'error' => string|null, 'message' => string|null]
     */
    public static function login($email, $password) {
        // Get user from session storage (equivalent to localStorage)
        $user = $_SESSION['user'];
        
        $error = null;
        $success = null;
        
        // Debug: Check if user exists
        if (empty($user['email']) || empty($user['password'])) {
            $error = "No user registered. Please sign up first.";
            return [
                'success' => false,
                'error' => $error,
                'message' => null
            ];
        }
        
        if ($email == $user['email'] && Bcrypt::compare($password, $user['password'])) {
            $success = "Login successful! Redirecting...";
            
            // Create access token
            $accessToken = Bcrypt::encrypt([
                'email' => $email,
                'password' => $user['password'],
                'exp' => (int)(microtime(true) * 1000) + TWENTY_FOUR_HOURS_IN_MILLISECONDS
            ]);
            
            // Set session
            $_SESSION['session'] = ['accessToken' => $accessToken];
            
            return [
                'success' => true,
                'error' => null,
                'message' => $success
            ];
        } else {
            $error = "Wrong credentials";
            
            return [
                'success' => false,
                'error' => $error,
                'message' => null
            ];
        }
    }
    
    /**
     * Sign up function
     * Returns: ['success' => bool, 'error' => string|null, 'data' => array|null]
     */
    public static function signUp($email, $password) {
        // Get existing user from session
        $user = $_SESSION['user'];
        
        $error = null;
        
        // Validate email format
        $isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        
        // Validate password length
        $isPasswordValid = strlen($password) >= 10;
        
        // Check if email already exists
        if (!empty($user['email']) && $user['email'] === $email) {
            $error = "Email already exists!";
            return [
                'success' => false,
                'error' => $error,
                'data' => null
            ];
        }
        
        if (!$isEmailValid) {
            $error = "String is not an email";
            return [
                'success' => false,
                'error' => $error,
                'data' => null
            ];
        }
        
        if (!$isPasswordValid) {
            $error = "Password must be atleast 10 characters";
            return [
                'success' => false,
                'error' => $error,
                'data' => null
            ];
        }
        
        // Hash password
        $hashedPassword = Bcrypt::hash($password);
        
        // Store user in session
        $_SESSION['user'] = [
            'email' => $email,
            'password' => $hashedPassword
        ];
        
        return [
            'success' => true,
            'error' => null,
            'data' => [
                'email' => $email,
                'password' => $password
            ]
        ];
    }
    
    /**
     * Logout function
     * Clears the session
     */
    public static function logout() {
        unset($_SESSION['session']);
        return ['success' => true];
    }
    
    /**
     * Check if user is authorized
     * Returns: bool
     */
    public static function isAuthorized() {
        // Get session
        $session = isset($_SESSION['session']) ? $_SESSION['session'] : null;
        
        if (!$session || !isset($session['accessToken'])) {
            return false;
        }
        
        $accessToken = $session['accessToken'];
        
        // Decrypt token
        $decryptedToken = Bcrypt::decrypt($accessToken);
        
        if (!$decryptedToken) {
            return false;
        }
        
        // Check if token is expired
        $currentTime = (int)(microtime(true) * 1000);
        return isset($decryptedToken['exp']) && $decryptedToken['exp'] > $currentTime;
    }
}
?>