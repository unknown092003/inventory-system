<?php
require_once __DIR__ . '/../api/config.php';
requireAuth();

// This script will update all inventory records that have empty equipment_type

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $equipment_type = $_POST['equipment_type'] ?? '';
    
    if (empty($equipment_type)) {
        $_SESSION['error'] = "No equipment type specified";
        header("Location: /inventory-system/pages/landing.php");
        exit();
    }
    
    try {
        // Update all records with empty equipment_type
        $stmt = $db->prepare("UPDATE inventory SET equipment_type = ? WHERE equipment_type IS NULL OR equipment_type = ''");
        $stmt->execute([$equipment_type]);
        
        $affected = $stmt->rowCount();
        $stmt->closeCursor();
        
        $_SESSION['import_success'] = "Updated equipment type for $affected records";
        
    } catch (Exception $e) {
        $_SESSION['import_error'] = "Error updating equipment types: " . $e->getMessage();
    }
    
    header("Location: /inventory-system/pages/landing.php");
    exit();
}

// Get valid equipment types
$valid_types = ['Machinery', 'Construction', 'ICT Equipment', 'Communications', 
               'Military/Security', 'Office', 'DRRM Equipment', 'Furniture'];

// Count records with empty equipment_type
$stmt = $db->prepare("SELECT COUNT(*) as count FROM inventory WHERE equipment_type IS NULL OR equipment_type = ''");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$empty_count = $row['count'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Equipment Types</title>
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
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-info {
            background-color: #d9edf7;
            border: 1px solid #bce8f1;
            color: #31708f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Update Equipment Types</h1>
        
        <?php if ($empty_count > 0): ?>
            <div class="alert alert-info">
                There are currently <strong><?= $empty_count ?></strong> records with empty equipment type.
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="equipment_type">Select Equipment Type:</label>
                    <select id="equipment_type" name="equipment_type" required>
                        <option value="">-- Select Type --</option>
                        <?php foreach ($valid_types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit">Update Records</button>
            </form>
        <?php else: ?>
            <div class="alert alert-info">
                All records have equipment types assigned.
            </div>
        <?php endif; ?>
        
        <p><a href="/inventory-system/pages/landing.php">← Back to Dashboard</a></p>
    </div>
</body>
</html>