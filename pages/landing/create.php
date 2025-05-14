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

        <label>Acquisition date:</label>
        <input type="date" name="acquisition_date">

        <label>Cost:</label>
        <input type="number" name="cost">

        <label>Person Accountable:</label>
        <input type="text" name="person_accountable" required>


        <label>Remarks:</label>
                <select name="remarks">
                    <option value="service">service</option>
                    <option value="unservice">unservice</option>
                    <option value="dispose">dispose</option>
                </select>

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
                acquisition_date, 
                person_accountable, 
                signature_of_inventory_team_date,
                cost,
                remarks
            ) VALUES (
                :property_number, 
                :description, 
                :model_number, 
                :acquisition_date,
                :cost, 
                :person_accountable, 
                :remarks, 
                :inventory_date
            )
        ";

        // Prepare and execute the query
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':property_number' => $_POST['property_number'],
            ':description' => $_POST['description'],
            ':model_number' => $_POST['model_number'],
            ':acquisition_date' => $_POST['acquisition_date'],
            ':cost' => $_POST['cost'],
            ':person_accountable' => $_POST['person_accountable'],
            ':remarks' => $_POST['remarks'],
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
            ':user' => $_POST['person_accountable'], // FIXED
            ':description' => $_POST['description']
        ]);
        
    } catch (PDOException $e) {
        // Error handling
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    // Close connection
    $conn = null;
}
?>

</html>