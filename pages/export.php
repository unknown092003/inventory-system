<?php
require_once __DIR__ . '/../api/config.php';
requireAuth();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and sanitize parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
$monthPicker = isset($_GET['monthPicker']) ? $_GET['monthPicker'] : '';
$valueSort = isset($_GET['value_sort']) ? $_GET['value_sort'] : '';

// Base query
$sql = "SELECT * FROM inventory WHERE 1=1";
$params = [];
$types = '';
// Add search filter if provided
if (!empty($search)) {
    $sql .= " AND (
        property_number LIKE ? OR
        description LIKE ? OR
        model_number LIKE ? OR
        remarks LIKE ? OR
        person_accountable LIKE ?
    )";
    $searchTerm = '%' . $search . '%';
    $params = array_fill(0, 5, $searchTerm);
    $types = str_repeat('s', count($params));
}
// Add month/year filter for acquisition_date if provided
if (!empty($monthPicker)) {
    // $monthPicker is in format YYYY-MM
    $sql .= " AND DATE_FORMAT(acquisition_date, '%Y-%m') = ?";
    $params[] = $monthPicker;
    $types .= 's';
}

// Add value_sort filter for cost level if provided
if (!empty($valueSort)) {
    if ($valueSort === 'high') {
        $sql .= " AND cost >= 5000";
} elseif ($valueSort === 'low') {
    $sql .= " AND cost < 5000";
}
}

// Validate and set sort order
$validSorts = [
    'date_asc', 'date_desc',
    'property_asc', 'property_desc',
    'person_asc', 'person_desc'
];
$sort = in_array($sort, $validSorts) ? $sort : 'date_desc';

$orderBy = [
    'date_asc' => 'signature_of_inventory_team_date ASC',
    'date_desc' => 'STR_TO_DATE(signature_of_inventory_team_date, "%Y-%m-%d") DESC',
    'property_asc' => 'property_number ASC',
    'property_desc' => 'property_number DESC',
    'person_asc' => 'person_accountable ASC',
    'person_desc' => 'person_accountable DESC'
];

$sql .= " ORDER BY " . $orderBy[$sort];

