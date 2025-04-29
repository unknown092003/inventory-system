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
              <th>person accountable<th>
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
              <td><?= htmlspecialchars($item['person_accountable']) ?></td>

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
  
 
 </body>
 </html>