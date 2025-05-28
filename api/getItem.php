<?php
// getItem.php
header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=localhost;dbname=inventory_system", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $propertyNumber = $_GET['property_number'] ?? '';
    
    if (empty($propertyNumber)) {
        echo json_encode(['error' => 'Property number is required']);
        exit;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = :property_number");
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