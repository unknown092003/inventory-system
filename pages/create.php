<?php
require_once __DIR__ . '/../api/config.php';
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
        'article' => $_POST['article'],
        'description' => $_POST['description'],
        'model_number' => $_POST['model_number'],
        'acquisition_date' => $_POST['acquisition_date'],
        'person_accountable' => $_POST['person_accountable'],
        'signature_of_inventory_team_date' => $_POST['signature_of_inventory_team_date'],
        'cost' => str_replace(',', '', $_POST['cost']),
        'equipment_type' => $_POST['equipment_type'],
        'remarks' => $_POST['remarks'],
    ];

    // 🔍 Check if property_number already exists
    $checkStmt = $db->prepare("SELECT id FROM inventory WHERE property_number = ?");
    $checkStmt->bind_param("s", $item['property_number']);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $error = "Property Number already exists. Please use a different one.";
    } else {
        // Insert only if not duplicate
        $stmt = $db->prepare("INSERT INTO inventory (property_number, article, description, model_number, acquisition_date, person_accountable, signature_of_inventory_team_date, cost, equipment_type, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssds", 
            $item['property_number'],
            $item['article'],
            $item['description'],
            $item['model_number'],
            $item['acquisition_date'],
            $item['person_accountable'],
            $item['signature_of_inventory_team_date'],
            $item['cost'],
            $item['equipment_type'],
            $item['remarks']
        );

        if ($stmt->execute()) {
            $logger->logCreateItem($item['property_number'], $item['equipment_type'], $_SESSION['username']);
            require_once __DIR__ . '/../api/qr_generator.php';

            if (generateSticker($item['property_number'])) {
                $_SESSION['success'] = "Item added and sticker generated successfully!";
            } else {
                $_SESSION['warning'] = "Item added but sticker generation failed";
            }

            header("Location: landing.php");
            exit();
        } else {
            $error = "Failed to add item: " . $db->error;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inventory Item</title>
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #eef2f5;
    margin: 0;
    padding: 20px;
}

h1 {
    color: #2c3e50;
    font-size: 28px;
    margin-bottom: 20px;
}

a {
    display: inline-block;
    margin-bottom: 20px;
    color: #3498db;
    font-weight: 500;
    text-decoration: none;
    transition: color 0.3s;
}

a:hover {
    color: #21618c;
    text-decoration: underline;
}

.container {
    max-width: 700px;
    margin: auto;
    background: #ffffff;
    padding: 30px 35px;
    border-radius: 16px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #2c3e50;
}

input[type="text"],
input[type="date"],
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #d0d7de;
    border-radius: 8px;
    font-size: 15px;
    transition: border-color 0.2s, box-shadow 0.2s;
    background-color: #fefefe;
    box-sizing: border-box;
}

input[type="text"]:focus,
input[type="date"]:focus,
select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.15);
    outline: none;
}

.error {
    color: #e74c3c;
    font-size: 14px;
    margin-top: 10px;
}

button {
    background-color: #3498db;
    color: #fff;
    border: none;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
    margin-top: 10px;
}

button:hover {
    background-color: #2c81ba;
    transform: scale(1.02);
}

.equipment-type-display {
    padding: 12px 15px;
    background-color: #ecf0f1;
    border: 1px solid #d0d7de;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 500;
    color: #34495e;
    margin-top: 8px;
}

/* Responsive Design */
@media (max-width: 600px) {
    .container {
        padding: 20px;
    }

    input[type="text"],
    input[type="date"],
    select {
        font-size: 14px;
    }

    button {
        width: 100%;
        padding: 12px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <h1>Add New Inventory Item</h1>
        <a href="landing.php">Back to Dashboard</a>

        <?php if (isset($error)) : ?>
        <script>alert("<?= htmlspecialchars($error) ?>");</script>
        <p class='error'><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>


        <form method="POST">
            <div class="form-group">
                <label>Property Number:</label>
                <input type="text" name="property_number" required>
            </div>

            <div class="form-group">
                <label>article:</label>
                <input type="text" name="article" required>
            </div>

            <div class="form-group">
                <label>Descriptions:</label>
                <input type="text" name="description" required>
            </div>

            <div class="form-group">
                <label>Model Number (optional):</label>
                <input type="text" name="model_number">
            </div>

            <div class="form-group">
                <label>Acquisition Date:</label>
                <input type="date" name="acquisition_date" required>
            </div>

            <div class="form-group">
                <label>Person Accountable:</label>
                <input type="text" name="person_accountable" required>
            </div>

            <div class="form-group">
                <label>Signature Date:</label>
                <input type="date" name="signature_of_inventory_team_date" required>
            </div>

            <div class="form-group">
                <label>Cost:</label>
                <input type="text" name="cost" required>
            </div>

            <div class="form-group">
                <label>Equipment Type:</label>
                <input type="hidden" name="equipment_type" value="<?= htmlspecialchars($equipment_type) ?>" required>
                <div class="equipment-type-display">
                    <strong>Equipment Type:</strong> <?= htmlspecialchars($equipment_type) ?>
                </div>
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="remarks" required>
                    <option value="service">In Service</option>
                    <option value="unservice">Unserviceable</option>
                    <option value="disposed">Disposed</option>
                </select>
            </div>

            <button type="submit">Save Item</button>
        </form>
    </div>
</body>
</html>