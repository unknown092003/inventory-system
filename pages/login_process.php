<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Username and password are required");
        exit;
    }

    try {
        $conn = getConnection();
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['last_activity'] = time();
            header('Location: landing.php');
            exit;
        } else {
            header("Location: login.php?error=Invalid username or password");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: login.php?error=Database error: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>