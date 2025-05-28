
<link rel="stylesheet" href="/inventory-system/public/styles/list.css">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">

<a href="landing.php">Home</a>

<?php
require dirname(__FILE__, 2) . "/vendor/autoload.php";

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=inventory_system", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");

try {
    // Get the specific property number from the request
    $property_number = isset($_GET['property_number']) ? $_GET['property_number'] : null;
    
    if (!$property_number) {
        echo "<p>Please specify a property number.</p>";
        exit;
    }

    // Get the specific inventory item
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = ?");
    $stmt->execute([$property_number]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo "<p>No inventory item found with property number: " . htmlspecialchars($property_number) . "</p>";
        exit;
    }

    // Set up QR code renderer
    $renderer = new ImageRenderer(
        new RendererStyle(150, 1),
        new ImagickImageBackEnd()
    );
    $writer = new Writer($renderer);

    // Template path
    $templatePath = dirname(__FILE__, 2) . '/templates/sticker-template.png';
    
    // Check if template exists
    if (!file_exists($templatePath)) {
        throw new Exception("Sticker template not found at: " . $templatePath);
    }

    echo "<div class='sticker-container'>";
    echo "<style>
        .sticker-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .sticker {
            border: 1px solid #ddd;
            padding: 10px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .sticker img {
            max-width: 100%;
            height: auto;
        }
    </style>";

    $outputPath = dirname(__FILE__, 2) . '/qr/' . $item["property_number"] . '.png';
    
    try {
        $im = new Imagick($templatePath);
        $draw = new ImagickDraw();

        // Add QR Code
        $qrCode = new Imagick();
        $qrCode->readImageBlob($writer->writeString($item["property_number"]));
        $qrCode->resizeImage(280, 280, Imagick::FILTER_LANCZOS, 1);
        $im->compositeImage($qrCode, Imagick::COMPOSITE_OVER, 15, 298);

        // Add Text - using the current data from database
        $draw->setFontSize(30);
        $draw->setFillColor('black');
        
        // Position adjustments for better alignment
        $im->annotateImage($draw, 600, 95, 0, $item["property_number"]);
        $im->annotateImage($draw, 600, 160, 0, $item["description"]);
        $im->annotateImage($draw, 600, 225, 0, $item["model_number"]);
        $im->annotateImage($draw, 600, 290, 0, $item["acquisition_date"]);
        $im->annotateImage($draw, 600, 350, 0, $item["cost"]);
        $im->annotateImage($draw, 600, 410, 0, $item["person_accountable"]);
        $im->annotateImage($draw, 600, 475, 0, $item["remarks"]);
        $im->annotateImage($draw, 600, 540, 0, $item["signature_of_inventory_team_date"]);

        // Save the output
        $im->writeImage($outputPath);
        $im->clear();
        $im->destroy();

        // Display the generated sticker
        echo '<div class="sticker">';
        echo '<img src="/inventory-system/qr/' . basename($outputPath) . '?t=' . time() . '" alt="Sticker for ' . $item["property_number"] . '">';
       
        echo '</div>';

    } catch (Exception $e) {
        echo "<p>Error generating sticker for " . $item["property_number"] . ": " . $e->getMessage() . "</p>";
    }

    echo "</div>";

} catch (PDOException $e) {
    echo "<p>Database error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?>