// Prepare and execute
$stmt = $db->prepare($sql);
if (!$stmt) {
    die("Error preparing query: " . $db->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Check if we got results
if ($result->num_rows === 0) {
    echo "<script>alert('No items found with current filters');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OCD Inventory Report</title>
    <link rel="stylesheet" href="/inventory-system/public/styles/export.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.min.js"></script>
    <style>
        /* General Styles */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f5f5;
    margin: 0;
    padding: 20px;
}

h1, h2 {
    color: #1622a7;
    margin: 0;
}

.controls {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
    margin-bottom: 20px;
}

button {
    padding: 10px 20px;
    background-color: #1622a7;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
    font-size: 14px;
}

button:hover {
    background-color: #0f187f;
}

.main-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.main-header img {
    width: 80px;
    height: 80px;
    object-fit: contain;
}

.main-header-text {
    text-align: center;
    flex-grow: 1;
}

.main-header-text h1 {
    font-size: 24px;
    font-weight: bold;
    margin: 5px 0;
}

.main-header-two {
    margin: 20px 0;
    text-align: center;
    font-size: 16px;
}

.main-header-two p {
    margin: 5px 0;
}

.main-header-two hr {
    width: 100%;
    margin: 15px 0;
    border: 0;
    border-top: 2px solid #1622a7;
}

/* Table Styles - Optimized for Export */
.inventory-container {
    width: 100%;
    overflow-x: auto;
    background-color: white;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.inventory-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    table-layout: fixed;
}

.inventory-table th,
.inventory-table td {
    padding: 8px 6px;
    border: 1px solid #ddd;
    text-align: left;
    vertical-align: middle;
    word-wrap: break-word;
}

/* Column Width Adjustments */
.inventory-table th:nth-child(1), /* Person Accountable */
.inventory-table td:nth-child(1) {
    width: 15%;
}

.inventory-table th:nth-child(2), /* Property Number */
.inventory-table td:nth-child(2) {
    width: 10%;
}

.inventory-table th:nth-child(3), /* Acquisition Date */
.inventory-table td:nth-child(3) {
    width: 8%;
    white-space: nowrap;
}

.inventory-table th:nth-child(4), /* Description */
.inventory-table td:nth-child(4) {
    width: 20%;
}

.inventory-table th:nth-child(5), /* Model Number */
.inventory-table td:nth-child(5) {
    width: 10%;
}

.inventory-table th:nth-child(6), /* ID */
.inventory-table td:nth-child(6) {
    width: 5%;
}

.inventory-table th:nth-child(7), /* Cost */
.inventory-table td:nth-child(7) {
    width: 10%;
}

.inventory-table th:nth-child(8), /* Cost Level */
.inventory-table td:nth-child(8) {
    width: 8%;
}

.inventory-table th:nth-child(9), /* Remarks */
.inventory-table td:nth-child(9) {
    width: 14%;
}

.inventory-table th {
    background-color: #1622a7;
    color: white;
    position: sticky;
    top: 0;
    z-index: 1;
    font-weight: normal;
}

.inventory-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Numeric Styling */
.numeric-cell {
    text-align: right;
    font-family: 'Courier New', monospace;
}

.high-cost {
    color: #d32f2f;
    font-weight: bold;
}

.low-cost {
    color: #388e3c;
}

/* Responsive Design */
@media screen and (max-width: 768px) {
    .main-header {
        flex-direction: column;
    }
    
    .main-header img {
        margin-bottom: 10px;
    }
    
    .inventory-table {
        font-size: 12px;
    }
    
    .inventory-table th, 
    .inventory-table td {
        padding: 6px 4px;
    }
}

/* Print Styles - Optimized for PDF/Word Export */
    /* Add padding to account for fixed nav */
    body {
        padding-top: 80px;
    }

    @media print {
        body {
            padding-top: 0;
        }
        .main-nav {
            display: none !important;
        }
    }

\@media print {
    body {
        font-size: 10pt;
        padding: 0;
        background: white;
    }
    
    .controls {
        display: none;
    }
    
    .inventory-table {
        page-break-inside: avoid;
    }
    
    .inventory-table th,
    .inventory-table td {
        padding: 4px 3px;
        font-size: 9pt;
    }
    
    .main-header img {
        width: 60px;
        height: 60px;
    }
    
    .main-header-text h1 {
        font-size: 14pt;
    }
    
    /* Ensure table doesn't break across pages */
    tr {
        page-break-inside: avoid;
    }
}

/* Additional Export-specific Styles */
.export-page {
    size: A4 landscape;
    margin: 0;
}

.export-table {
    font-size: 9pt;
}

@media print {
    body {
        width: 100%;
        margin: 0;
        padding: 0;
        font-size: 9pt;
    }
    
    .inventory-table {
        width: 100% !important;
        table-layout: auto;
    }
    
    .inventory-table td, 
    .inventory-table th {
        padding: 3px;
        font-size: 8pt;
        word-wrap: break-word;
        white-space: normal !important;
    }
    
    /* Specific column adjustments */
    .inventory-table td:nth-child(4), /* Description */
    .inventory-table td:nth-child(9) { /* Remarks */
        max-width: 150px;
        white-space: normal;
        word-break: break-word;
    }
}

    /* Navigation styles */
    .main-nav {
        background: #2c3e50;
        padding: 1rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 1000;
    }

    .nav-brand {
        color: white;
        font-size: 1.5rem;
        font-weight: bold;
    }

    .nav-links {
        display: flex;
        gap: 2rem;
    }

    .nav-link {
        color: white;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .nav-link:hover {
        background: #34495e;
    }

    .logout {
        background: #e74c3c;
    }

    .logout:hover {
        background: #c0392b;
    }

    /* For PDF export specifically */
.pdf-export {
    transform: scale(0.8);
    transform-origin: top left;
    width: 125%;
}
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="main-nav">
        <div class="nav-brand">Inventory System</div>
        <div class="nav-links">
            <div class="header-group">
                <div class="controls">
                    <!-- Sort/filter by acquisition date (month/year) -->
                    <form id="filterForm" method="get" style="display:inline; margin-right:10px;">
                        <label for="monthPicker">Select Month and Year:</label>
                        <input type="month" id="monthPicker" name="monthPicker" value="<?= htmlspecialchars($_GET['monthPicker'] ?? '') ?>">

                        <button type="submit" style="display:none;">Apply</button>
                    </form>
                    <script>
                        // Auto-submit on month change
                        document.getElementById('monthPicker').addEventListener('change', function() {
                            document.getElementById('filterForm').submit();
                        });
                    </script>

                    <!-- Value Sort -->
                    <select name="value_sort" class="sort-select" id="valueSort">
                        <option value="">Sort by Value</option>
                        <option value="high">High Value (≥₱5,000)</option>
                        <option value="low">Low Value (<₱5,000)</option>
                    </select>
                    <script>
                        // Set selected option based on URL param
                        (function() {
                            const params = new URLSearchParams(window.location.search);
                            const valueSort = params.get('value_sort') || '';
                            document.getElementById('valueSort').value = valueSort;
                        })();

                        // On change, submit form with value_sort param
                        document.getElementById('valueSort').addEventListener('change', function() {
                            const form = document.getElementById('filterForm');
                            let url = new URL(window.location.href);
                            let params = new URLSearchParams(url.search);

                            if (this.value) {
                                params.set('value_sort', this.value);
                            } else {
                                params.delete('value_sort');
                            }
                            // Keep monthPicker value
                            const monthPicker = document.getElementById('monthPicker').value;
                            if (monthPicker) params.set('monthPicker', monthPicker);

                            // Keep search and sort params if present
                            if (params.has('search')) params.set('search', params.get('search'));
                            if (params.has('sort')) params.set('sort', params.get('sort'));

                            window.location.search = params.toString();
                        });
                    </script>
                    <div class="export-dropdown">
                        <button id="exportDropdownBtn">Export ▼</button>
                        <div id="exportDropdownMenu" style="display:none; position:absolute; background:white; border:1px solid #ccc; border-radius:4px; min-width:140px; z-index:999;">
                            <button id="exportPdfBtn" style="width:100%; text-align:left; background:none; color:#1622a7;">Export to PDF</button>
                            <button id="exportBtn" style="width:100%; text-align:left; background:none; color:#1622a7;">Export to Excel</button>
                            <button id="exportWordBtn" style="width:100%; text-align:left; background:none; color:#1622a7;">Export to Word</button>
                        </div>
                    </div>
                    <button id="printBtn">Print</button>
                </div>
            </div>
        </div>
    </nav>
    <script>
        // Dropdown logic
        const exportBtn = document.getElementById('exportDropdownBtn');
        const exportMenu = document.getElementById('exportDropdownMenu');
        exportBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            exportMenu.style.display = exportMenu.style.display === 'block' ? 'none' : 'block';
        });
        // Hide dropdown when clicking outside
        document.addEventListener('click', function() {
            exportMenu.style.display = 'none';
        });
        // Prevent closing when clicking inside
        exportMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    </script>


    <div id="exportContent">
        <div class="main-header">
            <img src="/inventory-system/public/img/ocd.png" alt="OCD Logo" class="logo">
            <div class="main-header-text">
                <p>REPUBLIC OF THE PHILIPPINES</p>
                <p><strong>DEPARTMENT OF NATIONAL DEFENSE</strong></p>
                <h1>OFFICE OF CIVIL DEFENSE</h1>
                <h2>CORDILLERA ADMINISTRATIVE REGION</h2>
                <p>NO.55 FIRST ROAD, QUEZON HILL PROPER, BAGUIO CITY, 2600</p>
            </div>
            <img src="/inventory-system/public/img/bp.png" alt="Bagong Pilipinas Logo" class="logo">
        </div>

        <div class="main-header-two">
            <p><strong>REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT</strong></p>
            <p>Information, Communication and Technology Equipment (10605030)</p>
            <hr id="hr2">
        </div>

        <div class="inventory-container">
            <table class="inventory-table" id="inventoryTable">
                <thead>
                    <tr>
                        <th>PERSON ACCOUNTABLE</th>
                        <th>PROPERTY NUMBER</th>
                        <th>ACQUISITION DATE</th>
                        <th>DESCRIPTION</th>
                        <th>MODEL NUMBER</th>
                        <th>ID</th>
                        <th>COST</th>
                        <th>COST LEVEL</th>
                        <th>REMARKS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                        $cost = $row['cost'];
                        $costLevel = $cost >= 5000 ? 'HIGH' : 'LOW';
                        $costClass = $cost >= 5000 ? 'high-cost' : 'low-cost';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['person_accountable']) ?></td>
                            <td><?= htmlspecialchars($row['property_number']) ?></td>
                            <td><?= htmlspecialchars((string)($row['acquisition_date'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= htmlspecialchars($row['model_number']) ?></td>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td class='numeric-cell <?= $costClass ?>'><?= number_format($cost, 2) ?></td>
                            <td><?= $costLevel ?></td>
                            <td><?= htmlspecialchars($row['remarks']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/inventory-system/public/scripts/export.js"></script>
</body>
</html>