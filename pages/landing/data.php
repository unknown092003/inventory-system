<!-- <script>
  setTimeout(() => {
    location.reload();
  }, 2000); // 30,000 ms = 30 seconds
</script> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Database</title>
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/data.css">
</head> 
<body>
    <h1>Inventory Database</h1>
    
    <div class="search-sort-container">
        <!-- Combined Search and Sort Form -->
        <form method="GET" action="">
            <!-- Hidden field to preserve page parameter -->
            <input type="hidden" name="page" value="data">
            
            <!-- Search Input -->
            <input type="text" name="search" class="search-box" 
                   placeholder="Search inventory..." 
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            
            <!-- Sort Dropdown -->
            <select name="sort" class="sort-select">
                <option value="date_desc" <?= ($_GET['sort'] ?? 'date_desc') === 'date_desc' ? 'selected' : '' ?>>
                    Newest First
                </option>
                <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>
                    Oldest First
                </option>
                <option value="property_asc" <?= ($_GET['sort'] ?? '') === 'property_asc' ? 'selected' : '' ?>>
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
            </select>
            
            <button id="apply" type="submit">Apply</button>
            
            <!-- Clear button when filters are active -->
            <?php if (isset($_GET['search']) || (isset($_GET['sort']) && $_GET['sort'] != 'date_desc')): ?>
                <a href="?page=data" class="button">Clear Filters</a>
            <?php endif; ?>
        </form>

        <button onclick="redirectToExport()">View Layout</button>
        <script>
            function redirectToExport() {
            const search = encodeURIComponent('<?= $_GET['search'] ?? '' ?>');
            const sort = encodeURIComponent('<?= $_GET['sort'] ?? 'date_desc' ?>');
            window.location.href = `export.php?search=${search}&sort=${sort}`;
            }
        </script>
    </div>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
    
    // Base query
    $sql = "SELECT * FROM inventory WHERE 1=1";
    $params = [];
    
    // Search functionality
    if (!empty($_GET['search'])) {
        $searchTerm = '%' . $_GET['search'] . '%';
        $sql .= " AND (
            property_number LIKE :search OR
            description LIKE :search OR
            model_number LIKE :search OR
            remarks LIKE :search OR
            person_accountable LIKE :search OR
            remarks LIKE :search
        )";
        $params[':search'] = $searchTerm;
    }
    
    // Sorting functionality
    $sortOption = $_GET['sort'] ?? 'date_desc';
    switch ($sortOption) {
        case 'date_asc':
            $sql .= " ORDER BY signature_of_inventory_team_date ASC";
            break;
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
        default: // date_desc
            $sql .= " ORDER BY STR_TO_DATE(signature_of_inventory_team_date, '%Y-%m-%d') DESC";
    }
    
    try {
        // Prepare and execute query
        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database error: " . htmlspecialchars($e->getMessage()));
    }
    ?>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Property Number</th>
                <th>Description</th>
                <th>Model Number</th>
                <th>Unit Value</th>
                <th>Acquisition Date</th>
                <th>Accountable Person</th>
                <th>remarks</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['property_number']) ?></td>
                    <td><?= htmlspecialchars($item['description']) ?></td>
                    <td><?= htmlspecialchars($item['model_number']) ?></td>
                    <td><?= htmlspecialchars($item['cost']) ?></td>
                    <td><?= htmlspecialchars($item['acquisition_date']) ?></td>
                    <td><?= htmlspecialchars($item['person_accountable']) ?></td>
                    <td><?= htmlspecialchars($item['remarks']) ?></td>
                    <td><?= htmlspecialchars($item['signature_of_inventory_team_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">No inventory items found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>