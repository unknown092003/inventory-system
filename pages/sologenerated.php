<a href="landing.php">Home</a>
<?php
// echo '<link rel="stylesheet" href="/inventory-system/public/styles/list.css">';
require dirname(__FILE__, 2) . "/vendor/autoload.php";

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

$pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
$stmt = $pdo->query("SELECT inventory.*, MAX(activity_log.timestamp) as last_updated
                    FROM inventory
                    LEFT JOIN activity_log ON inventory.id = activity_log.record_id
                    GROUP BY inventory.id
                    ORDER BY last_updated DESC");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

$renderer = new ImageRenderer(
    new RendererStyle(150, 1),
    new ImagickImageBackEnd()
);

$writer = new Writer($renderer);

if (!empty($res)) {
    $row = $res[0]; // Get the first record
    $templatePath = dirname(__FILE__, 2) . '/templates/sticker-template.png';
    $outputPath = dirname(__FILE__, 2) . '/qr/' . $row["property_number"] . '.png';

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
    $im->annotateImage($draw, 590, 350, 0, $row["acquisition_date"]);
    $im->annotateImage($draw, 590, 410, 0, $row["cost"]);
    $im->annotateImage($draw, 590, 350, 0, $row["acquisition_date"]);
    $im->annotateImage($draw, 600, 410, 0, $row["person_accountable"]);
    $im->annotateImage($draw, 600, 475, 0, $row["status"]);
    $im->annotateImage($draw, 600, 535, 0, $row["signature_of_inventory_team_date"]);

    // Save the output
    $im->writeImage($outputPath);
    $im->clear();
    $im->destroy();

    // Display the generated sticker on the webpage
    echo '<div class="sticker-preview">
    <style>img{margin-top: 10px};</style>
    ';
    $outputPath = '/inventory-system/qr/' . $row["property_number"] . '.png';
    echo '<div class="sticker">';
    echo '<img src="' . $outputPath . '" alt="Sticker for ' . $row["property_number"] . '" width="70%">';
    echo '</div>';
    echo '</div>';
}
?>
