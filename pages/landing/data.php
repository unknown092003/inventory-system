<?php
/**
 * Inventory Management System - Data View
 * 
 * This script handles displaying inventory items with pagination, filtering, and sorting capabilities.
 */

// Include configuration file and authentication check
require_once __DIR__ . '/../../api/config.php';  // Database configuration
requireAuth();  // Ensure user is authenticated

// =============================================
// PAGINATION CONFIGURATION
// =============================================

// Number of items to display per page
$per_page = 20;

// Get current page from URL, default to 1
$current_page = max(1, intval($_GET['p'] ?? 1));

// Calculate offset for SQL query
$offset = ($current_page - 1) * $per_page;

// =============================================
// BASE QUERY CONSTRUCTION
// =============================================

// Base SQL query - selects all inventory fields we need
$base_query = "SELECT 
    property_number, description, model_number, equipment_type, 
    acquisition_date, cost, person_accountable, remarks,
    signature_of_inventory_team_date
    FROM inventory 
    WHERE 1=1";  // 1=1 allows easy addition of AND conditions

// Build dynamic WHERE conditions and parameters for prepared statements
$where_clauses = [];
$params = [];

// =============================================
// SEARCH FUNCTIONALITY
// =============================================

// Add search conditions if search parameter exists
if (!empty($_GET['search'])) {
    $search_term = $_GET['search'];
    $where_clauses[] = "(property_number LIKE ? OR description LIKE ? OR model_number LIKE ? OR person_accountable LIKE ? OR equipment_type LIKE ? OR remarks LIKE ?";
    $params = array_merge($params, array_fill(0, 6, "%$search_term%"));
    // Date search logic can be added here as needed
    $where_clauses[count($where_clauses)-1] .= ")";
}

// =============================================
// FILTERS
// =============================================

// Apply equipment type filter if selected
if (!empty($_GET['equipment_type'])) {
    $where_clauses[] = "equipment_type = ?";
    $params[] = $_GET['equipment_type'];
}

// Apply remarks filter if selected
if (!empty($_GET['remarks'])) {
    $where_clauses[] = "remarks = ?";
    $params[] = $_GET['remarks'];
}

// Apply month filter if selected
if (!empty($_GET['month'])) {
    $where_clauses[] = "MONTH(acquisition_date) = ?";
    $params[] = $_GET['month'];
}

// Apply year filter if selected
if (!empty($_GET['year'])) {
    $where_clauses[] = "YEAR(acquisition_date) = ?";
    $params[] = $_GET['year'];
}

// Apply value filter (high/low) if selected
if (!empty($_GET['value_sort'])) {
    if ($_GET['value_sort'] === 'high') {
        $where_clauses[] = "cost >= 5000";
    } elseif ($_GET['value_sort'] === 'low') {
        $where_clauses[] = "cost < 5000";
    }
}

// =============================================
// SORTING
// =============================================

// Default sorting by acquisition date (newest first)
$sort_condition = " ORDER BY acquisition_date DESC";

// Handle different sorting options from dropdown
if (!empty($_GET['sort'])) {
    switch ($_GET['sort']) {
        // Date sorting options
        case 'date_asc':
            $sort_condition = " ORDER BY acquisition_date ASC";
            break;
            
        // Property number sorting
        case 'property_asc':
            $sort_condition = " ORDER BY property_number ASC";
            break;
        case 'property_desc':
            $sort_condition = " ORDER BY property_number DESC";
            break;
            
        // Person accountable sorting
        case 'person_asc':
            $sort_condition = " ORDER BY person_accountable ASC";
            break;
        case 'person_desc':
            $sort_condition = " ORDER BY person_accountable DESC";
            break;
            
        // Equipment type filtering with date sorting
        case 'type_machinery':
            $where_clauses[] = "equipment_type = 'Machinery'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_construction':
            $where_clauses[] = "equipment_type = 'Construction'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_ict':
            $where_clauses[] = "equipment_type = 'ICT Equipment'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_communications':
            $where_clauses[] = "equipment_type = 'Communications'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_military':
            $where_clauses[] = "equipment_type = 'Military/Security'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_office':
            $where_clauses[] = "equipment_type = 'Office'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_drrm':
            $where_clauses[] = "equipment_type = 'DRRM'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
        case 'type_furniture':
            $where_clauses[] = "equipment_type = 'Furniture'";
            $sort_condition = " ORDER BY acquisition_date DESC";
            break;
            
        // Default sorting (newest first)
        case 'date_desc':
        default:
            $sort_condition = " ORDER BY acquisition_date DESC";
    }
}

// =============================================
// PAGINATION CALCULATIONS
// =============================================

// Combine WHERE clauses
$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = ' AND ' . implode(' AND ', $where_clauses);
}

// Get total count of items for pagination
$count_query = "SELECT COUNT(*) as total FROM inventory WHERE 1=1" . $where_sql;
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$count_data = $count_stmt->fetch(PDO::FETCH_ASSOC);
$total_items = $count_data['total'] ?? 0;  // Total matching items
$total_pages = max(1, ceil($total_items / $per_page));  // Calculate total pages needed

