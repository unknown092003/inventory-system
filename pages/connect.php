<?php
// connect.php
$host = 'localhost';
$username = 'root';  // Default XAMPP username
$password = '';      // Default XAMPP password
$database = 'inventory-system';

try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>