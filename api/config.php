<?php
session_start();

// Database connection
$db = new mysqli('localhost', 'root', '', 'inventory_system');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

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
        header("Location: index.php");
        exit();
    }
}
?>