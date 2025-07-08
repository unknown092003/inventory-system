<?php
// getItem.php
header('Content-Type: application/json');

require_once '../pages/db.php';

// Get database connection
$conn = Database::getInstance()->getConnection();

try {
    $propertyNumber = $_GET['property_number'] ?? '';

    if (empty($propertyNumber)) {
        echo json_encode(['error' => 'Property number is required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM inventory WHERE property_number = :property_number");
    $stmt->bindParam(':property_number', $propertyNumber);
    $stmt->execute();

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        echo json_encode(['error' => 'Item not found']);
        exit;
    }

    echo json_encode($item);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>