<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/data.css">
</head> 
<body>
    <h1>Inventory Database</h1>

    <?php
    $pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");

    // Prepare and execute the query to fetch all data
    $sql = "SELECT * FROM inventory";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <table class="inventory-table">
        <thead>
            <tr>
                <th>Property Number</th>
                <th>Description</th>
                <th>Model Number</th>
                <th>Serial Number</th>
                <th>Acquisition Date/Cost</th>
                <th>Accountable Person</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['property_number']) ?></td>
                <td><?= htmlspecialchars($item['description']) ?></td>
                <td><?= htmlspecialchars($item['model_number']) ?></td>
                <td><?= htmlspecialchars($item['serial_number']) ?></td>
                <td><?= htmlspecialchars($item['acquisition_date_cost']) ?></td>
                <td><?= htmlspecialchars($item['person_accountable']) ?></td>
                <td><?= htmlspecialchars($item['status']) ?></td>
                <td><?= htmlspecialchars($item['signature_of_inventory_team_date']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
