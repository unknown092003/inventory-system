<?php
require_once __DIR__ . '/db.php';
session_start();

// Check if user is logged in (check for username since that's what's stored)
if (!isset($_SESSION['username'])) {
    header("Location: /inventory-system/index.php");
    exit();
}

// Get user_id from username if not in session
if (!isset($_SESSION['user_id'])) {
    $conn = Database::getInstance()->getConnection();
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        $_SESSION['user_id'] = $result['id'];
    }
}

$message = '';
$error = '';
$current_username = '';

$current_username = $_SESSION['username'];

if ($_POST) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        // Use singleton for DB connection
        $conn = Database::getInstance()->getConnection();
        // Get current user's data
        $stmt = $conn->prepare("SELECT username, password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Store username in session if not already there
            if (!isset($_SESSION['username'])) {
                $_SESSION['username'] = $result['username'];
            }
            
            // Verify current password
            if (password_verify($current_password, $result['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->execute([$hashed_password, $_SESSION['user_id']]);
                
                if ($update_stmt->rowCount() > 0) {
                    $message = "Password changed successfully!";
                } else {
                    $error = "Error updating password.";
                }
            } else {
                $error = "Current password is incorrect.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="/inventory-system/public/styles/landing.css">
    <style>
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #001938;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            background-color: #001938;
            color: #ffa200;
            padding: 12px 20px;
            border: 1px solid #ffa200;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-bottom: 10px;
        }
        .btn:hover {
            background-color: #ffa200;
            color: #001938;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
            border: none;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 style="text-align: center; color: #001938; margin-bottom: 30px;">Change Password</h2>
        
        <?php if ($message): ?>
            <div class="message success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" value="<?= htmlspecialchars($current_username) ?>" readonly style="background-color: #f5f5f5;">
            </div>
            
            <div class="form-group">
                <label for="current_password">Current Password:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Change Password</button>
            <button type="button" class="btn back-btn" onclick="window.location.href='landing.php'">Back to Dashboard</button>
        </form>
    </div>
</body>
</html>