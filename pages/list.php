<?php

require dirname(__FILE__, 2) . "/vendor/autoload.php";

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

$pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
$stmt = $pdo->query("SELECT * FROM inventory;");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

$renderer = new ImageRenderer(
    new RendererStyle(150, 1),
    new ImagickImageBackEnd()
);

$writer = new Writer($renderer);

foreach ($res as $row) {
    $writer->writeFile($row["product_number"], dirname(__FILE__, 2) . "/qr/" . $row["product_number"] . ".png");
}

?>

<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/list.css">
    <title>List</title>
</head>

<body>
    <main>
        <?php
        // foreach ($res as $data) { 
        ?>
            <div class="product-card" data-product-number="<?= $data["product_number"] ?>">
                <div id="<?= $data["product_number"] ?>">
                    <img src="data:image/png;base64, <?= base64_encode($writer->writeString($data["product_number"])) ?>" alt="">
                </div>
                <div>
                    <h4 title="<?= $data["product_name"] ?>"><?= $data["product_name"] ?></h4>
                </div>
                <button>Edit</button>
            </div>
        <?php   // }
        ?>
    </main> -->


<!-- <script src="/inventory-system/public/scripts/qr-generator.js"></script>
    <script src="/inventory-system/public/scripts/list.js"></script> -->
<!-- </body>

</html> -->