<?php
require dirname(__FILE__, 2) . "/vendor/autoload.php";
require_once __DIR__ . '/../pages/db.php';

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

function generateSticker($propertyNumber) {
    global $db;
    
    // Always get the PDO connection
    $db = Database::getInstance()->getConnection();

    // Debug log to check property number
    error_log("Generating sticker for property number: " . $propertyNumber);

    // Prepare and execute query using PDO
    $stmt = $db->prepare("SELECT * FROM inventory WHERE property_number = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . implode(' | ', $db->errorInfo()));
        return false;
    }

    if (!$stmt->execute([$propertyNumber])) {
        error_log("Execute failed: " . implode(' | ', $stmt->errorInfo()));
        return false;
    }

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Debug log to check if equipment_type is present in the record
    if ($row) {
        error_log("Found record with equipment_type: " . ($row['equipment_type'] ?? 'NOT SET'));
    }

    if (!$row) {
        error_log("Record not found for property number: " . $propertyNumber);
        return false; // Record not found
    }
    
    // Create QR directory if it doesn't exist
    $qrDir = dirname(__FILE__, 2) . '/qr';
    if (!file_exists($qrDir)) {
        mkdir($qrDir, 0755, true);
    }

    $renderer = new ImageRenderer(
        new RendererStyle(150, 1),
        new ImagickImageBackEnd()
    );

    $writer = new Writer($renderer);
    $templatePath = dirname(__FILE__, 2) . '/templates/sticker-template.png';
    $outputPath = dirname(__FILE__, 2) . '/qr/' . $row["property_number"] . '.png';

    try {
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
        $im->annotateImage($draw, 600, 160, 0, $row["article"]);
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
        
        error_log("Sticker generated successfully for: " . $propertyNumber);
        return true;
    } catch (Exception $e) {
        error_log("Sticker generation failed: " . $e->getMessage());
        return false;
    }
}
?>