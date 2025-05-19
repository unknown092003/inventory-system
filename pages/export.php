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
                        <th>DATE SIGNED</th>
                        <th>COST</th>
                        <th>COST LEVEL</th>
                        <th>REMARKS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // === FIXED PHP MAPPING & LOGIC ===
                    try {
                        $conn = new PDO("mysql:host=localhost;dbname=inventory-system", 'root', '');
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $search = $_GET['search'] ?? '';
                        $sort = $_GET['sort'] ?? 'date_desc';

                        $sql = "SELECT * FROM inventory WHERE 1=1";
                        $params = [];

                        if (!empty($search)) {
                            $sql .= " AND (
                                property_number LIKE :search OR
                                description LIKE :search OR
                                model_number LIKE :search OR
                                remarks LIKE :search OR
                                person_accountable LIKE :search
                            )";
                            $params[':search'] = '%' . $search . '%';
                        }

                        $orderOptions = [
                            'date_asc' => 'signature_of_inventory_team_date ASC',
                            'property_asc' => 'property_number ASC',
                            'property_desc' => 'property_number DESC',
                            'person_asc' => 'person_accountable ASC',
                            'person_desc' => 'person_accountable DESC',
                            'date_desc' => 'STR_TO_DATE(signature_of_inventory_team_date, "%Y-%m-%d") DESC'
                        ];
                        $sql .= " ORDER BY " . ($orderOptions[$sort] ?? $orderOptions['date_desc']);

                        $stmt = $conn->prepare($sql);
                        foreach ($params as $k => $v) {
                            $stmt->bindValue($k, $v);
                        }
                        $stmt->execute();
                        $stmt->setFetchMode(PDO::FETCH_ASSOC);

                        while ($row = $stmt->fetch()) {
                            $cost = $row['cost'];
                            $costLevel = $cost >= 5000 ? 'HIGH' : 'LOW';
                            $costClass = $cost >= 5000 ? 'high-cost' : 'low-cost';

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['person_accountable']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['property_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['acquisition_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['model_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['signature_of_inventory_team_date']) . "</td>";
                            echo "<td class='numeric-cell " . $costClass . "'>" . number_format($cost, 2) . "</td>";
                            echo "<td>" . $costLevel . "</td>";
                            echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                            echo "</tr>";
                        }

                        if ($stmt->rowCount() === 0) {
                            echo "<tr><td colspan='10'>No inventory items found.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='10'>Connection failed: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/inventory-system/public/scripts/export.js"></script>
</body>
</html>
