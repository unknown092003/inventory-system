<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/list.css">
    <title>Inventory Stickers</title>
    <style>
        input[type="text"],
            select {
                padding: 10px 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                font-size: 16px;
                outline: none;
                background-color: #f9f9f9;
                transition: border-color 0.3s ease, box-shadow 0.3s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            input[type="text"]:focus,
            select:focus {
                border-color: #007bff;
                box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
                background-color: #fff;
            }

            button {
                padding: 10px 20px;
                background-color: #007bff;
                color: white;
                font-size: 16px;
                font-weight: 500;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                transition: background-color 0.3s ease, transform 0.2s ease;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            button:hover {
                background-color: #0056b3;
                transform: translateY(-1px);
            }

            button:active {
                background-color: #004494;
                transform: scale(0.98);
            }

        /* SCREEN-SPECIFIC STYLES */
        .sticker-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .sticker img {
            width: 70%;
            margin-top: 3px;
        }
        
        .list-nav {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        .search-sort-container {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        
        @media print {
            nav, .list-nav, .navbar, header, footer {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="list-nav">
        <form method="GET" class="search-sort-container">
            <input type="hidden" name="page" value="list">
            <input type="text" name="search" placeholder="Search all fields..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <select name="equipment_type">
                <option value="">All Equipment Types</option>
                <option value="Machinery" <?= ($_GET['equipment_type'] ?? '') === 'Machinery' ? 'selected' : '' ?>>Machinery</option>
                <option value="Construction" <?= ($_GET['equipment_type'] ?? '') === 'Construction' ? 'selected' : '' ?>>Construction</option>
                <option value="ICT Equipment" <?= ($_GET['equipment_type'] ?? '') === 'ICT Equipment' ? 'selected' : '' ?>>ICT Equipment</option>
                <option value="Communications" <?= ($_GET['equipment_type'] ?? '') === 'Communications' ? 'selected' : '' ?>>Communications</option>
                <option value="Military/Security" <?= ($_GET['equipment_type'] ?? '') === 'Military/Security' ? 'selected' : '' ?>>Military/Security</option>
                <option value="Office" <?= ($_GET['equipment_type'] ?? '') === 'Office' ? 'selected' : '' ?>>Office</option>
                <option value="DRRM Equipment" <?= ($_GET['equipment_type'] ?? '') === 'DRRM Equipment' ? 'selected' : '' ?>>DRRM Equipment</option>
                <option value="Furniture" <?= ($_GET['equipment_type'] ?? '') === 'Furniture' ? 'selected' : '' ?>>Furniture</option>
            </select>
            <select name="month">
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
            <select name="year">
                <option value="">All Years</option>
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
                    $selected = (($_GET['year'] ?? '') == $y) ? 'selected' : '';
                    echo "<option value=\"$y\" $selected>$y</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
        <button onclick="printStickers()">Print</button>
    </div>
    
    <div class="sticker-preview" id="stickerContainer">
        <?php
        require_once __DIR__ . '/db.php';
        $pdo = Database::getInstance()->getConnection();
        
        require dirname(__FILE__, 2) . "/vendor/autoload.php";
        
        use BaconQrCode\Common\ErrorCorrectionLevel;
        use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
        use BaconQrCode\Renderer\ImageRenderer;
        use BaconQrCode\Renderer\RendererStyle\RendererStyle;
        use BaconQrCode\Writer;
        
        // Build query with filters
        $query = "SELECT * FROM inventory WHERE 1=1";
        $params = [];
        
        if (!empty($_GET['search'])) {
            $query .= " AND (property_number LIKE ? OR description LIKE ? OR model_number LIKE ? OR person_accountable LIKE ?)";
            $search = '%' . $_GET['search'] . '%';
            $params = array_merge($params, [$search, $search, $search, $search]);
        }
        
        if (!empty($_GET['equipment_type'])) {
            $query .= " AND equipment_type = ?";
            $params[] = $_GET['equipment_type'];
        }
        
        if (!empty($_GET['month']) && !empty($_GET['year'])) {
            $query .= " AND MONTH(acquisition_date) = ? AND YEAR(acquisition_date) = ?";
            $params[] = $_GET['month'];
            $params[] = $_GET['year'];
        } elseif (!empty($_GET['month'])) {
            $query .= " AND MONTH(acquisition_date) = ?";
            $params[] = $_GET['month'];
        } elseif (!empty($_GET['year'])) {
            $query .= " AND YEAR(acquisition_date) = ?";
            $params[] = $_GET['year'];
        }
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $renderer = new ImageRenderer(
            new RendererStyle(150, 1),
            new ImagickImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        
        // Create QR directory if it doesn't exist
        $qrDir = dirname(__FILE__, 2) . '/qr';
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }
        
        foreach ($res as $row) {
            // Sanitize property number for filename
            $safePropertyNumber = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row["property_number"]);
            $outputPath = dirname(__FILE__, 2) . '/qr/' . $safePropertyNumber . '.png';
            
            // Check if sticker already exists
            if (!file_exists($outputPath)) {
                // Only generate if it doesn't exist
                $templatePath = dirname(__FILE__, 2) . '/templates/sticker-template.png';
                
                $im = new Imagick($templatePath);
                $draw = new ImagickDraw();
            
                // Add QR Code
                $qrCode = new Imagick();
                $qrCode->readImageBlob($writer->writeString($row["property_number"]));
                $qrCode->resizeImage(280, 280, Imagick::FILTER_LANCZOS, 1);
                $im->compositeImage($qrCode, Imagick::COMPOSITE_OVER, 15, 298);
            
                // Add Text
                $draw->setFontSize(30);
                $draw->setFillColor('black');
                $im->annotateImage($draw, 600, 95, 0, $row["property_number"]);
                $im->annotateImage($draw, 600, 160, 0, $row["description"]);
                $im->annotateImage($draw, 600, 225, 0, $row["model_number"]);
                $im->annotateImage($draw, 600, 290, 0, $row["acquisition_date"]);
                $im->annotateImage($draw, 600, 350, 0, $row["cost"]);
                $im->annotateImage($draw, 600, 410, 0, $row["person_accountable"]);
                $im->annotateImage($draw, 600, 475, 0, $row["remarks"]);
                $im->annotateImage($draw, 600, 540, 0, $row["signature_of_inventory_team_date"]);
            
                // Save the output
                $im->writeImage($outputPath);
                $im->clear();
                $im->destroy();
            }
        }
        // Display the generated stickers on the webpage
        foreach ($res as $row) {
            $safePropertyNumber = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row["property_number"]);
            $outputPath = '/inventory-system/qr/' . $safePropertyNumber . '.png';
            echo '<div class="sticker" 
                  data-property="' . htmlspecialchars($row["property_number"]) . '" 
                  data-description="' . htmlspecialchars($row["description"]) . '" 
                  data-model="' . htmlspecialchars($row["model_number"]) . '"
                  data-date="' . htmlspecialchars($row["acquisition_date"]) . '"
                  data-cost="' . htmlspecialchars($row["cost"]) . '"
                  data-accountable="' . htmlspecialchars($row["person_accountable"]) . '"
                  data-remarks="' . htmlspecialchars($row["remarks"]) . '"
                  data-equipment="' . htmlspecialchars($row["equipment_type"] ?? '') . '">';
            echo '<img src="' . $outputPath . '" alt="Sticker for ' . htmlspecialchars($row["property_number"]) . '" width="70%">';
            echo '</div>';
        }
        ?>
    </div>
    
    <script>
    function printStickers() {
        // Get all visible sticker HTML
        const stickers = document.querySelectorAll('.sticker:not([style*="display: none"])');
        let stickersHTML = '';
        
        // Build HTML for each sticker with proper print dimensions
        stickers.forEach(sticker => {
            stickersHTML += `
                <div class="print-sticker">
                    ${sticker.innerHTML.replace('width="70%"', 'width="100%" height="100%"')}
                </div>
            `;
        });
        
        // Create print window
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Print Stickers</title>
                <style>
                    @page {
                        size: letter; /* Standard 8.5" x 11" paper */
                        margin: 0.25in; /* Reduced margins for more space */
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                    .sticker-sheet {
                        display: grid;
                        grid-template-columns: repeat(2, 4in); /* 2 columns of 4" stickers */
                        grid-auto-rows: 2in; /* Each row 2" tall */
                        gap: 0.1in; /* Small gap between stickers */
                        width: 8.5in; /* Full page width */
                        height: 11in; /* Full page height */
                        padding: 0.1in;
                        box-sizing: border-box;
                    }
                    .print-sticker {
                        width: 4in;
                        height: 2in;
                        overflow: hidden;
                        page-break-inside: avoid;
                        border: 1px dashed #ccc; /* Optional: visual guide for cutting */
                    }
                    .print-sticker img {
                        width: 100% !important;
                        height: 100% !important;
                        object-fit: contain;
                        margin: 0 !important;
                    }
                </style>
            </head>
            <body>
                <div class="sticker-sheet">${stickersHTML}</div>
            </body>
            </html>
        `);
        printWindow.document.close();
        
        printWindow.onload = function() {
            printWindow.print();
        };
    }
    
    function filterStickers() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const equipmentType = document.getElementById('sortSelect').value;
        const stickers = document.querySelectorAll('.sticker');
        
        stickers.forEach(sticker => {
            // Check equipment type filter first
            const matchesEquipment = equipmentType === 'all' || 
                                   sticker.getAttribute('data-equipment') === equipmentType;
            
            // If equipment type doesn't match, hide immediately
            if (!matchesEquipment) {
                sticker.style.display = 'none';
                return;
            }
            
            // If we get here, equipment type matches - now check search term
            let found = false;
            if (searchTerm === '') {
                found = true;
            } else {
                // Check all data attributes for a match
                const attributes = sticker.attributes;
                for (let i = 0; i < attributes.length; i++) {
                    if (attributes[i].name.startsWith('data-')) {
                        const value = attributes[i].value.toLowerCase();
                        if (value.includes(searchTerm)) {
                            found = true;
                            break;
                        }
                    }
                }
            }
            
            sticker.style.display = found ? '' : 'none';
        });
    }
    
    function filterByEquipmentType() {
        // This will trigger the combined filter
        filterStickers();
    }
    </script>
</body>
</html>