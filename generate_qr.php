<?php
require_once __DIR__ . '/pages/db.php';
$pdo = Database::getInstance()->getConnection();

// Get item ID from URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid request");
}

// Fetch item data
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Code Sticker</title>
    <script src="/inventory-system/public/scripts/qr-generator.js"></script>
    <style>
        .sticker {
            width: 300px; 
            padding: 20px;
            border: 2px solid #000;
            text-align: center;
            background: white;
        }
        .qr-code {
            margin: 15px auto;
        }
        .sticker-header {
            font-size: 1.2em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="sticker">
        <div class="sticker-header">Inventory Item #<?= htmlspecialchars($item['property_number']) ?></div>
        
        <div class="qr-code" id="qrcode"></div>
        
        <div class="item-details">
            <p><strong>Article:</strong> <?= htmlspecialchars($item['article']) ?></p>
            <p><strong>Model:</strong> <?= htmlspecialchars($item['model_number']) ?></p>
            <p><strong>Serial:</strong> <?= htmlspecialchars($item['serial_number']) ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($item['status']) ?></p>
        </div>
    </div>

    <script>
        // Generate QR code with item data
        const qrData = `Inventory Item:
Property: <?= $item['property_number'] ?>
Article: <?= $item['article'] ?>
Model: <?= $item['model_number'] ?>
Serial: <?= $item['serial_number'] ?>
Last Updated: <?= (new Date()).toISOString().split('T')[0] ?>`;

        new QRCode(document.getElementById("qrcode"), {
            text: qrData,
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.correctLevel.H
        });
    </script>
</body>
</html>