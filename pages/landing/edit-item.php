<?php
session_start();

// Database connection
$pdo = new PDO("mysql:host=localhost;dbname=inventory-system", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get property number from URL
$propertyNumber = $_GET['property_number'] ?? null;

// Validate property number exists
if (!$propertyNumber) {
    $_SESSION['error_message'] = "Invalid request - missing property number";
    header("Location: /inventory-system/public/pages/scanner.php");
    exit;
}

// Fetch existing item data
try {
    $stmt = $pdo->prepare("SELECT * FROM inventory WHERE property_number = ?");
    $stmt->execute([$propertyNumber]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        $_SESSION['error_message'] = "Item not found";
        header("Location: /inventory-system/public/pages/scanner.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    header("Location: /inventory-system/public/pages/scanner.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
        
        // Log activity
        $logstmt = $pdo->prepare("INSERT INTO activity_log
                (action_type, table_name, record_id, user, description, timestamp)
                VALUES
                (:action_type, :table_name, :record_id, :user, :description, NOW())");
                    
        $logstmt->execute([
            ':action_type' => 'edit',
            ':table_name' => 'inventory',
            ':record_id' => $item['id'],
            ':user' => $_POST['person_accountable'],
            ':description' => $_POST['description']
        ]);

        // Auto-generate QR code if missing
        $newPropertyNumber = $_POST['property_number'];
        $qrPath = $_SERVER['DOCUMENT_ROOT'] . '/inventory-system/qr/' . $newPropertyNumber . '.png';
        if (!file_exists($qrPath)) {
            file_get_contents("http://localhost/inventory-system/generate_qr.php?property_number=" . urlencode($newPropertyNumber));
        }

        $_SESSION['success_message'] = "Item updated successfully";
        header("Location: /inventory-system/public/pages/scanner.php");
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: edit.php?property_number=" . urlencode($propertyNumber));
        exit;
    }
}

// Display success/error messages if they exist
$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/edit.item.css">
    <title>Edit Item - <?= htmlspecialchars($item['property_number']) ?></title>
    <style>
        .notification {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .button {
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .save-btn {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .back-btn {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Inventory Item</h1>
        
        <?php if ($successMessage): ?>
            <div class="notification success">
                <?= htmlspecialchars($successMessage) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="notification error">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

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
            
            <div class="button-group">
                <a href="/inventory-system/pages/landing/scan.php" class="button back-btn">Back to Scanner</a>
                <button type="submit" class="button save-btn">Save Changes</button>
            </div>
        </form>

        <div class="qr-section">
            <h2>QR Sticker</h2>
            <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/inventory-system/qr/' . $item['property_number'] . '.png')): ?>
                <img src="/inventory-system/qr/<?= htmlspecialchars($item['property_number']) ?>.png" alt="QR Code" class="qr-image">
            <?php else: ?>
                <p class="qr-missing">QR code not generated yet</p>
            <?php endif; ?>
            <a href="/inventory-system/generate_qr.php?property_number=<?= $item['property_number'] ?>" class="generate-qr-btn">Generate QR</a>
        </div>
    </div>

    <script src="/inventory-system/public/scripts/qr-generator.js"></script>
</body>
</html>