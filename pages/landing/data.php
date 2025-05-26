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
            
            <?php if (isset($_GET['search']) || (isset($_GET['sort']) && $_GET['sort'] != 'date_desc')): ?>
                <a href="?page=data" class="button">Clear Filters</a>
            <?php endif; ?>
        </form>

        <button onclick="redirectToExport()">View Layout</button>
        <script>
            function redirectToExport() {
                // Get all current parameters EXCEPT 'page'
                const params = new URLSearchParams(window.location.search);
                params.delete('page'); // Remove pagination parameter
                
                // Convert to URL-encoded string
                const queryString = params.toString();
                
                // Redirect with all current filters
                window.location.href = `export.php?${queryString}`;
            }
        </script>
    </div>

    <?php
    require_once __DIR__ . '/../../api/config.php';
    requireAuth();

    // Initialize pagination variables
    $per_page = 20;
    $page = max(1, intval($_GET['page'] ?? 1));
    $offset = ($page - 1) * $per_page;

    // Build the base query
    $base_query = "SELECT 
        property_number, description, model_number, equipment_type, 
        acquisition_date, cost, person_accountable, remarks,
        signature_of_inventory_team_date
        FROM inventory 
        WHERE 1=1";

    // Add search conditions
    $search_condition = "";
    if (!empty($_GET['search'])) {
        $search = $db->real_escape_string($_GET['search']);
        $search_condition = " AND (property_number LIKE '%$search%' 
                   OR description LIKE '%$search%'
                   OR model_number LIKE '%$search%'
                   OR person_accountable LIKE '%$search%')";
    }

    // Add sorting
    $sort_condition = " ORDER BY acquisition_date DESC";
    if (!empty($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'date_asc':
                $sort_condition = " ORDER BY signature_of_inventory_team_date ASC";
                break;
            case 'property_asc':
                $sort_condition = " ORDER BY property_number ASC";
                break;
            case 'property_desc':
                $sort_condition = " ORDER BY property_number DESC";
                break;
            case 'person_asc':
                $sort_condition = " ORDER BY person_accountable ASC";
                break;
            case 'person_desc':
                $sort_condition = " ORDER BY person_accountable DESC";
                break;
            case 'date_desc':
            default:
                $sort_condition = " ORDER BY STR_TO_DATE(signature_of_inventory_team_date, '%Y-%m-%d') DESC";
        }
    }

    // Get total count for pagination
    $count_query = "SELECT COUNT(*) as total FROM inventory WHERE 1=1" . $search_condition;
    $count_result = $db->query($count_query);

    if ($count_result) {
        $count_data = $count_result->fetch_assoc();
        $total_items = $count_data['total'] ?? 0;
        $total_pages = max(1, ceil($total_items / $per_page));
    } else {
        $total_items = 0;
        $total_pages = 1;
        $_SESSION['error'] = "Error counting inventory items: " . $db->error;
    }

    // Build and execute main query
    $query = $base_query . $search_condition . $sort_condition . " LIMIT $offset, $per_page";
    $items = $db->query($query);

    if (!$items) {
        $_SESSION['error'] = "Error loading inventory: " . $db->error;
        $items = [];
    }
    ?>

  

    <!-- Display any errors -->
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color: red; margin-bottom: 15px; padding: 10px; border: 1px solid red;">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <a href="/inventory-system/pages/landing/data.php">full Screen</a>
    <!-- Show results count -->
    <div style="margin-bottom: 15px;">
        Showing <?= $offset + 1 ?>-<?= min($offset + $per_page, $total_items) ?> of <?= $total_items ?> items
    </div>

    <!-- Pagination Top -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination" style="margin-bottom: 20px;">
        
        <?php if ($page > 1): ?>
            <a href="?page=1&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>" title="First">« First</a>
            <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>" title="Previous">‹ Previous</a>
        <?php endif; ?>
        
        <?php 
        $start_page = max(1, $page - 2);
        $end_page = min($total_pages, $page + 2);
        
        for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>" 
               style="<?= $i == $page ? 'font-weight:bold; background:#4CAF50; color:white;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>" title="Next">Next ›</a>
            <a href="?page=<?= $total_pages ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>" title="Last">Last »</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table border="1" cellpadding="8" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th>Property #</th>
                    <th>Description</th>
                    <th>Model #</th>
                    <th>Type</th>
                    <th>Acquired</th>
                    <th>Cost</th>
                    <th>Accountable</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($items && $items->num_rows > 0): ?>
                    <?php while ($item = $items->fetch_assoc()): 
                        $cost = number_format($item['cost'], 2);
                        $acquired = date('M j, Y', strtotime($item['acquisition_date']));
                        $updated = $item['signature_of_inventory_team_date'] ? 
                            date('M j, Y', strtotime($item['signature_of_inventory_team_date'])) : 'N/A';
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['property_number']) ?></td>
                        <td><?= htmlspecialchars($item['description']) ?></td>
                        <td><?= htmlspecialchars($item['model_number'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($item['equipment_type']) ?></td>
                        <td><?= $acquired ?></td>
                        <td style="text-align: right;">₱<?= $cost ?></td>
                        <td><?= htmlspecialchars($item['person_accountable'] ?? 'N/A') ?></td>
                        <td>
                            <?php 
                            $status_class = [
                                'service' => 'status-service',
                                'unservice' => 'status-unservice',
                                'disposed' => 'status-disposed'
                            ][$item['remarks']] ?? '';
                            ?>
                            <span class="status-badge <?= $status_class ?>">
                                <?= ucfirst($item['remarks']) ?>
                            </span>
                        </td>
                        <td><?= $updated ?></td>
                        <td>
                            <a href="edit.php?property_number=<?= urlencode($item['property_number']) ?>" 
                               style="padding: 4px 8px; background: #4CAF50; color: white; text-decoration: none; border-radius: 3px;">
                                Edit
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 20px;">No inventory items found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>