<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/export.css">
    <title>Inventory Management System</title>
   
</head>
<body>
    <div class="main-header">
        <img src="/inventory-system/public/img/ocd.png" alt="OCD Logo" class="logo">
        <div class="main-header-text">
            <p>REPUBLIC OF THE PHILIPPINES</p>
            <p><strong>DEPARTMENT OF NATIONAL DEFENSE</strong></p>
            <h1>OFFICE OF CIVIL DEFENSE</h1>
            <h2>CORDILLERA ADMINISTRATIVE REGION<hr></h2>
            <p>NO.55 FIRST ROAD, QUEZON HILL PROPER, BAGUIO CITY, 2600</p>
        </div>
        <img src="/inventory-system/public/img/bp.png" alt="Bagong Pilipinas">
    </div>
    <div class="main-header-two">
        <p><strong>REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT</p>
        <p>Information, Communication adn Technology Equipment(10605030)</strong><hr id="hr2"></p>
        <h2></h2>
    </div>
    <div class="header-group">
        <h1>Inventory Management System</h1>
    </div>
    
    <div class="inventory-container">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ARTICLE</th>
                    <th>DESCRIPTION</th>
                    <th>ACQUISITION DATE</th>
                    <th>NEW PROPERTY NUMBER</th>
                    <th>UNIT OF MEASURE</th>
                    <th>UNIT VALUE</th>
                    <th>QUANTITY Per PROPERTY CARD</th>
                    <th>QUANTITY Per PHYSICAL COUNT</th>
                    <th></th>
                    <th>REMARKS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Database connection
                $host = 'localhost';
                $dbname = 'inventory-system';
                $username = 'root';
                $password = '';
                
                try {
                    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Query to fetch inventory data sorted by cost ascending
                    $stmt = $conn->prepare("SELECT * FROM inventory ORDER BY cost ASC");
                    $stmt->execute();
                    
                    // Set the fetch mode to associative array
                    $stmt->setFetchMode(PDO::FETCH_ASSOC);
                    
                    // Loop through each row
                    while($row = $stmt->fetch()) {
                        // Determine cost level
                        $cost = $row['cost'];
                        $costLevel = ($cost >= 5000) ? 'HIGH' : 'LOW';
                        $costClass = ($cost >= 5000) ? 'high-cost' : 'low-cost';
                        
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['property_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['model_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['acquisition_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['person_accountable']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['signature_of_inventory_team_date']) . "</td>";
                        echo "<td class='numeric-cell " . $costClass . "'>" . number_format($cost, 2) . "</td>";
                        echo "<td>" . $costLevel . "</td>";
                        echo "<td>" . htmlspecialchars($row['remarks']) . "</td>";
                        echo "</tr>";
                    }
                } catch(PDOException $e) {
                    echo "<tr><td colspan='10'>Connection failed: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>