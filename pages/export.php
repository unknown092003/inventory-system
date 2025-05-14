<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/export.css">
    <title>Inventory Management System</title>
   
</head>
<body>
    <div class="header-group">
        <h1>Inventory Management System</h1>
    </div>
    
    <div class="inventory-container">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Property Number</th>
                    <th>Description</th>
                    <th>Model Number</th>
                    <th>Acquisition Date</th>
                    <th>Accountable Person</th>
                    <th>Inventory Date</th>
                    <th>Cost</th>
                    <th>Cost Level</th>
                    <th>Remarks</th>
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