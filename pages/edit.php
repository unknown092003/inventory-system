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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        a {
            text-decoration: none;
            color: #007BFF;
            display: block;
            text-align: center;
            margin-bottom: 20px;
        }
        a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
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
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            border-color: #007BFF;
            outline: none;
        }
        .error {
            color: red;
            margin-top: 5px;
            text-align: center;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            flex: 1;
            margin: 0 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .qr-image{
            width: 90%;
        }
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        .modal-btn {
            margin: 5px;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal-btn-primary {
            background-color: #4CAF50; /* Green */
            color: white;
        }
        .modal-btn-secondary {
            background-color: #f44336; /* Red */
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Inventory Item</h1>
        <a href="landing.php">Back to Dashboard</a>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>



        <form method="POST" id="editForm">
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



            <div class="button-group">
                <a href="/inventory-system/pages/landing/scan.php" class="button back-btn">Back to Scanner</a>
                <a href="/inventory-system/pages/landing.php?page=edit" class="button back-btn">Back to List</a>
                <button type="submit" class="button save-btn">Save Changes</button>
            </div>
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
                    <button id="editAgain" class="modal-btn modal-btn-secondary">Edit Again</button>
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



        document.getElementById('editAgain').addEventListener('click', function() {
            window.location.reload();
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