<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/home.css">
    <title>Home</title>
</head>
<body>
    <div class="home">
        <h1>Recent Activity</h1>
        
        <?php
        try {
            // Establish database connection
            $pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "", [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Fetch all activity logs ordered by timestamp
            $stmt = $pdo->query("SELECT action_type, description, user, timestamp 
                                FROM activity_log
                                ORDER BY timestamp DESC");
            $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
        ?>
        
        <table class="activity-log">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                    <td><?= htmlspecialchars($log['action_type']) ?></td>
                    <td><?= htmlspecialchars($log['description'] ?? 'No description') ?></td>
                    <td><?= htmlspecialchars($log['user'] ?? 'Anonymous') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
