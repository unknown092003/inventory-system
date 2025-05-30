<?php
require dirname(__FILE__, 2) . "/vendor/autoload.php";

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

function generateSticker($propertyNumber) {
    $pdo = new PDO("mysql:host=localhost;dbname=inventory_system", "root", "");
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = ?");
    $stmt->execute([$propertyNumber]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false; // Record not found
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

        return true;
    } catch (Exception $e) {
        error_log("Sticker generation failed: " . $e->getMessage());
        return false;
    }
}
?>