<?php
require_once __DIR__ . '/pages/db.php';
// Test database connection and check equipment_type column
$db = Database::getInstance()->getConnection();

try {
    $result = $db->query("SHOW COLUMNS FROM equipment LIKE 'equipment_type'");
    if ($result && $result->rowCount() > 0) {
        echo "equipment_type column exists.\n";
    } else {
        echo "equipment_type column does not exist.\n";
}
echo "Database connection successful\n";
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// Check if any records have empty equipment_type
$result = $db->query("SELECT COUNT(*) as count FROM inventory WHERE equipment_type IS NULL OR equipment_type = ''");
$row = $result->fetch(PDO::FETCH_ASSOC);
echo "Records with empty equipment_type: " . $row['count'] . "\n";

// Check if any records have non-empty equipment_type
$result = $db->query("SELECT COUNT(*) as count FROM inventory WHERE equipment_type IS NOT NULL AND equipment_type != ''");
$row = $result->fetch(PDO::FETCH_ASSOC);
echo "Records with non-empty equipment_type: " . $row['count'] . "\n";

// Check database name
$result = $db->query("SELECT DATABASE()");
$row = $result->fetch(PDO::FETCH_NUM);
echo "Current database: " . $row[0] . "\n";
?>