<?php
require_once __DIR__ . '/../api/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];
        $logger->logLogin($user['username']);
        header("Location: /inventory-system/pages/landing.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/login.css">
    <title>Login - Inventory System</title>
</head>
<body>
<div class="bg-wrapper">
  <img class="logo-bg" src="/inventory-system/public/img/ocd.png" alt="Background Logo">
  <div class="login_form">
    <h1 style="text-align: center; margin-bottom: 2rem; color: #2d3748; font-size: 1.8rem">Log In</h1>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Sign In</button>
    </form>
    
    <div class="register-prompt" style="text-align: center; margin: 1.5rem 0">
      <p style="color: #4a5568; font-size: 0.9rem; margin-bottom: 0.5rem;">Don't have an account?</p>
      <a href="register.php" style="color: #1622a7; text-decoration: none; font-weight: 600; font-size: 0.9rem">Create Account</a>
    </div>
  </div>
</div>
</body>
</html>