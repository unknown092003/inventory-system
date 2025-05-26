<?php
require_once __DIR__ . '/../api/config.php';
requireAuth();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get and sanitize parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';

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
@media print {
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

/* For PDF export specifically */
.pdf-export {
    transform: scale(0.8);
    transform-origin: top left;
    width: 125%;
}
    </style>
</head>
<body>
    <div class="header-group">
        <div class="controls">
            <button id="exportPdfBtn">Export to PDF</button>
            <button id="exportBtn">Export to Excel</button>
            <button id="exportWordBtn">Export to Word</button>
            <button id="printBtn">Print</button>
        </div>
    </div>

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
                            <td><?= htmlspecialchars($row['acquisition_date']) ?></td>
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