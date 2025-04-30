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
        status = ?,
        signature_of_inventory_team_date = ?
        WHERE id = ?");
        
    $stmt->execute([
        $_POST['property_number'],
        $_POST['description'],
        $_POST['model_number'],
        $_POST['serial_number'],
        $_POST['acquisition_date_cost'],
        $_POST['person_accountable'],
        $_POST['status'],
        $_POST['signature_of_inventory_team_date'],
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
        'status' => $_POST['status'],
        'date' => $_POST['signature_of_inventory_team_date'],
    ];

    // Log detailed activity
    $logstmt = $pdo->prepare("INSERT INTO activity_log
            (action_type, table_name, record_id, user, description, timestamp)
            VALUES
            (:action_type, :table_name, :record_id, :user, :description, NOW())");
                
    $logstmt->execute([
        ':action_type' => 'edit',
        ':table_name' => 'inventory',
        ':record_id' => $item['id'],
        ':user' => '' . $_POST['person_accountable'],
        ':description' => ' ' . $_POST['description']
    ]);

    // Update property number and re-fetch item with new data
    $propertyNumber = $_POST['property_number'];
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = ?");
    $stmt->execute([$propertyNumber]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Auto-generate QR code if missing
    $qrPath = $_SERVER['DOCUMENT_ROOT'] . '/inventory-system/qr/' . $propertyNumber . '.png';
    if (!file_exists($qrPath)) {
        $qrResponse = file_get_contents("http://localhost/inventory-system/generate_qr.php?property_number=" . urlencode($propertyNumber));
        if ($qrResponse === false) {
            die("Failed to generate QR code. Please check the QR generation script.");
        }
    }
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
            <input type="date" name="signature_of_inventory_team_date" value="<?= htmlspecialchars($item['signature_of_inventory_team_date']) ?>">
        </div>
        
        <button type="submit" class="save-btn">Save Changes</button>
    </form>

    <?php if (isset($showSuccess)): ?>
    <div class="success-banner">
        Changes saved successfully! Updated QR code:
        <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/inventory-system/qr/' . $item['property_number'] . '.png')): ?>
            <img src="/inventory-system/qr/<?= htmlspecialchars($item['property_number']) ?>.png" alt="QR Code" class="qr-image">
        <?php else: ?>
            <p class="qr-missing">QR code not generated yet. <a href="/inventory-system/generate_qr.php?property_number=<?= $item['property_number'] ?>">Generate Now</a></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="qr-section">
        <h2>QR Sticker</h2>
        <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/inventory-system/qr/' . $item['property_number'] . '.png')): ?>
            <img src="/inventory-system/qr/<?= htmlspecialchars($item['property_number']) ?>.png" alt="QR Code" class="qr-image">
        <?php else: ?>
            <p class="qr-missing">QR code not generated yet</p>
        <?php endif; ?>
        <a href="/inventory-system/generate_qr.php?property_number=<?= $item['property_number'] ?>" class="generate-qr-btn">Generate QR</a>
    </div>

    <script src="/inventory-system/public/scripts/qr-generator.js"></script>
</body>
</html>