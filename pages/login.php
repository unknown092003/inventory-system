<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: landing.php');
    exit;
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
    
    <?php if (isset($_GET['error'])): ?>
      <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
      <div class="success-message"><?= htmlspecialchars($_GET['success']) ?></div>
    <?php endif; ?>
    
    <form method="post" action="login_process.php">
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