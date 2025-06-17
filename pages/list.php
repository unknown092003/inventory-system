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
        
        .sticker {
            width: 400px;
        }
        
        .sticker img {
            width: 70%;
            margin-top: 3px;
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
        <button onclick="printStickers()">Print</button>
    </div>
    
    <script>
    function printStickers() {
        // Get all sticker HTML
        const stickers = document.querySelectorAll('.sticker');
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
    </script>
    <?php
    require dirname(__FILE__, 2) . "/vendor/autoload.php";
    
    use BaconQrCode\Common\ErrorCorrectionLevel;
    use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
    use BaconQrCode\Renderer\ImageRenderer;
    use BaconQrCode\Renderer\RendererStyle\RendererStyle;
    use BaconQrCode\Writer;
    
    $pdo = new PDO("mysql:host=localhost;dbname=inventory_system", "root", "");
    $stmt = $pdo->query("SELECT * FROM inventory limit 10;");
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
    echo '<div class="sticker-preview">';
    foreach ($res as $row) {
        $outputPath = '/inventory-system/qr/' . $row["property_number"] . '.png';
        echo '<div class="sticker">';
        echo '<img src="' . $outputPath . '" alt="Sticker for ' . $row["property_number"] . '" width="70%">';
        echo '</div>';
    }
    echo '</div>';
    ?>     
</body>
</html>