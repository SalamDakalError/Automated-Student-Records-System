<?php
// Simple logout: destroy session and redirect to login page
session_start();

// capture email (if present) so we can remove tokens for this user
$currentEmail = $_SESSION['email'] ?? null;

// Unset all session variables
$_SESSION = array();

// If session uses cookies, remove the session cookie
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}

// Destroy the session
session_unset();
session_destroy();

require_once 'config.php';
// Redirect back to login page
header('Location: ' . $base_url . 'Login/loginpage.php');
exit();
