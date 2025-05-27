<?php
require_once 'config.php';
requireAuth();

require_once 'config.php';
requireAuth();

// Get the preselected equipment type
$equipment_type = $_GET['type'] ?? '';
$valid_types = ['Machinery', 'Construction', 'ICT Equipment', 'Communications', 'Military/Security', 'Office', 'DRRM Equipment', 'Furniture'];

if (!in_array($equipment_type, $valid_types)) {
    $_SESSION['error'] = "Invalid equipment type selected";
    header("Location: equipment-type.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item = [
        'property_number' => $_POST['property_number'],
        'description' => $_POST['description'],
        'model_number' => $_POST['model_number'],
        'equipment_type' => $_POST['equipment_type'],
        'remarks' => $_POST['remarks'],
        // Add other fields as needed
    ];
    
    // Change this line in create.php
$stmt = $db->prepare("INSERT INTO inventory (property_number, description, model_number, equipment_type, remarks) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", 
        $item['property_number'],
        $item['description'],
        $item['model_number'],
        $item['equipment_type'],
        $item['remarks']
    );
    
    if ($stmt->execute()) {
        $logger->logCreateItem($property_number, $_POST['equipment_type'], $_SESSION['username']);
        header("Location: home.php");
        exit();
    } else {
        $error = "Failed to add item: " . $db->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Inventory Item</title>
</head>
<body>
    <h1>Add New Inventory Item</h1>
    <a href="home.php">Back to Dashboard</a>
    
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST">
        <p>
            <label>Property Number:</label><br>
            <input type="text" name="property_number" required>
        </p>
        
        <p>
            <label>Description:</label><br>
            <input type="text" name="description" required>
        </p>
        
        <p>
            <label>Model Number (optional):</label><br>
            <input type="text" name="model_number">
        </p>
        
        <p>
            <label>Equipment Type:</label><br>
            <select name="equipment_type" required>
                <option value="ICT">ICT Equipment</option>
                <option value="Machine">Machine Equipment</option>
                <option value="Furniture">Furniture</option>
                <option value="Other">Other</option>
            </select>
            <input type="hidden" name="equipment_type" value="<?= htmlspecialchars($equipment_type) ?>" required>
            <label>Equipment Type: <?= htmlspecialchars($equipment_type) ?></label>
            <!-- Show the selected type to user -->
<div style="margin: 15px 0; padding: 10px; background: #f0f0f0; border-radius: 4px;">
    <strong>Equipment Type:</strong> <?= htmlspecialchars($equipment_type) ?>
</div>
        </p>
        
        <p>
            <label>Status:</label><br>
            <select name="remarks" required>
                <option value="service">In Service</option>
                <option value="unservice">Unserviceable</option>
                <option value="disposed">Disposed</option>
            </select>
        </p>
        
        <button type="submit">Save Item</button>
    </form>
</body>
</html>