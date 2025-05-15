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
    header("Location: /inventory-system/pages/scanner.php");
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
            acquisition_date = ?,
            cost = ?,
            person_accountable = ?,
            remarks = ?,
            signature_of_inventory_team_date = ?
            WHERE id = ?");
            
        $stmt->execute([
            $_POST['property_number'],
            $_POST['description'],
            $_POST['model_number'],
            $_POST['acquisition_date'],
            $_POST['cost'],
            $_POST['person_accountable'],
            $_POST['remarks'],
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

        // For AJAX response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode([
                'success' => true,
                'message' => 'Item updated successfully',
                'property_number' => $_POST['property_number']
            ]);
            exit;
        }

        $_SESSION['success_message'] = "Item updated successfully";
        $_SESSION['updated_property_number'] = $_POST['property_number'];
        header("Location: edit.php?property_number=" . urlencode($_POST['property_number']));
        exit;
        
    } catch (PDOException $e) {
        // For AJAX response
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
            exit;
        }

        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: edit.php?property_number=" . urlencode($propertyNumber));
        exit;
    }
}

// Display success/error messages if they exist
$successMessage = $_SESSION['success_message'] ?? null;
$updatedPropertyNumber = $_SESSION['updated_property_number'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message']);
unset($_SESSION['updated_property_number']);
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
            flex-wrap: wrap;
        }
        .button {
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            flex: 1;
            min-width: 120px;
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
        .action-btn {
            background-color: #007bff;
            color: white;
        }
        .qr-btn {
            background-color: #17a2b8;
            color: white;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            width: 80%;
            max-width: 500px;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            justify-content: flex-end;
        }
        .modal-btn {
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .modal-btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .modal-btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Inventory Item</h1>
        
        <?php if ($successMessage): ?>
            <div class="notification success">
                <?= htmlspecialchars($successMessage) ?>
                <div class="button-group" style="margin-top: 10px;">
                    <a href="edit.php?property_number=<?= urlencode($updatedPropertyNumber ?? $propertyNumber) ?>" class="button action-btn">Edit Again</a>
                    <a href="/inventory-system/pages/sologenerated.php?property_number=<?= urlencode($updatedPropertyNumber ?? $propertyNumber) ?>" class="button qr-btn">Generate QR</a>
                    <a href="/inventory-system/pages/landing/scan.php" class="button action-btn">Open Scanner</a>
                    <a href="/inventory-system/pages/landing/list.php" class="button back-btn">Back to List</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
            <div class="notification error">
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="edit-form" id="editForm">
            <input type="hidden" name="property_number_original" value="<?= htmlspecialchars($item['property_number']) ?>">
            
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
                <label>Acquisition Date:</label>
                <input type="date" name="acquisition_date" value="<?= htmlspecialchars($item['acquisition_date']) ?>">
            </div>

            <div class="form-group">
                <label>Unit Value:</label>
                <input type="number" name="cost" value="<?= htmlspecialchars($item['cost']) ?>">
            </div>
            
            <div class="form-group">
                <label>Accountable Person:</label>
                <input type="text" name="person_accountable" value="<?= htmlspecialchars($item['person_accountable']) ?>">
            </div>
            
            <div class="form-group">
                <label>remarks:</label>
                <select name="remarks">
                    <option value="service" <?= $item['remarks'] === 'service' ? 'selected' : '' ?>>service</option>
                    <option value="unservice" <?= $item['remarks'] === 'unservice' ? 'selected' : '' ?>>unservice</option>
                    <option value="dispose" <?= $item['remarks'] === 'dispose' ? 'selected' : '' ?>>dispose</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Date:</label>
                <input type="date" name="signature_of_inventory_team_date" value="<?= htmlspecialchars($item['signature_of_inventory_team_date']) ?>">
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
                <img src="/inventory-system/qr/<?= htmlspecialchars($updatedPropertyNumber ?? $item['property_number']) ?>.png" alt="QR Code" class="qr-image">
            <?php else: ?>
                <p class="qr-missing">QR code not generated yet</p>
            <?php endif; ?>
            <a href="/inventory-system/pages/sologenerated.php?property_number=<?= htmlspecialchars($updatedPropertyNumber ?? $item['property_number']) ?>" class="button qr-btn">Generate QR</a>
        </div>
    </div>

    <!-- Confirmation Modal -->
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
    // Handle form submission with confirmation
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent the default form submission
        
        // Submit the form via AJAX first
        const formData = new FormData(this);
        
        fetch('', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show a temporary "Saved" notification
                const notification = document.createElement('div');
                notification.className = 'notification success';
                notification.textContent = 'Item saved successfully!';
                document.body.prepend(notification);

                setTimeout(() => {
                    notification.remove();

                    // Show the confirmation modal after the notification disappears
                    const modal = document.getElementById('confirmationModal');
                    modal.style.display = 'block';

                    // Update the QR generation link and image with the new property number if it changed
                    const originalPN = document.querySelector('input[name="property_number_original"]').value;
                    const newPN = data.property_number;

                    if (originalPN !== newPN) {
                        document.querySelector('.qr-section .qr-btn').href = 
                            `/inventory-system/pages/sologenerated.php?property_number=${encodeURIComponent(newPN)}`;
                        
                        if (document.querySelector('.qr-section .qr-image')) {
                            document.querySelector('.qr-section .qr-image').src = 
                                `/inventory-system/qr/${encodeURIComponent(newPN)}.png?timestamp=${new Date().getTime()}`;
                        }
                    } else {
                        // Force reload of the QR image to reflect the latest changes
                        if (document.querySelector('.qr-section .qr-image')) {
                            const qrImage = document.querySelector('.qr-section .qr-image');
                            qrImage.src = qrImage.src.split('?')[0] + `?timestamp=${new Date().getTime()}`;
                        }
                    }
                }, 1500); // Delay for the notification
            } else {
                alert(data.message || 'Error saving changes');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving changes');
        });
    });

    // Handle modal button actions
    document.getElementById('generateQR').addEventListener('click', function() {
        const newPN = document.querySelector('input[name="property_number"]').value;
        window.location.href = `/inventory-system/pages/sologenerated.php?property_number=${encodeURIComponent(newPN)}`;
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
</body>
</html>