<?php
session_start();

require_once __DIR__ . '/../pages/db.php';
$db = Database::getInstance()->getConnection();

// Include Logger
require_once 'Logger.php';
$logger = new Logger($db);

// Simple authentication check function
function isAuthenticated() {
    return isset($_SESSION['username']) && !empty($_SESSION['username']);
}

// Redirect to login if not authenticated
function requireAuth() {
    if (!isAuthenticated()) {
        header("Location: pages/login.php");
        exit();
    }
}
?>