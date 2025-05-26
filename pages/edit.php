<?php
require_once __DIR__ . '/../api/config.php';
requireAuth();

$property_number = $_GET['property_number'];

// Get the complete item data including cost
$item = $db->query("
    SELECT * FROM inventory 
    WHERE property_number = '".$db->real_escape_string($property_number)."'
")->fetch_assoc();

if (!$item) {
    $_SESSION['error'] = "Item not found!";
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare all fields from the form
    $update_data = [
        'property_number' => $_POST['property_number'],
        'description' => $_POST['description'],
        'model_number' => $_POST['model_number'],
        'acquisition_date' => $_POST['acquisition_date'],
        'person_accountable' => $_POST['person_accountable'],
        'cost' => str_replace(',', '', $_POST['cost']), // Remove commas from cost
        'equipment_type' => $_POST['equipment_type'],
        'remarks' => $_POST['remarks'],
        'signature_of_inventory_team_date' => $_POST['signature_date'] ?? null
    ];

    $changes = [
        'old' => $item,
        'new' => $update_data
    ];

    // Update query with all fields
    $stmt = $db->prepare("
        UPDATE inventory 
        SET 
            property_number = ?,
            description = ?,
            model_number = ?,
            acquisition_date = ?,
            person_accountable = ?,
            cost = ?,
            equipment_type = ?,
            remarks = ?,
            signature_of_inventory_team_date = ?
        WHERE property_number = ?
    ");
    
    $stmt->bind_param(
        "sssssdssss",
        $update_data['property_number'],
        $update_data['description'],
        $update_data['model_number'],
        $update_data['acquisition_date'],
        $update_data['person_accountable'],
        $update_data['cost'],
        $update_data['equipment_type'],
        $update_data['remarks'],
        $update_data['signature_of_inventory_team_date'],
        $property_number // Original property number for WHERE clause
    );
    
    if ($stmt->execute()) {
        $logger->logEditItem(
            $update_data['property_number'], 
            $update_data['equipment_type'], 
            $_SESSION['username'], 
            $changes
        );
        $_SESSION['success'] = "Item updated successfully!";
        header("Location: /inventory-system/pages/landing.php");
        exit();
    } else {
        $error = "Failed to update item: " . $db->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Inventory Item</title>
    <style>
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .error {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Edit Inventory Item</h1>
    <a href="home.php">Back to Dashboard</a>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    
    <form method="POST">
        <div class="form-group">
            <label>Property Number:</label>
            <input type="text" name="property_number" value="<?= htmlspecialchars($item['property_number']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Description:</label>
            <input type="text" name="description" value="<?= htmlspecialchars($item['description']) ?>" required>
        </div>
        
        <div class="form-group">
            <label>Model Number:</label>
            <input type="text" name="model_number" value="<?= htmlspecialchars($item['model_number'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Acquisition Date:</label>
            <input type="date" name="acquisition_date" value="<?= htmlspecialchars($item['acquisition_date']) ?>">
        </div>
        
        <div class="form-group">
            <label>Person Accountable:</label>
            <input type="text" name="person_accountable" value="<?= htmlspecialchars($item['person_accountable'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <label>Cost:</label>
            <input type="text" name="cost" value="<?= htmlspecialchars(number_format($item['cost'], 2)) ?>">
        </div>
        
        <div class="form-group">
            <label>Equipment Type:</label>
            <select name="equipment_type" required>
                <option value="ICT" <?= $item['equipment_type'] === 'ICT' ? 'selected' : '' ?>>ICT Equipment</option>
                <option value="Machine" <?= $item['equipment_type'] === 'Machine' ? 'selected' : '' ?>>Machine Equipment</option>
                <option value="Furniture" <?= $item['equipment_type'] === 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                <option value="Other" <?= $item['equipment_type'] === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Status:</label>
            <select name="remarks" required>
                <option value="service" <?= $item['remarks'] === 'service' ? 'selected' : '' ?>>In Service</option>
                <option value="unservice" <?= $item['remarks'] === 'unservice' ? 'selected' : '' ?>>Unserviceable</option>
                <option value="disposed" <?= $item['remarks'] === 'disposed' ? 'selected' : '' ?>>Disposed</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Signature Date:</label>
            <input type="date" name="signature_date" value="<?= htmlspecialchars($item['signature_of_inventory_team_date'] ?? '') ?>">
        </div>
        
        <div class="form-group">
            <button type="submit">Update Item</button>
        </div>
    </form>
</body>
</html>