<?php
// Enhanced session configuration
session_set_cookie_params([
    'lifetime' => 1800,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
} else {
    session_regenerate_id(true);
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /inventory-system/pages/login.php');
    exit;
}

// Session timeout handling (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: /inventory-system/pages/login.php?error=Session expired');
    exit;
}

$_SESSION['last_activity'] = time();
// Regenerate session ID periodically to prevent fixation attacks
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();

    error_log("Session ID regenerated for user ID: " . $_SESSION['user_id']);
}
?>