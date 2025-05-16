<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/data.css">
    <title>Edit Inventory Items</title>
</head>
<body>
    <h1 id="name_edit">Edit Inventory Items</h1>

    <!-- Search Form -->
    <div class="search-sort-container">
        <!-- Combined Search and Sort Form -->
        <form method="GET" action="">
            <!-- Hidden field to preserve page parameter -->
            <input type="hidden" name="page" value="edit">
            
            <!-- Search Input -->
            <input type="text" name="search" class="search-box" 
                   placeholder="Search inventory..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            
            <!-- Sort Dropdown -->
            <select name="sort" class="sort-select">
                <option value="property_asc" <?= ($_GET['sort'] ?? 'property_asc') === 'property_asc' ? 'selected' : '' ?>>
                    Property Number (A-Z)
                </option>
                <option value="property_desc" <?= ($_GET['sort'] ?? '') === 'property_desc' ? 'selected' : '' ?>>
                    Property Number (Z-A)
                </option>
                <option value="person_asc" <?= ($_GET['sort'] ?? '') === 'person_asc' ? 'selected' : '' ?>>
                    Accountable Person (A-Z)
                </option>
                <option value="person_desc" <?= ($_GET['sort'] ?? '') === 'person_desc' ? 'selected' : '' ?>>
                    Accountable Person (Z-A)
                </option>
                <option value="description_asc" <?= ($_GET['sort'] ?? '') === 'description_asc' ? 'selected' : '' ?>>
                    Description (A-Z)
                </option>
                <option value="description_desc" <?= ($_GET['sort'] ?? '') === 'description_desc' ? 'selected' : '' ?>>
                    Description (Z-A)
                </option>
            </select>
            
            <button id="apply" type="submit">Apply</button>
            
            <!-- Clear button when filters are active -->
            <?php if (isset($_GET['search']) || (isset($_GET['sort']) && $_GET['sort'] != 'property_asc')): ?>
                <a href="?page=edit" class="button">Clear Filters</a>
            <?php endif; ?>
        </form>
    </div>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
    
    $search = $_GET['search'] ?? '';
    $sort = $_GET['sort'] ?? 'property_asc';
    $sql = "SELECT * FROM inventory";
    $params = [];
    
    if ($search !== '') {
        $sql .= " WHERE property_number LIKE ? OR description LIKE ? OR model_number LIKE ?
              LIKE ? OR person_accountable LIKE ?";
        $searchTerm = "%$search%";
        $params = array_fill(0, 5, $searchTerm);
    }
    
    // Add sorting based on available columns
    switch ($sort) {
        case 'property_asc':
            $sql .= " ORDER BY property_number ASC";
            break;
        case 'property_desc':
            $sql .= " ORDER BY property_number DESC";
            break;
        case 'person_asc':
            $sql .= " ORDER BY person_accountable ASC";
            break;
        case 'person_desc':
            $sql .= " ORDER BY person_accountable DESC";
            break;
        case 'description_asc':
            $sql .= " ORDER BY description ASC";
            break;
        case 'description_desc':
            $sql .= " ORDER BY description DESC";
            break;
        default:
            $sql .= " ORDER BY property_number ASC";
            break;
    }
    
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
              <th>Person Accountable</th>
              <th>Remarks</th>
              <th>Actions</th>
           </tr>
        </thead>
        <tbody>
           <?php if (count($items) > 0): ?>
               <?php foreach ($items as $item): ?>
               <tr>
                  <td><?= htmlspecialchars($item['property_number']) ?></td>
                  <td><?= htmlspecialchars($item['description']) ?></td>
                  <td><?= htmlspecialchars($item['model_number']) ?></td>
                  <td><?= htmlspecialchars($item['person_accountable']) ?></td>
                  <td><?= htmlspecialchars($item['remarks']) ?></td>
                  <td>
                     <a href="/inventory-system/pages/landing/edit-item.php?property_number=<?= urlencode($item['property_number']) ?>" class="edit-btn">Edit</a>
                     <a href="/inventory-system/pages/landing/delete-item.php?property_number=<?= urlencode($item['property_number']) ?>" class="delete-btn">Delete</a>
                  </td>
               </tr>
               <?php endforeach; ?>
           <?php else: ?>
               <tr>
                  <td colspan="6">No items found.</td>
               </tr>
           <?php endif; ?>
        </tbody>
    </table>
</body>
</html>