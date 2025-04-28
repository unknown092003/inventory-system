<?php
// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");

// Get property number from URL
$propertyNumber = $_GET['property_number'] ?? null;

// Validate property number exists
if (!$propertyNumber) {
    die("Invalid request");
}

// Fetch existing item data
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = ?");
$stmt->execute([$propertyNumber]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    die("Item not found");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update database
    $stmt = $pdo->prepare("UPDATE inventory SET
        property_number = ?,
        description = ?,
        model_number = ?,
        serial_number = ?,
        acquisition_date_cost = ?,
        person_accountable = ?,
        status = ?
        WHERE id = ?");
        
    $stmt->execute([
        $_POST['property_number'],
        $_POST['description'],
        $_POST['model_number'],
        $_POST['serial_number'],
        $_POST['acquisition_date_cost'],
        $_POST['person_accountable'],
        $_POST['status'],
        $item['id']
    ]);
    
    // Get old data
    $oldStmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ?");
    $oldStmt->execute([$item['id']]);
    $oldData = $oldStmt->fetch(PDO::FETCH_ASSOC);

    // Prepare new data
    $newData = [
        'property_number' => $_POST['property_number'],
        'description' => $_POST['description'],
        'model_number' => $_POST['model_number'],
        'serial_number' => $_POST['serial_number'],
        'acquisition_date_cost' => $_POST['acquisition_date_cost'],
        'person_accountable' => $_POST['person_accountable'],
        'status' => $_POST['status']
    ];

    // Log detailed activity
    $stmt = $pdo->prepare("INSERT INTO activity_log
                (action_type, table_name, record_id, old_data, new_data, user)
                VALUES (:action, :table, :id, :old, :new, :user)");
                
    $stmt->execute([
        ':action' => 'update',
        ':table' => 'inventory',
        ':id' => $item['id'],
        ':old' => json_encode($oldData),
        ':new' => json_encode($newData),
        ':user' => $_POST['person_accountable']
    ]);

    header("Location: edit.php?success=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/edit.item.css">
    <title>Edit Item</title>
</head>
<body>
    <h1>Edit Inventory Item</h1>

    <form method="post" class="edit-form">
        <div class="form-group" id="p1">
            <label>Property Number:</label>
            <input type="text" name="property_number" value="<?= htmlspecialchars($item['property_number']) ?>" required>
        </div>
        <div class="form-group" id="p2">
            <label>Description:</label>
            <textarea name="description" required><?= htmlspecialchars($item['description']) ?></textarea>
        </div>

        
        <div class="form-group" id="p3">
            <label>Model Number:</label>
            <input type="text" name="model_number" value="<?= htmlspecialchars($item['model_number']) ?>">
        </div>
        
        <div class="form-group">
            <label>Serial Number:</label>
            <input type="text" name="serial_number" value="<?= htmlspecialchars($item['serial_number']) ?>">
        </div>
        
        <div class="form-group">
            <label>Acquisition Date/Cost:</label>
            <input type="text" name="acquisition_date_cost" value="<?= htmlspecialchars($item['acquisition_date_cost']) ?>">
        </div>
        
        <div class="form-group">
            <label>Accountable Person:</label>
            <input type="text" name="person_accountable" value="<?= htmlspecialchars($item['person_accountable']) ?>">
        </div>
        
        <div class="form-group">
            <label>Status:</label>
            <select name="status">
                <option value="Active" <?= $item['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= $item['status'] === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="Retired" <?= $item['status'] === 'Retired' ? 'selected' : '' ?>>Retired</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Date:</label>
            <input type="text" name="person_accountable" value="<?= htmlspecialchars($item['signature_of_inventory_team_date']) ?>">
        </div>
        
        <button type="submit" class="save-btn">Save Changes</button>
    </form>
</body>
</html>