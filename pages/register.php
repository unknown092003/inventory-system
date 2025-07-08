<?php
require_once __DIR__ . '/../api/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $full_name = trim($_POST['full_name']);

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords don't match!";
    } else {
        // Check if username exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->rowCount() > 0) {
            $error = "Username already exists!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $db->prepare("INSERT INTO users (username, password, full_name) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $hashed_password, $full_name])) {
                $success = "Registration successful! You can now login.";
                // Optionally log this action if you want
                $logger->logAction('user_created', 'New user registered', NULL, $username);
                header("Location: login.php");
                exit();
            } else {
                $error = "Registration failed: " . implode(' | ', $db->errorInfo());
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
    <title>Register</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande",
                        "Lucida Sans Unicode", Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(to right, #080c2d, #6a2402);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .register-form {
            background-color: rgba(255, 255, 255, 0.95);
            border: 2px solid #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            transition: all 0.3s ease;
        }

        .register-form:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .register-form h1 {
            font-size: 1.8rem;
            color: #1622a7;
            margin-bottom: 2rem;
            text-align: center;
        }

        .register-form input {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1.2rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .register-form input:focus {
            border-color: #1622a7;
            box-shadow: 0 0 0 3px rgba(22, 34, 167, 0.2);
            outline: none;
        }

        .register-form button {
            width: 100%;
            padding: 1rem;
            background: #1622a7;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .register-form button:hover {
            background: #0f187f;
            transform: translateY(-2px);
        }

        .error-message {
            color: #e53e3e;
            background-color: #fed7d7;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #feb2b2;
        }

        .success-message {
            color: #38a169;
            background-color: #c6f6d5;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            text-align: center;
            border: 1px solid #9ae6b4;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #4a5568;
        }

        .login-link a {
            color: #1622a7;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-form">
        <h1>Create Account</h1>
        
        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p style='color:green'>$success</p>"; ?>
        
        <form method="post">

    <input type="text" placeholder="Full Name" name="full_name" value="<?= isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : '' ?>" required>

    <input type="text" placeholder="Username" name="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required>

    <input type="password" placeholder="Password" name="password" required>

    <input type="password" placeholder="Confirm Password" name="confirm_password" required>

    <button type="submit" name="signUp">Register</button>
</form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
</body>
</html>