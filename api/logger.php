<?php
class Logger {
    private $db;
    
    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }
    
    public function logAction($action, $description, $property_number, $user, $details = '') {
        $stmt = $this->db->prepare("
            INSERT INTO activity_log 
            (action, description, property_number, user, details) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([$action, $description, $property_number, $user, $details]);
    }
    
    public function logLogin($username) {
        $this->logAction('login', 'User logged in', NULL, $username);
    }
    
    public function logCreateItem($property_number, $equipment_type, $username) {
        $this->logAction('created', $equipment_type, $property_number, $username, "Added new {$equipment_type} equipment");
    }
    
    public function logEditItem($property_number, $equipment_type, $username, $changes) {
        $formattedChanges = [
            'old' => $this->formatChanges($changes['old']),
            'new' => $this->formatChanges($changes['new'])
        ];
        $this->logAction('edited', $equipment_type, $property_number, $username, json_encode($formattedChanges));
    }
    
    private function formatChanges($data) {
        return [
            'Property Number' => $data['property_number'] ?? '',
            'Description' => $data['description'] ?? '',
            'Model Number' => $data['model_number'] ?? '',
            'Equipment Type' => $data['equipment_type'] ?? '',
            'Status' => $data['remarks'] ?? '',
            'Cost' => $data['cost'] ?? '',
            // Add other fields as needed
        ];
    }
    // In Logger.php
public function logImport($username, $count, $equipment_type) {
    $this->logAction(
        'imported', 
        "Imported $count $equipment_type items", 
        null, 
        $username, 
        "Bulk import of $equipment_type equipment"
    );
}
    
    public function getRecentLogs($limit = 20) {
        $result = $this->db->query("
            SELECT timestamp, action, description, user, details
            FROM activity_log 
            ORDER BY timestamp DESC 
            LIMIT $limit
        ");
        
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>