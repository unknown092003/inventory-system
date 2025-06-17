<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/list.css">
    <title>Inventory Stickers</title>
    <style>
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
        <div class="search-sort-container">
            <input type="text" id="searchInput" placeholder="Search all fields..." oninput="filterStickers()">
            <select id="sortSelect" onchange="filterByEquipmentType()">
                <option value="all">All Equipment Types</option>
                <option value="Machinery">Machinery</option>
                <option value="Construction">Construction</option>
                <option value="ICT Equipment">ICT Equipment</option>
                <option value="Communications">Communications</option>
                <option value="Military/Security">Military/Security</option>
                <option value="Office">Office</option>
                <option value="DRRM">DRRM</option>
                <option value="Furniture">Furniture</option>
            </select>
        </div>
        <button onclick="printStickers()">Print</button>
    </div>
    
    <div class="sticker-preview" id="stickerContainer">
        <?php
        require dirname(__FILE__, 2) . "/vendor/autoload.php";
        
        use BaconQrCode\Common\ErrorCorrectionLevel;
        use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
        use BaconQrCode\Renderer\ImageRenderer;
        use BaconQrCode\Renderer\RendererStyle\RendererStyle;
        use BaconQrCode\Writer;
        
        $pdo = new PDO("mysql:host=localhost;dbname=inventory_system", "root", "");
        $stmt = $pdo->query("SELECT * FROM inventory  ;");
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
            $outputPath = dirname(__FILE__, 2) . '/qr/' . $row["property_number"] . '.png';
            
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
            $outputPath = '/inventory-system/qr/' . $row["property_number"] . '.png';
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