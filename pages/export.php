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
        
        /* .inventory-table {
            width: auto;
            min-width: 100%;
            table-layout: auto;
        }
         */
        /* Force columns to show all content */
        /* .inventory-table td, .inventory-table th {
            white-space: nowrap;
            overflow: visible;
            text-overflow: clip;
        } */
    </style>
</head>
<body>
<div id="exportContent">
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
                    
                    // Query to fetch inventory data sorted by cost ascending
                    $stmt = $conn->prepare("SELECT * FROM inventory ORDER BY cost ASC");
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
                } catch(PDOException $e) {
                    echo "<tr><td colspan='10'>Connection failed: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
    
    <div class="header-group">
        <h1>Inventory Management System</h1>
        <div class="controls">
            <button id="exportPdfBtn">Export to PDF</button>
            <button id="exportBtn">Export to Excel</button>
            <button id="printBtn">Print Report</button>
            <button id="exportWordBtn">Export to Word</button>
        </div>
    </div>
    
    <script>
    document.getElementById('exportBtn').addEventListener('click', function() {
        // Create a clone of the export content
        const exportContent = document.getElementById('exportContent').cloneNode(true);
        
        // Remove any buttons that might be inside
        const buttons = exportContent.querySelectorAll('button');
        buttons.forEach(button => button.remove());
        
        // Create a temporary div for export
        const tempDiv = document.createElement('div');
        tempDiv.appendChild(exportContent);
        document.body.appendChild(tempDiv);
        
        // Convert to Excel
        const wb = XLSX.utils.table_to_book(exportContent.querySelector('table'), {
            sheet: "Inventory",
            raw: true
        });
        
        // Generate filename with current date
        const today = new Date();
        const dateString = today.getFullYear() + '-' + 
                          (today.getMonth()+1).toString().padStart(2, '0') + '-' + 
                          today.getDate().toString().padStart(2, '0');
        const filename = `OCD_Inventory_Report_${dateString}.xlsx`;
        
        // Export to Excel
        XLSX.writeFile(wb, filename);
        
        // Clean up
        document.body.removeChild(tempDiv);
    });

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const element = document.getElementById('exportContent');
        const opt = {
            margin: 0.3,
            filename: 'OCD_Inventory_Report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                scrollX: 0,
                scrollY: 0,
                width: document.getElementById('exportContent').scrollWidth, // Ensure full width is captured
                windowWidth: document.getElementById('exportContent').scrollWidth
            },
            jsPDF: { 
                unit: 'px', // Use pixels for better precision
                format: [document.getElementById('exportContent').scrollWidth, 842], // Dynamically set width and height
                orientation: 'landscape' // Use landscape for wider tables
            }
        };
        
        // Temporarily adjust body overflow for PDF generation
        const originalOverflow = document.body.style.overflow;
        document.body.style.overflow = 'visible';
        
        html2pdf().set(opt).from(element).save().then(() => {
            document.body.style.overflow = originalOverflow;
        });
    });

    document.getElementById('printBtn').addEventListener('click', function() {
        window.print();
    });

    document.getElementById('exportWordBtn').addEventListener('click', function() {
        const content = document.getElementById('exportContent').cloneNode(true);
        
        // Remove any buttons from the content
        const buttons = content.querySelectorAll('button');
        buttons.forEach(button => button.remove());
        
        // Convert to Word
        const converted = htmlDocx.asBlob(content.innerHTML);
        saveAs(converted, 'OCD_Inventory_Report.docx');
    });
    </script>
</body>
</html>