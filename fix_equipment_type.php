<?php
// Script to fix equipment_type values in the database
require_once __DIR__ . '/api/config.php';

// Check if the equipment_type column has the correct type
$result = $db->query("SHOW COLUMNS FROM inventory LIKE 'equipment_type'");
$column = $result->fetch_assoc();

// If the column is an enum with limited values, we need to alter it
if (strpos($column['Type'], 'enum') !== false) {
    echo "Altering equipment_type column to VARCHAR(50)...<br>";
    $db->query("ALTER TABLE inventory MODIFY COLUMN equipment_type VARCHAR(50)");
    
    if ($db->error) {
        die("Error altering column: " . $db->error);
    }
    echo "Column altered successfully.<br>";
}

// Update all records with empty equipment_type to a default value
if (isset($_GET['type']) && !empty($_GET['type'])) {
    $type = $db->real_escape_string($_GET['type']);
    
    $db->query("UPDATE inventory SET equipment_type = '$type' WHERE equipment_type IS NULL OR equipment_type = ''");
    
    if ($db->error) {
        die("Error updating records: " . $db->error);
    }
    
    $affected = $db->affected_rows;
    echo "Updated $affected records with equipment_type: $type<br>";
    echo "<a href='/inventory-system/pages/landing.php'>Return to Dashboard</a>";
} else {
    // Show form to select equipment type
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Fix Equipment Types</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background-color: #f5f5f5;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            h1 {
                color: #333;
            }
            .form-group {
                margin-bottom: 15px;
            }
            label {
                display: block;
                margin-bottom: 5px;
                font-weight: bold;
            }
            select {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
                box-sizing: border-box;
            }
            button {
                background-color: #4CAF50;
                color: white;
                padding: 10px 15px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            button:hover {
                background-color: #45a049;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Fix Equipment Types</h1>
            
            <?php
            // Count records with empty equipment_type
            $result = $db->query("SELECT COUNT(*) as count FROM inventory WHERE equipment_type IS NULL OR equipment_type = ''");
            $row = $result->fetch_assoc();
            $empty_count = $row['count'];
            
            if ($empty_count > 0) {
                echo "<p>There are $empty_count records with empty equipment type.</p>";
                ?>
                <form method="GET">
                    <div class="form-group">
                        <label for="type">Select Equipment Type:</label>
                        <select id="type" name="type" required>
                            <option value="">-- Select Type --</option>
                            <option value="Machinery">Machinery</option>
                            <option value="Construction">Construction</option>
                            <option value="ICT Equipment">ICT Equipment</option>
                            <option value="Communications">Communications</option>
                            <option value="Military/Security">Military/Security</option>
                            <option value="Office">Office</option>
                            <option value="DRRM Equipment">DRRM Equipment</option>
                            <option value="Furniture">Furniture</option>
                        </select>
                    </div>
                    
                    <button type="submit">Update Records</button>
                </form>
                <?php
            } else {
                echo "<p>All records have equipment types assigned.</p>";
            }
            ?>
            
            <p><a href="/inventory-system/pages/landing.php">← Back to Dashboard</a></p>
        </div>
    </body>
    </html>
    <?php
}
?>