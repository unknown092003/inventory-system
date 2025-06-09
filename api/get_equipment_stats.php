<?php
require_once dirname(__FILE__, 2) . '/api/config.php';

// Get equipment type from request
$equipment_type = $_GET['type'] ?? '';

if (empty($equipment_type)) {
    echo json_encode(['error' => 'Equipment type is required']);
    exit;
}

try {
    // Get total count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as total, SUM(cost) as total_cost FROM inventory WHERE equipment_type = ?");
    $stmt->bind_param("s", $equipment_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $total = $data['total'];
    $total_cost = $data['total_cost'] ?? 0;
    
    // Get serviceable count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'service'");
    $stmt->bind_param("s", $equipment_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $serviceable = $data['count'];
    $serviceable_cost = $data['cost'] ?? 0;
    
    // Get unserviceable count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'unservice'");
    $stmt->bind_param("s", $equipment_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $unserviceable = $data['count'];
    $unserviceable_cost = $data['cost'] ?? 0;
    
    // Get disposed count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'disposed'");
    $stmt->bind_param("s", $equipment_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $disposed = $data['count'];
    $disposed_cost = $data['cost'] ?? 0;
    
    // Return the data as JSON
    echo json_encode([
        'total' => $total,
        'total_cost' => $total_cost,
        'serviceable' => $serviceable,
        'serviceable_cost' => $serviceable_cost,
        'unserviceable' => $unserviceable,
        'unserviceable_cost' => $unserviceable_cost,
        'disposed' => $disposed,
        'disposed_cost' => $disposed_cost
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>