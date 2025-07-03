<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /inventory-system/index.php");
    exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "inventory_system");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $message = "User deleted successfully!";
    } else {
        $error = "Error deleting user.";
    }
    $stmt->close();
}

// Get all users
$sql = "SELECT id, username FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nebula | User Accounts</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0f172a;
            --secondary: #7c3aed;
            --accent: #10b981;
            --danger: #ef4444;
            --light: #f8fafc;
            --dark: #020617;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-highlight: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: var(--light);
            min-height: 100vh;
            padding: 2rem;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .dashboard-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(90deg, var(--primary), rgba(15, 23, 42, 0.8));
            border-bottom: 1px solid var(--glass-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }

        .logo-icon {
            color: var(--secondary);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: var(--glass-highlight);
            transform: translateY(-2px);
        }

        .content-container {
            padding: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--secondary), var(--accent));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.15);
            border-left: 4px solid var(--accent);
            color: var(--accent);
        }

        .alert-error {
            background-color: rgba(239, 68, 68, 0.15);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        .accounts-table-container {
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
        }

        .accounts-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--glass);
        }

        .accounts-table thead {
            background: linear-gradient(90deg, var(--primary), rgba(15, 23, 42, 0.9));
        }

        .accounts-table th {
            padding: 1.25rem 1.5rem;
            text-align: left;
            font-weight: 600;
            color: var(--light);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
        }

        .accounts-table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--glass-border);
            font-weight: 500;
        }

        .accounts-table tr:last-child td {
            border-bottom: none;
        }

        .accounts-table tr:hover {
            background: var(--glass-highlight);
        }

        .user-id {
            color: var(--secondary);
            font-weight: 600;
        }

        .username {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .delete-btn {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .delete-btn:hover {
            background-color: rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
        }

        .empty-state {
            padding: 3rem;
            text-align: center;
            color: var(--light-text);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--glass-border);
        }

        .stats-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
            color: var(--light-text);
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .accounts-table th, 
            .accounts-table td {
                padding: 1rem;
            }
            
            .username {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="logo">
                <img src="/public/img/ocd.png" alt="">
                <span style = "color: orange;">Office Of Civil Defense Cordillera Administrative Region</span>
            </div>
            <a href="landing.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
        
        <div class="content-container">
            <h1 class="page-title">Inventory System Accounts</h1>
            
            <?php if (isset($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="accounts-table-container">
                <?php if ($result && $result->num_rows > 0): ?>
                    <table class="accounts-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): 
                                $initial = strtoupper(substr($row['username'], 0, 1));
                            ?>
                                <tr>
                                    <td class="user-id">#<?= htmlspecialchars($row['id']) ?></td>
                                    <td>
                                        <div class="username">
                                            <div class="user-avatar"><?= $initial ?></div>
                                            <?= htmlspecialchars($row['username']) ?>
                                        </div>
                                    </td>

                                    <td>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="delete_user" class="action-btn delete-btn">
                                                <i class="fas fa-trash-alt"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-user-slash"></i>
                        </div>
                        <h3>No User Accounts Found</h3>
                        <p>The system doesn't have any registered users yet.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="stats-footer">
                <div>
                    <i class="fas fa-database"></i>
                    Total accounts: <?= $result ? $result->num_rows : 0 ?>
                </div>
                <div>
                    <i class="fas fa-clock"></i>
                    Last updated: <?= date("M d, Y H:i") ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add confirmation for delete actions
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Are you sure you want to delete this user?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>