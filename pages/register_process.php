<?php
// register_process.php
require_once __DIR__ . '/db.php';

// Get database connection
$conn = Database::getInstance()->getConnection();

if (isset($_POST['signUp'])) {
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($username) || empty($password)) {
        header("Location: register.php?error=All fields are required");
        exit;
    }

    if (strlen($password) < 8) {
        header("Location: register.php?error=Password must be at least 8 characters");
        exit;
    }

    try {
        // Check if username exists
        $checkUsername = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $checkUsername->bindParam(':username', $username);
        $checkUsername->execute();

        if ($checkUsername->rowCount() > 0) {
            header("Location: register.php?error=Username already exists");
            exit;
        }

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $insertQuery = $conn->prepare("INSERT INTO users (firstName, lastName, username, password) 
                                      VALUES (:firstName, :lastName, :username, :password)");
        $insertQuery->bindParam(':firstName', $firstName);
        $insertQuery->bindParam(':lastName', $lastName);
        $insertQuery->bindParam(':username', $username);
        $insertQuery->bindParam(':password', $hashedPassword);
        
        if ($insertQuery->execute()) {
            header("Location: login.php?success=Registration successful. Please login.");
            exit;
        }
    } catch(PDOException $e) {
        header("Location: register.php?error=Database error: " . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>