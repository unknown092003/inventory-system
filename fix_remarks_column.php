<?php
require_once __DIR__ . '/api/config.php';

// Check current column structure
$result = $db->query("SHOW COLUMNS FROM inventory LIKE 'remarks'");
$column = $result->fetch(PDO::FETCH_ASSOC);

echo "Current remarks column structure:<br>";
echo "Type: " . $column['Type'] . "<br><br>";

// If it's an ENUM, alter it to include standby
if (strpos($column['Type'], 'enum') !== false) {
    echo "Altering remarks column to include 'standby'...<br>";
    $db->query("ALTER TABLE inventory MODIFY COLUMN remarks ENUM('standby','service','unservice','disposed') DEFAULT 'standby'");
    
    if ($db->errorInfo()) {
        echo "Error: " . $db->errorInfo()[2] . "<br>";
    } else {
        echo "Successfully updated remarks column to include 'standby'<br>";
    }
} else {
    echo "Remarks column is already flexible (not ENUM)<br>";
}

// Check the updated structure
$result = $db->query("SHOW COLUMNS FROM inventory LIKE 'remarks'");
$column = $result->fetch(PDO::FETCH_ASSOC);
echo "<br>Updated remarks column structure:<br>";
echo "Type: " . $column['Type'] . "<br>";

echo "<br><a href='/inventory-system/pages/landing.php'>Return to Dashboard</a>";
?>