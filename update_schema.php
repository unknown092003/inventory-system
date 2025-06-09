<?php
require_once __DIR__ . '/api/config.php';

// Check if the equipment_type column has the correct type
$result = $db->query("SHOW COLUMNS FROM inventory LIKE 'equipment_type'");
$column = $result->fetch_assoc();

// If the column is an enum, alter it to VARCHAR
if (strpos($column['Type'], 'enum') !== false) {
    echo "Altering equipment_type column to VARCHAR(50)...<br>";
    $db->query("ALTER TABLE inventory MODIFY COLUMN equipment_type VARCHAR(50)");
    
    if ($db->error) {
        die("Error altering column: " . $db->error);
    }
    echo "Column altered successfully. The system will now correctly store equipment types.<br>";
    echo "<a href='/inventory-system/pages/landing.php'>Return to Dashboard</a>";
} else {
    echo "The equipment_type column is already correctly configured.<br>";
    echo "<a href='/inventory-system/pages/landing.php'>Return to Dashboard</a>";
}
?>