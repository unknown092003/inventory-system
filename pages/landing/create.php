<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/create.css">
    <title>Create Inventory</title>
</head>
<body>
<div class="create">
    <h1>Create Area</h1>
    <form method="post" action="">
        <label>Property number:</label>
        <input type="text" name="property_number" required>

        <label>Description:</label>
        <input type="text" name="description" required>

        <label>Model number:</label>
        <input type="text" name="model_number">

        <label>Serial Number:</label>
        <input type="text" name="serial_number">

        <label>Acquisition date/cost:</label>
        <input type="text" name="acquisition_date_costs">

        <label>Person Accountable:</label>
        <input type="text" name="accountable_person" required>

        <label>Status:</label>
        <input type="text" name="status" required>

        <label>Signature of Inventory Team/Date:</label>
        <input type="date" name="inventory_date">

        <input type="submit" value="Submit">
    </form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection
    $host = 'localhost';
    $dbname = 'inventory-system';
    $username = 'root'; // Replace with your DB username
    $password = ''; // Replace with your DB password

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare SQL query matching your table structure
        $sql = "
            INSERT INTO inventory (
                property_number, 
                description, 
                model_number, 
                serial_number, 
                acquisition_date_cost, 
                person_accountable, 
                status, 
                signature_of_inventory_team_date
            ) VALUES (
                :property_number, 
                :description, 
                :model_number, 
                :serial_number, 
                :acquisition_date_costs, 
                :accountable_person, 
                :status, 
                :inventory_date
            )
        ";

        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':property_number' => $_POST['property_number'],
            ':description' => $_POST['description'],
            ':model_number' => $_POST['model_number'],
            ':serial_number' => $_POST['serial_number'],
            ':acquisition_date_costs' => $_POST['acquisition_date_costs'],
            ':accountable_person' => $_POST['accountable_person'],
            ':status' => $_POST['status'],
            ':inventory_date' => $_POST['inventory_date']
        ]);

        // Success message
        echo "<p>Record created successfully!</p>";
        
        // Log activity with prepared statement
        $lastInsertId = $conn->lastInsertId();
        $logStmt = $conn->prepare("INSERT INTO activity_log
            (action_type, table_name, record_id, user, description, timestamp)
            VALUES
            (:action_type, :table_name, :record_id, :user, :description, NOW())");
        
        $logStmt->execute([
            ':action_type' => 'create',
            ':table_name' => 'inventory',
            ':record_id' => $lastInsertId,
            ':user' => $_POST['accountable_person'],
            ':description' => 'Created new inventory item: ' . $_POST['description']
        ]);
        
        } catch (PDOException $e) {
        // Error handling
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

        // Close connection
        $conn = null;}
        ?>
</body>
</html>