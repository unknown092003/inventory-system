<?php
$pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
$stmt = $pdo->query("SELECT * FROM inventory LIMIT 100;");
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List</title>
</head>

<body>
    <main>
        <?php
        foreach ($res as $data) { ?>
            <div data-product-number="<?= $data["product_number"] ?>">
                <div id="<?= $data["product_number"] ?>"></div>
                <div>
                    <h4><?= $data["product_name"] ?></h4>
                </div>
            </div>
        <?php    }
        ?>
    </main>


    <script src="/inventory-system/public/scripts/qr-generator.js"></script>
    <script src="/inventory-system/public/scripts/list.js"></script>
</body>

</html>