// =============================================
// DATA FETCHING
// =============================================

// Build and execute the main query with pagination
$query = $base_query . $where_sql . $sort_condition . " LIMIT $offset, $per_page";
$stmt = $db->prepare($query);
$stmt->execute($params);
$items = $stmt;

// =============================================
// URL PARAMETER MANAGEMENT
// =============================================

// Function to build URL with parameters while maintaining existing ones
function buildUrl($params = []) {
    $current_params = $_GET;
    unset($current_params['p']); // Remove pagination param
    $merged_params = array_merge($current_params, $params);
    return '?' . http_build_query($merged_params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Database</title>
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/data_main.css">
</head> 
<body class="data_body">
    <h1 class="inv-h1">Inventory Database</h1>
    
    <!-- Multi-Filter and Sort Controls -->
     <div class="page-wrapper">
    <div class="search-sort-container">
        <form method="GET" action="">
            <input type="hidden" name="page" value="data">

            <!-- Search Input -->
            <input type="text" name="search" class="search-box"
                   placeholder="Search property #, description, dates, etc..."
                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

            <!-- Equipment Type Filter -->
            <select name="equipment_type" class="sort-select">
                <option value="">All Types</option>
                <option value="Machinery" <?= ($_GET['equipment_type'] ?? '') === 'Machinery' ? 'selected' : '' ?>>Machinery</option>
                <option value="Construction" <?= ($_GET['equipment_type'] ?? '') === 'Construction' ? 'selected' : '' ?>>Construction</option>
                <option value="ICT Equipment" <?= ($_GET['equipment_type'] ?? '') === 'ICT Equipment' ? 'selected' : '' ?>>ICT Equipment</option>
                <option value="Communications" <?= ($_GET['equipment_type'] ?? '') === 'Communications' ? 'selected' : '' ?>>Communications</option>
                <option value="Military/Security" <?= ($_GET['equipment_type'] ?? '') === 'Military/Security' ? 'selected' : '' ?>>Military/Security</option>
                <option value="Office" <?= ($_GET['equipment_type'] ?? '') === 'Office' ? 'selected' : '' ?>>Office</option>
                <option value="DRRM" <?= (($_GET['equipment_type'] ?? '') === 'DRRM') ? 'selected' : '' ?>>DRRM</option>
                <option value="Furniture" <?= (($_GET['equipment_type'] ?? '') === 'Furniture') ? 'selected' : '' ?>>Furniture</option>
            </select>

            <!-- Remarks Filter -->
            <select name="remarks" class="sort-select">
                <option value="">All Status</option>
                <option value="standby" <?= ($_GET['remarks'] ?? '') === 'standby' ? 'selected' : '' ?>>Standby</option>
                <option value="service" <?= ($_GET['remarks'] ?? '') === 'service' ? 'selected' : '' ?>>Serviceable</option>
                <option value="unservice" <?= ($_GET['remarks'] ?? '') === 'unservice' ? 'selected' : '' ?>>Unserviceable</option>
                <option value="disposed" <?= ($_GET['remarks'] ?? '') === 'disposed' ? 'selected' : '' ?>>Disposed</option>
            </select>

            <!-- Month Filter -->
            <select name="month" class="sort-select">
                <option value="">All Months</option>
                <?php
                for ($m = 1; $m <= 12; $m++) {
                    $month_val = str_pad($m, 2, '0', STR_PAD_LEFT);
                    $month_name = date('F', mktime(0, 0, 0, $m, 10));
                    $selected = (($_GET['month'] ?? '') === $month_val) ? 'selected' : '';
                    echo "<option value=\"$month_val\" $selected>$month_name</option>";
                }
                ?>
            </select>

            <!-- Year Filter -->
            <select name="year" class="sort-select">
                <option value="">All Years</option>
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
                    $selected = (($_GET['year'] ?? '') == $y) ? 'selected' : '';
                    echo "<option value=\"$y\" $selected>$y</option>";
                }
                ?>
            </select>

            <!-- Value Sort -->
            <select name="value_sort" class="sort-select">
                <option value="">Sort by Value</option>
                <option value="high" <?= ($_GET['value_sort'] ?? '') === 'high' ? 'selected' : '' ?>>High Value (≥₱5,000)</option>
                <option value="low" <?= ($_GET['value_sort'] ?? '') === 'low' ? 'selected' : '' ?>>Low Value (<₱5,000)</option>
            </select>

            <button id="apply" type="submit">Apply</button>

            <!-- Clear Filters button (only shows when filters are active) -->
            <?php
            $filtersActive = !empty($_GET['search']) || !empty($_GET['equipment_type']) || !empty($_GET['remarks']) ||
                !empty($_GET['month']) || !empty($_GET['year']) || !empty($_GET['value_sort']) ||
                (isset($_GET['sort']) && $_GET['sort'] != 'date_desc');
            if ($filtersActive): ?>
                <a href="?page=data" class="button">Clear Filters</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Error Display -->
    <?php if (isset($_SESSION['error'])): ?>
        <div style="color: red; margin-bottom: 15px; padding: 10px; border: 1px solid red;">
            <?= $_SESSION['error'] ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Results Count -->
    <div style="margin-bottom: 15px; color: white;">
        Showing <?= $offset + 1 ?>-<?= min($offset + $per_page, $total_items) ?> of <?= $total_items ?> items
    </div>

<!-- Inventory Table -->
<div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0; padding: 10px 0;" class="table-container">
    <table border="1" cellpadding="6" style="border-collapse: collapse; white-space: nowrap; margin: 0 10px;">
        <thead>
            <tr>
                <th style="min-width: 100px;">Property #</th>
                <th style="min-width: 150px;">Description</th>
                <th style="min-width: 100px;">Model #</th>
                <th style="min-width: 120px;">Type</th>
                <th style="min-width: 90px;">Acquired</th>
                <th style="min-width: 90px;">Cost</th>
                <th style="min-width: 130px;">Accountable</th>
                <th style="min-width: 100px;">Remarks</th>
                <th style="min-width: 90px; padding-right: 20px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($items && $items->rowCount() > 0): ?>
                <?php while ($item = $items->fetch(PDO::FETCH_ASSOC)): 
                    $cost = number_format($item['cost'], 2);
                    $acquired = date('M j, Y', strtotime($item['acquisition_date']));
                    $updated = $item['signature_of_inventory_team_date'] ? 
                        date('M j, Y', strtotime($item['signature_of_inventory_team_date'])) : 'N/A';
                    
                    $status_class = [
                        'standby' => 'status-standby',
                        'service' => 'status-service',
                        'unservice' => 'status-unservice',
                        'disposed' => 'status-disposed'
                    ][$item['remarks']] ?? '';
                ?>
                <tr>
                    <td style="min-width: 100px;"><?= htmlspecialchars($item['property_number']) ?></td>
                    <td style="min-width: 150px;"><?= htmlspecialchars($item['description']) ?></td>
                    <td style="min-width: 100px;"><?= htmlspecialchars($item['model_number'] ?? 'N/A') ?></td>
                    <td style="min-width: 120px;"><?= htmlspecialchars($item['equipment_type'] ?? 'Not Specified') ?></td>
                    <td style="min-width: 90px;"><?= $acquired ?></td>
                    <td style="text-align: right; min-width: 90px;">₱<?= $cost ?></td>
                    <td style="min-width: 130px;"><?= htmlspecialchars($item['person_accountable'] ?? 'N/A') ?></td>
                    <td style="min-width: 100px;">
                        <span class="status-badge <?= $status_class ?>">
                            <?= ucfirst($item['remarks']) ?>
                        </span>
                    </td>
                    <td style="min-width: 90px; padding-right: 20px;">
                        <a href="edit.php?property_number=<?= urlencode($item['property_number']) ?>" 
                           style="padding: 4px 8px; background: #4CAF50; color: white; text-decoration: none; border-radius: 3px; display: inline-block;">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 15px;">No inventory items found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    <!-- ============================================= -->
    <!-- PAGINATION CONTROLS -->
    <!-- ============================================= -->
    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="<?= buildUrl(['p' => 1]) ?>">&laquo; First</a>
            <a href="<?= buildUrl(['p' => $current_page - 1]) ?>">&lsaquo; Previous</a>
        <?php else: ?>
            <span class="disabled">&laquo; First</span>
            <span class="disabled">&lsaquo; Previous</span>
        <?php endif; ?>

        <?php
        // Show page numbers (with ellipsis for many pages)
        $max_pages_to_show = 5;
        $start_page = max(1, $current_page - floor($max_pages_to_show / 2));
        $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);
        
        // Adjust if we're at the end
        if ($end_page - $start_page < $max_pages_to_show - 1) {
            $start_page = max(1, $end_page - $max_pages_to_show + 1);
        }
        
        // Show first page + ellipsis if needed
        if ($start_page > 1) {
            echo '<a href="' . buildUrl(['p' => 1]) . '">1</a>';
            if ($start_page > 2) {
                echo '<span>...</span>';
            }
        }
        
        // Show page numbers
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i == $current_page) {
                echo '<span class="current">' . $i . '</span>';
            } else {
                echo '<a href="' . buildUrl(['p' => $i]) . '">' . $i . '</a>';
            }
        }
        
        // Show last page + ellipsis if needed
        if ($end_page < $total_pages) {
            if ($end_page < $total_pages - 1) {
                echo '<span>...</span>';
            }
            echo '<a href="' . buildUrl(['p' => $total_pages]) . '">' . $total_pages . '</a>';
        }
        ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="<?= buildUrl(['p' => $current_page + 1]) ?>">Next &rsaquo;</a>
            <a href="<?= buildUrl(['p' => $total_pages]) ?>">Last &raquo;</a>
        <?php else: ?>
            <span class="disabled">Next &rsaquo;</span>
            <span class="disabled">Last &raquo;</span>
        <?php endif; ?>
    </div>
</body>
</html>