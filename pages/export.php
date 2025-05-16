<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/export.css">
    <title>Inventory Management System</title>
    <!-- Include SheetJS library for Excel export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <!-- PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <!-- Word -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html-docx-js@0.3.1/dist/html-docx.min.js"></script>

    <style>
        @media print {
            button {
                display: none;
            }
            body {
                zoom: 80%; /* Adjust this percentage to fit all columns */
            }
            .inventory-table {
                width: 100% !important;
                min-width: 100% !important;
            }
        }
        
        /* Ensure table fits all columns without scrolling */
        .inventory-container {
            width: 100%;
            overflow-x: visible;
        }
    </style>
</head>
<body>
    <div class="nav">
    <div class="header-group">
        <h1>Inventory Management System</h1>
        <div class="controls">
            <button id="exportPdfBtn">Export to PDF</button>
            <button id="exportBtn">Export to Excel</button>
            <button id="printBtn">Print Report</button>
            <button id="exportWordBtn">Export to Word</button>
        </div>
    </div>
    </div>
<div id="exportContent">
    <div class="mainheader">
    <div class="main-header">
        <img src="/inventory-system/public/img/ocd.png" alt="OCD Logo" class="logo">
        <div class="main-header-text">
            <p>REPUBLIC OF THE PHILIPPINES</p>
            <p><strong>DEPARTMENT OF NATIONAL DEFENSE</strong></p>
            <h1>OFFICE OF CIVIL DEFENSE</h1>
            <h2>CORDILLERA ADMINISTRATIVE REGION<hr></h2>
            <p>NO.55 FIRST ROAD, QUEZON HILL PROPER, BAGUIO CITY, 2600</p>
        </div>
        <img src="/inventory-system/public/img/bp.png" alt="Bagong Pilipinas">
    </div>
    <div class="main-header-two">
        <p><strong>REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT</p>
        <p>Information, Communication and Technology Equipment(10605030)</strong><hr id="hr2"></p>
    </div>
    </div>

    <div class="inventory-container">
    <table id="inventoryTable" class="inventory-table" style="width: auto;">
        <!-- table content -->
        <div class="inventory-container">
        <table id="inventoryTable" class="inventory-table">
            <thead>
                <tr>
                    <th>ARTICLE</th>
                    <th>DESCRIPTION</th>
                    <th>ACQUISITION DATE</th>
                    <th>NEW PROPERTY NUMBER</th>
                    <th>UNIT OF MEASURE</th>
                    <th>UNIT VALUE</th>
                    <th>QUANTITY Per PROPERTY CARD</th>
                    <th>QUANTITY Per PHYSICAL COUNT</th>
                    <th>COST LEVEL</th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $host = 'localhost';
                $dbname = 'inventory-system';
                $username = 'root';
                $password = '';
                
                try {
                    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Get search and sort parameters from URL
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'date_desc';
                    
                    // Build the query with search and sort
                    $sql = "SELECT * FROM inventory WHERE 1=1";
                    $params = [];
                    
                    // Add search condition if search term exists
                    if (!empty($search)) {
                        $searchTerm = '%' . $search . '%';
                        $sql .= " AND (
                            property_number LIKE :search OR
                            description LIKE :search OR
                            model_number LIKE :search OR
                            remarks LIKE :search OR
                            person_accountable LIKE :search
                        )";
                        $params[':search'] = $searchTerm;
                    }
                    
                    // Add sorting based on parameter
                    switch ($sort) {
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
                    
                    // Prepare and execute query
                    $stmt = $conn->prepare($sql);
                    foreach ($params as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    $stmt->execute();
                    
                    // Set the fetch mode to associative array
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    
                    // Loop through each row
                    while($row = $stmt->fetch()) {
                        // Determine cost level
                        $cost = $row['cost'];
                        $costLevel = ($cost >= 5000) ? 'HIGH' : 'LOW';
                        $costClass = ($cost >= 5000) ? 'high-cost' : 'low-cost';
                        
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
                    
                    // Show message if no results found
                    if ($stmt->rowCount() === 0) {
                        echo "<tr><td colspan='10'>No inventory items found matching your search criteria</td></tr>";
                    }
                } catch(PDOException $e) {
                    echo "<tr><td colspan='10'>Connection failed: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    </table>
</div>
    
   
</div>
    
    <script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        const exportContent = document.getElementById('exportContent').cloneNode(true);
        const buttons = exportContent.querySelectorAll('button');
        buttons.forEach(button => button.remove());

        const tempDiv = document.createElement('div');
        tempDiv.style.textAlign = 'center'; // Center align the content
        tempDiv.appendChild(exportContent);
        document.body.appendChild(tempDiv);

        const wb = XLSX.utils.table_to_book(exportContent.querySelector('table'), {
            sheet: "Inventory",
            raw: true
        });

        const today = new Date();
        const dateString = today.getFullYear() + '-' + 
                          (today.getMonth()+1).toString().padStart(2, '0') + '-' + 
                          today.getDate().toString().padStart(2, '0');
        const filename = `OCD_Inventory_Report_${dateString}.xlsx`;

        XLSX.writeFile(wb, filename);
        document.body.removeChild(tempDiv);
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const element = document.getElementById('exportContent');
        const originalStyles = {
            bodyOverflow: document.body.style.overflow,
            bodyWidth: document.body.style.width,
            tableWidth: document.getElementById('inventoryTable').style.width,
            tableMargin: document.getElementById('inventoryTable').style.margin,
            tablePadding: document.getElementById('inventoryTable').style.padding
        };

        document.body.style.overflow = 'visible';
        document.body.style.width = '100% auto';
        const table = document.getElementById('inventoryTable');
        table.style.width = '100%';
        table.style.margin = '0 auto'; // Center the table
        table.style.padding = '0 0px';

        const opt = {
            margin: 0,
            filename: 'OCD_Inventory_Report.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { 
                scale: 1,
                scrollX: 0,
                scrollY: 0,
                width: element.scrollWidth,
                windowWidth: element.scrollWidth,
                useCORS: true,
                allowTaint: true
            },
            jsPDF: { 
                unit: 'mm',
                format: [297, 210],
                orientation: 'landscape'
            }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            document.body.style.overflow = originalStyles.bodyOverflow;
            document.body.style.width = originalStyles.bodyWidth;
            table.style.width = originalStyles.tableWidth;
            table.style.margin = originalStyles.tableMargin;
            table.style.padding = originalStyles.tablePadding;
        });
    });

    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });

    document.getElementById('exportWordBtn').addEventListener('click', function() {
        const content = document.getElementById('exportContent').cloneNode(true);
        const buttons = content.querySelectorAll('button');
        buttons.forEach(button => button.remove());

        const wrapper = document.createElement('div');
        wrapper.style.textAlign = 'center'; // Center align the content
        wrapper.appendChild(content);

        const converted = htmlDocx.asBlob(wrapper.innerHTML);
        saveAs(converted, 'OCD_Inventory_Report.docx');
    });
    </script>
</body>
</html>