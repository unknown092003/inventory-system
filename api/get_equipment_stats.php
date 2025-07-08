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
    $stmt->execute([$equipment_type]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $data['total'];
    $total_cost = $data['total_cost'] ?? 0;
    
    // Get serviceable count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'service'");
    $stmt->execute([$equipment_type]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $serviceable = $data['count'];
    $serviceable_cost = $data['cost'] ?? 0;
    
    // Get unserviceable count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'unservice'");
    $stmt->execute([$equipment_type]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $unserviceable = $data['count'];
    $unserviceable_cost = $data['cost'] ?? 0;
    
    // Get disposed count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'disposed'");
    $stmt->execute([$equipment_type]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $disposed = $data['count'];
    $disposed_cost = $data['cost'] ?? 0;
    
    // Get standby count and cost
    $stmt = $db->prepare("SELECT COUNT(*) as count, SUM(cost) as cost FROM inventory WHERE equipment_type = ? AND remarks = 'standby'");
    $stmt->execute([$equipment_type]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $standby = $data['count'];
    $standby_cost = $data['cost'] ?? 0;
    
    // Return the data as JSON
    echo json_encode([
        'total' => $total,
        'total_cost' => $total_cost,
        'serviceable' => $serviceable,
        'serviceable_cost' => $serviceable_cost,
        'unserviceable' => $unserviceable,
        'unserviceable_cost' => $unserviceable_cost,
        'disposed' => $disposed,
        'disposed_cost' => $disposed_cost,
        'standby' => $standby,
        'standby_cost' => $standby_cost
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>