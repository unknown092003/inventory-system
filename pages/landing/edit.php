 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/data.css">
 </head>
 <body>
    <h1>Edit Inventory Items</h1>
 
    <!-- Search Form -->
    <form method="GET" action="" style="margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Search items..."
             value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>
 
    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
    
    $search = $_GET['search'] ?? '';
    $sql = "SELECT * FROM inventory";
    $params = [];
    
    if ($search !== '') {
        $sql .= " WHERE property_number LIKE ? OR description LIKE ? OR model_number LIKE ?
               OR serial_number LIKE ? OR person_accountable LIKE ?";
        $searchTerm = "%$search%";
        $params = array_fill(0, 5, $searchTerm);
    }
    
    $sql .= "  ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
 
    <table class="inventory-table">
        <thead>
           <tr>
              <th>Property Number</th>
              <th>Description</th>
              <th>Model Number</th>
              <th>Serial Number</th>
              <th>Actions</th>
           </tr>
        </thead>
        <tbody>
           <?php foreach ($items as $item): ?>
           <tr>
              <td><?= htmlspecialchars($item['property_number']) ?></td>
              <td><?= htmlspecialchars($item['description']) ?></td>
              <td><?= htmlspecialchars($item['model_number']) ?></td>
              <td><?= htmlspecialchars($item['serial_number']) ?></td>
              <td>
                 <a href="/inventory-system/pages/landing/edit-item.php?property_number=<?= urlencode($item['property_number']) ?>" class="edit-btn">Edit</a>
              </td>
           </tr>
           <?php endforeach; ?>
        </tbody>
    </table>
    <h2>Activity Logs</h2>
  <?php
  $logSql = "SELECT * FROM activity_log ORDER BY timestamp DESC";
  $logStmt = $pdo->prepare($logSql);
  $logStmt->execute();
  $logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <?php 
  $logSql = "SELECT * FROM activity_log ORDER BY action DESC";
  $logStmot = $pdo->prepare($logSql);
  $logStmt->execute();
  $logs = $logStmt->fetchAll(PDO::FETCH_ASSOC);
  ?>
  
  <table class="activity-log-table">
    <thead>
      <tr>
        <th>Log ID</th>
        <th>Action</th>
        <th>Timestamp</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($logs)): ?>
      <tr>
        <td colspan="3">No activity logs available.</td>
      </tr>
      <?php else: ?>
      <?php foreach ($logs as $log): ?>
      <tr>
        <td><?= htmlspecialchars($log['id'] ?? '') ?></td>
        <td><?= htmlspecialchars($log['action'] ?? '') ?></td>
        <td><?= htmlspecialchars($log['timestamp'] ?? '') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
</table>

<?php
 // Log activity with prepared statement
 $lastInsertId = $conn->lastInsertId();
 $logStmt = $conn->prepare("INSERT INTO activity_log
     (action_type, table_name, record_id, user, description, timestamp)
     VALUES
     (:action_type, :table_name, :record_id, :user, :description, NOW())");
 
 $logStmt->execute([
     ':action_type' => 'create',
     ':table_name' => 'inventory',
     ':record_id' => $lastInsertId,
     ':user' => $_POST['accountable_person'],
     ':description' => 'Created new inventory item: ' . $_POST['description']
 ]);
 
?>

  
 </body>
 </html>