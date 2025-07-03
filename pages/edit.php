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
        // Instead of redirecting, we will show the modal
        echo "<script>window.onload = function() { document.getElementById('confirmationModal').style.display = 'block'; };</script>";
    } else {
        $error = "Failed to update item: " . $db->error;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory Item</title>
    <style>
/* Modern Reset */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f3f4f6;
    color: #1f2937;
    display: flex;
    justify-content: center;
    align-items: start;
    min-height: 100vh;
    padding: 40px 16px;
}

.container {
    background-color: #ffffff;
    padding: 32px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    max-width: 700px;
    width: 100%;
}

h1 {
    font-size: 2rem;
    margin-bottom: 20px;
    text-align: center;
    color: #111827;
}

a.back-dashboard {
    display: inline-block;
    margin-bottom: 20px;
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

a.back-dashboard:hover {
    text-decoration: underline;
}

.form-group {
    margin-bottom: 16px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #374151;
}

input[type="text"],
input[type="date"],
select {
    width: 100%;
    padding: 12px;
    font-size: 1rem;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    transition: border-color 0.3s;
}

input:focus,
select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.button-group {
    margin: 24px 0;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.button {
    padding: 12px 18px;
    font-size: 1rem;
    border-radius: 10px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.3s ease;
}

.back-btn {
    background-color: #e5e7eb;
    color: #1f2937;
}

.back-btn:hover {
    background-color: #d1d5db;
}

.save-btn {
    background: linear-gradient(to right, #06b6d4, #3b82f6);
    color: white;
    border: none;
    width: 100%;
    margin-top: 10px;
}

.save-btn:hover {
    background: linear-gradient(to right, #0284c7, #2563eb);
}

.qr-section {
    margin-top: 32px;
    text-align: center;
}

.qr-section h2 {
    font-size: 1.4rem;
    margin-bottom: 16px;
}

.qr-btn {
    display: inline-block;
    margin-top: 12px;
    padding: 12px 20px;
    background: linear-gradient(135deg, #06b6d4, #3b82f6);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
    transition: background 0.3s ease;
}

.qr-btn:hover {
    background: linear-gradient(135deg, #0284c7, #2563eb);
}

.qr-image {
    max-width: 100%;
    height: auto;
    margin-top: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 8px;
    background-color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(31, 41, 55, 0.5);
}

.modal-content {
    background-color: #ffffff;
    margin: 10% auto;
    padding: 24px;
    border-radius: 12px;
    max-width: 400px;
    text-align: center;
}

.modal-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 20px;
}

.modal-btn {
    padding: 10px 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.modal-btn-primary {
    background-color: #3b82f6;
    color: white;
}

.modal-btn-secondary {
    background-color: #e5e7eb;
    color: #1f2937;
}

.modal-btn-primary:hover {
    background-color: #2563eb;
}

.modal-btn-secondary:hover {
    background-color: #d1d5db;
}

.error {
    color: red;
    font-weight: bold;
    margin-bottom: 12px;
}

/* for closing 
 */
 .modal {
    transition: opacity 0.3s ease;
}

.modal.fade-out {
    opacity: 0;
}

.modal-btn-tertiary {
    background-color: #f0f0f0;
    color: #333;
    border: 1px solid #ccc;
}

.modal-btn-tertiary:hover {
    background-color: #e0e0e0;
}


    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Inventory Item</h1>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>



        <form method="POST" id="editForm" class="qr-form">
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
                    <option value="Machinery" <?= $item['equipment_type'] === 'Machinery' ? 'selected' : '' ?>>Machinery</option>
                    <option value="Construction" <?= $item['equipment_type'] === 'Construction' ? 'selected' : '' ?>>Construction</option>
                    <option value="ICT Equipment" <?= $item['equipment_type'] === 'ICT Equipment' || $item['equipment_type'] === 'ICT' ? 'selected' : '' ?>>ICT Equipment</option>
                    <option value="Communications" <?= $item['equipment_type'] === 'Communications' ? 'selected' : '' ?>>Communications</option>
                    <option value="Military/Security" <?= $item['equipment_type'] === 'Military/Security' ? 'selected' : '' ?>>Military/Security</option>
                    <option value="Office" <?= $item['equipment_type'] === 'Office' ? 'selected' : '' ?>>Office</option>
                    <option value="DRRM Equipment" <?= $item['equipment_type'] === 'DRRM Equipment' ? 'selected' : '' ?>>DRRM Equipment</option>
                    <option value="Furniture" <?= $item['equipment_type'] === 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                </select>
            </div>



            <div class="form-group">
                <label>Status:</label>
                <select name="remarks" required>
                    <option value="standby" <?= $item['remarks'] === 'standby' ? 'selected' : '' ?>>Standby</option>
                    <option value="service" <?= $item['remarks'] === 'service' ? 'selected' : '' ?>>In Service</option>
                    <option value="unservice" <?= $item['remarks'] === 'unservice' ? 'selected' : '' ?>>Unserviceable</option>
                    <option value="disposed" <?= $item['remarks'] === 'disposed' ? 'selected' : '' ?>>Disposed</option>
                </select>
            </div>



            <div class="form-group">
                <label>Signature Date:</label>
                <input type="date" name="signature_date" value="<?= htmlspecialchars($item['signature_of_inventory_team_date'] ?? '') ?>">
            </div>



            <div class="button-group">
                <a href="/inventory-system/pages/landing/scan.php" class="button back-btn">Back to Scanner</a>
                <a href="/inventory-system/pages/landing.php?page=data" class="button back-btn">Back to List</a>
                <a href="/inventory-system/pages/landing.php?page=home" class="button back-btn">Back Dashboard</a>
            </div>
                <button type="submit" class="button save-btn">Save Changes</button>
            
        </form>



        <div class="qr-section">
            <h2>QR Sticker</h2>
            <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/inventory-system/qr/' . ($updatedPropertyNumber ?? $item['property_number']) . '.png')): ?>
                <img src="/inventory-system/qr/<?= htmlspecialchars($updatedPropertyNumber ?? $item['property_number']) ?>.png?t=<?= time() ?>" alt="QR Code" class="qr-image">
            <?php else: ?>
                <p class="qr-missing">QR code not generated yet</p>
            <?php endif; ?>
            <a href="/inventory-system/pages/sologenerated.php?property_number=<?= htmlspecialchars($updatedPropertyNumber ?? $item['property_number']) ?>&t=<?= time() ?>" class="button qr-btn">Generate QR</a>
        </div>



       <div id="confirmationModal" class="modal">
    <div class="modal-content">
        <h3>Save Changes</h3>
        <p>What would you like to do after saving?</p>
        <div class="modal-actions">
            <button id="generateQR" class="modal-btn modal-btn-primary">Generate New QR Code</button>
            <button id="openScanner" class="modal-btn modal-btn-secondary">Open Scanner</button>
            <button id="backToList" class="modal-btn modal-btn-secondary">Back to List</button>
            <button id="cancelChanges" class="modal-btn modal-btn-tertiary">Cancel</button>
        </div>
    </div>
</div>



        <script>
        // Handle modal button actions
        document.getElementById('generateQR').addEventListener('click', function() {
            const newPN = document.querySelector('input[name="property_number"]').value;
            window.location.href = `/inventory-system/pages/sologenerated.php?property_number=${encodeURIComponent(newPN)}&t=${Date.now()}`;
        });



        document.getElementById('openScanner').addEventListener('click', function() {
            window.location.href = "/inventory-system/pages/landing/scan.php";
        });



        document.getElementById('backToList').addEventListener('click', function() {
            window.location.href = "/inventory-system/pages/landing.php?page=edit";
        });



       document.getElementById('cancelChanges').addEventListener('click', function() {
    // Close the modal
    document.getElementById('confirmationModal').style.display = 'none';
    
    // Optional: Add fade-out animation
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('fade-out');
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('fade-out');
    }, 300);
});



        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target == document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        });
        </script>
    </div>
</body>
</html>