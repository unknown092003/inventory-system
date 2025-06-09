<?php
// Test database connection and check equipment_type column
$db = new mysqli('localhost', 'root', '', 'inventory_system');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "Database connection successful\n";

// Check if equipment_type column exists
$result = $db->query("SHOW COLUMNS FROM inventory LIKE 'equipment_type'");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Column equipment_type exists: " . print_r($row, true) . "\n";
} else {
    echo "Column equipment_type does not exist\n";
}

// Check if any records have empty equipment_type
$result = $db->query("SELECT COUNT(*) as count FROM inventory WHERE equipment_type IS NULL OR equipment_type = ''");
$row = $result->fetch_assoc();
echo "Records with empty equipment_type: " . $row['count'] . "\n";

// Check if any records have non-empty equipment_type
$result = $db->query("SELECT COUNT(*) as count FROM inventory WHERE equipment_type IS NOT NULL AND equipment_type != ''");
$row = $result->fetch_assoc();
echo "Records with non-empty equipment_type: " . $row['count'] . "\n";

// Check database name
$result = $db->query("SELECT DATABASE()");
$row = $result->fetch_row();
echo "Current database: " . $row[0] . "\n";
?>