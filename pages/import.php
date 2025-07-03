<?php
/**
 * Inventory Import Script
 * 
 * This script handles importing inventory items from Excel files into the database.
 * It properly handles cases where the article column may be empty or missing,
 * preventing column shifting and ensuring data integrity.
 */

// Include configuration file and authentication check
require_once __DIR__ . '/../api/config.php';
requireAuth(); // Ensure user is authenticated

// Get and validate equipment type from URL parameter
$equipment_type = $_GET['type'] ?? '';
// Define valid equipment types
$valid_types = ['Machinery', 'Construction', 'ICT Equipment', 'Communications', 
               'Military/Security', 'Office', 'DRRM Equipment', 'Furniture'];

// Validate the equipment type
if (!in_array($equipment_type, $valid_types)) {
    $_SESSION['error'] = "Invalid equipment type selected. Please choose a valid equipment type.";
    header("Location: equipment-type.php");
    exit();
}

// Process file upload if POST request with file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    // Validate file upload errors
    if ($_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['import_error'] = "File upload error: " . $_FILES['excel_file']['error'];
        header("Location: /inventory-system/pages/landing.php");
        exit();
    }

    // Verify file type is Excel
    $allowed_types = ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $_FILES['excel_file']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime_type, $allowed_types)) {
        $_SESSION['import_error'] = "Invalid file type. Please upload an Excel file (.xls or .xlsx).";
        header("Location:/inventory-system/pages/landing.php");
        exit();
    }

    // Include required libraries
    require_once __DIR__ .  '/../vendor/autoload.php';
    // Include QR generator functionality
    require_once __DIR__ . '/../api/qr_generator.php';
    
    try {
        // Process the Excel file
        $file = $_FILES['excel_file']['tmp_name'];
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $sheet = $spreadsheet->getActiveSheet();
        
        // Initialize counters and tracking variables
        $imported_count = 0;  // Count of new items added
        $updated_count = 0;   // Count of existing items updated
        $skipped_count = 0;   // Count of rows skipped due to errors
        $errors = [];         // Array to store error messages
        $success_rows = [];   // Array to track successfully processed rows
        $db->begin_transaction(); // Start database transaction

        // Iterate through each row in the Excel sheet
        foreach ($sheet->getRowIterator() as $row) {
            if ($row->getRowIndex() == 1) continue; // Skip header row

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            
            // Get all cell values for this row, ensuring we get exactly 7 columns
            $data = [];
            $columnCount = 0;
            foreach ($cellIterator as $cell) {
                if ($columnCount >= 7) break; // Only process first 7 columns
                $data[] = $cell->getValue();
                $columnCount++;
            }
            
            // Ensure we have exactly 7 columns (pad with empty strings if needed)
            while (count($data) < 7) {
                $data[] = '';
            }

            /**
             * Data Processing and Column Mapping
             * 
             * This section handles the complex logic of mapping Excel columns to database fields,
             * especially when the article column is empty or missing.
             */
            
            // Initialize all fields with default values
            $article = null;
            $acquisition_date = null;
            $model_number = null;
            $property_number = '';
            $description = '';
            $person_accountable = null;
            $cost = 0;
            $remarks = 'service';

            // Always use fixed column mapping (do not shift columns)
            $columns = [
                'article' => 0,
                'acquisition_date' => 1,
                'model_number' => 2,
                'property_number' => 3,
                'description' => 4,
                'person_accountable' => 5,
                'cost' => 6
            ];

            // Assign values based on the fixed column mapping
            $article = (isset($data[$columns['article']]) && !empty(trim($data[$columns['article']])))
                ? trim($data[$columns['article']])
                : null;

            // Process acquisition date if available
            if (isset($data[$columns['acquisition_date']]) && !empty(trim($data[$columns['acquisition_date']]))) {
                $dateValue = trim($data[$columns['acquisition_date']]);
                $acquisition_date = DateTime::createFromFormat('F j, Y', $dateValue) ?: 
                                   DateTime::createFromFormat('m/d/Y', $dateValue) ?: null;
            }

            // Process model number if available
            if (isset($data[$columns['model_number']])) {
                $model_number = !empty(trim($data[$columns['model_number']])) ? trim($data[$columns['model_number']]) : null;
            }

            // Process property number (required field)
            if (isset($data[$columns['property_number']])) {
                $property_number = trim($data[$columns['property_number']] ?? '');
            }

            // Process description (required field)
            if (isset($data[$columns['description']])) {
                $description = trim($data[$columns['description']] ?? '');
            }

            // Process person accountable if available
            if (isset($data[$columns['person_accountable']])) {
                $person_accountable = !empty(trim($data[$columns['person_accountable']])) ? trim($data[$columns['person_accountable']]) : null;
            }

            // Process cost value
            if (isset($data[$columns['cost']]) && !empty(trim($data[$columns['cost']]))) {
                $cost = filter_var(str_replace(',', '', trim($data[$columns['cost']])), FILTER_VALIDATE_FLOAT);
                if ($cost === false) $cost = 0;
            }

            /**
             * Data Validation
             */
            $row_errors = [];
            if (empty($property_number)) {
                $row_errors[] = "Property number is required";
            }
            if (empty($description)) {
                $row_errors[] = "Description is required";
            }

            // Skip this row if there are validation errors
            if (!empty($row_errors)) {
                $errors[] = "Row {$row->getRowIndex()}: " . implode(', ', $row_errors);
                $skipped_count++;
                continue;
            }

            /**
             * Database Operations
             */
            
            // Check if item already exists in database
            $check_stmt = $db->prepare("SELECT id FROM inventory WHERE property_number = ?");
            $check_stmt->bind_param("s", $property_number);
            $check_stmt->execute();
            $exists = $check_stmt->get_result()->num_rows > 0;
            $check_stmt->close();

            // Prepare appropriate query based on whether item exists
            if ($exists) {
                // Update existing record
                $stmt = $db->prepare("
                    UPDATE inventory SET
                        article = ?,
                        description = ?,
                        model_number = ?,
                        acquisition_date = ?,
                        person_accountable = ?,
                        cost = ?,
                        equipment_type = ?,
                        remarks = ?,
                        signature_of_inventory_team_date = NOW()
                    WHERE property_number = ?
                ");
                $acquisition_date_str = $acquisition_date ? $acquisition_date->format('Y-m-d') : null;
                $stmt->bind_param(
                    "sssssdsss",
                    $article,
                    $description,
                    $model_number,
                    $acquisition_date_str,
                    $person_accountable,
                    $cost,
                    $equipment_type,
                    $remarks,
                    $property_number
                );
            } else {
                // Insert new record
                $stmt = $db->prepare("
                    INSERT INTO inventory 
                    (article, property_number, description, model_number, acquisition_date, 
                     person_accountable, cost, equipment_type, remarks)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $acquisition_date_str = $acquisition_date ? $acquisition_date->format('Y-m-d') : null;
                
                $stmt->bind_param(
                    "ssssssdss",
                    $article,
                    $property_number,
                    $description,
                    $model_number,
                    $acquisition_date_str,
                    $person_accountable,
                    $cost,
                    $equipment_type,
                    $remarks
                );
            }

            // Execute the query and handle results
            if ($stmt->execute()) {
                if ($exists) {
                    $updated_count++;
                    $logger->logAction(
                        'updated', 
                        $equipment_type, 
                        $property_number, 
                        $_SESSION['username'],
                        "Item updated via import"
                    );
                } else {
                    $imported_count++;
                    $logger->logAction(
                        'created', 
                        $equipment_type, 
                        $property_number, 
                        $_SESSION['username'],
                        "Item created via import"
                    );
                }
                
                // Generate QR code sticker for this item
                generateSticker($property_number);
                
                $success_rows[] = $row->getRowIndex();
            } else {
                $errors[] = "Row {$row->getRowIndex()}: Database error - " . $stmt->error;
                $skipped_count++;
            }
            $stmt->close();
        }

        $db->commit(); // Commit transaction if all went well
        
        // Prepare success message with counts
        $message = "Import completed: ";
        $message .= $imported_count > 0 ? "$imported_count new items added, " : "";
        $message .= $updated_count > 0 ? "$updated_count items updated, " : "";
        $message .= $skipped_count > 0 ? "$skipped_count rows skipped" : "";
        $message = rtrim($message, ", ");
        
        $_SESSION['import_success'] = $message;
        
        // Store errors if any occurred
        if (!empty($errors)) {
            $_SESSION['import_errors'] = array_slice($errors, 0, 50); // Limit to 50 errors
            $_SESSION['import_warning'] = count($errors) . " rows had issues during import.";
        }
        
    } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        $db->rollback(); // Rollback on spreadsheet error
        $_SESSION['import_error'] = "Error reading Excel file: " . $e->getMessage();
    } catch (Exception $e) {
        $db->rollback(); // Rollback on general error
        $_SESSION['import_error'] = "Import failed: " . $e->getMessage();
    }

    // Redirect back to landing page
    header("Location: /inventory-system/pages/landing.php");
    exit();
}
?>

<!-- HTML portion remains exactly the same as in your original file -->


<!DOCTYPE html>
<html>
<head>
    <title>Import Inventory from Excel</title>
    <style>
        /* CSS styles for the import page */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 40px;
            background-color: #f4f6f8;
            color: #333;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        a {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        .type-confirmation,
        .file-requirements,
        .duplicate-options,
        .upload-form,
        .instructions {
            background-color: #ffffff;
            border-left: 6px solid #3498db;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .type-confirmation {
            border-left-color: #4caf50;
        }

        .file-requirements {
            border-left-color: #2196f3;
        }

        .duplicate-options {
            border-left-color: #fbc02d;
        }

        h3 {
            margin-top: 0;
            font-size: 20px;
            color: #2c3e50;
        }

        ul, ol {
            margin-left: 20px;
            padding-left: 10px;
        }

        .upload-form input[type="file"] {
            display: block;
            margin-top: 10px;
            font-size: 15px;
        }

        button[type="submit"] {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #43a047;
        }

        #progressContainer {
            background: #e0e0e0;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
        }

        #progressBar {
            height: 24px;
            width: 0;
            background-color: #4CAF50;
            text-align: center;
            line-height: 24px;
            color: white;
            border-radius: 3px;
            transition: width 0.4s ease-in-out;
        }

        #importForm {
            margin-top: 20px;
        }

        #progressStatus {
            margin-top: 8px;
            font-size: 14px;
            color: #555;
            text-align: center;
        }

        div[style*="background: #fff3e0"] {
            background-color: #fff8e1 !important;
            border-left: 6px solid #ffb300;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        div[style*="background: #fff3e0"] h3 {
            color: #e65100;
        }

    </style>
</head>
<body>
    <h1>Import <?= htmlspecialchars($equipment_type) ?> Inventory</h1>
    <a href="/inventory-system/pages/landing.php" style="display: inline-block; margin-bottom: 20px;">← Back to Dashboard</a>
    
    <!-- Equipment type confirmation section -->
    <div class="type-confirmation">
        <h3 style="margin-top: 0;">Equipment Type Confirmation</h3>
        <p>All imported items will be categorized as: 
           <strong><?= htmlspecialchars($equipment_type) ?></strong></p>
        <p>If this is incorrect, <a href="equipment-type.php">go back and select a different type</a>.</p>
    </div>
    
    <!-- File requirements section -->
    <div class="file-requirements">
        <h3>File Requirements</h3>
        <p>Your Excel file must meet these requirements:</p>
        <ul>
            <li>File format: .xls or .xlsx</li>
            <li>First row must be column headers</li>
            <li>Columns must be in this exact order:</li>
        </ul>
        <ol>
            <li><strong>Article</strong> (required)</li>
            <li><strong>Acquisition Date</strong> (e.g. "September 27, 2024" or "09/27/2024")</li>
            <li><strong>Model Number</strong> (ICS NO.)</li>
            <li><strong>Property Number</strong> (required)</li>
            <li><strong>Description</strong> (Description)</li>
            <li><strong>Person Accountable</strong> (Office/Officer)</li>
            <li><strong>Cost</strong> (e.g. "36,862.81")</li>
        </ol>
    </div>
    
    <!-- Duplicate handling explanation -->
    <div class="duplicate-options">
        <h3>Duplicate Handling</h3>
        <p>When items with existing property numbers are found:</p>
        <ul>
            <li>Existing items will be <strong>updated</strong> with the new information</li>
            <li>The inventory team signature date will be automatically updated</li>
            <li>All fields except the property number will be overwritten</li>
        </ul>
    </div>
    
    <!-- File upload form -->
    <div class="upload-form">
        <form method="POST" enctype="multipart/form-data" id="importForm">
            <div>
                <label for="excel_file" style="display: block; margin-bottom: 5px; font-weight: bold;">
                    Select Excel File:
                </label>
                <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>
            </div>
            
            <!-- Progress bar (hidden by default) -->
            <div id="progressContainer" style="display: none; margin: 15px 0;">
                <div style="width: 100%; background-color: #f3f3f3; border-radius: 5px; overflow: hidden;">
                    <div id="progressBar" style="height: 24px; width: 0; background-color: #4CAF50; text-align: center; line-height: 24px; color: white;">0%</div>
                </div>
                <p id="progressStatus" style="margin-top: 5px; text-align: center;">Preparing import...</p>
            </div>
            <button type="submit" id="submitBtn" style="padding: 8px 15px; background: #4CAF50; color: white; border: none; border-radius: 4px;">
                Import <?= htmlspecialchars($equipment_type) ?> Items
            </button>
        </form>
    </div>
    
    <!-- JavaScript for progress bar animation -->
    <script>
        document.getElementById('importForm').addEventListener('submit', function(e) {
            // Show progress bar when form is submitted
            document.getElementById('progressContainer').style.display = 'block';
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').innerHTML = 'Processing...';
            
            // Simulate progress for import and sticker generation
            let progress = 0;
            const progressBar = document.getElementById('progressBar');
            const progressStatus = document.getElementById('progressStatus');
            
            const interval = setInterval(function() {
                // Increment progress with better distribution
                if (progress < 60) {
                    progress += 5;
                } else if (progress < 85) {
                    progress += 2;
                } else if (progress < 99) {
                    progress += 1;
                } else {
                    progress = 100;
                    clearInterval(interval);
                    progressStatus.innerHTML = "Finalizing... Please wait, this might take a few minutes.";
                }
                
                // Update progress status messages
                if (progress >= 70 && progress < 99) {
                    progressStatus.innerHTML = "Generating stickers...";
                } else if (progress >= 40 && progress < 70) {
                    progressStatus.innerHTML = "Processing data...";
                } else if (progress < 40) {
                    progressStatus.innerHTML = "Uploading file...";
                }
                
                progressBar.style.width = progress + '%';
                progressBar.innerHTML = progress + '%';
            }, 400);
        });
    </script>
    
    <!-- Important notes section -->
    <div style="margin-top: 30px; padding: 15px; background: #fff3e0; border-radius: 5px;">
        <h3>Important Notes</h3>
        <ul>
            <li>The import process may take several minutes for large files</li>
            <li>Do not close the browser during import</li>
            <li>You will receive a detailed summary after completion</li>
            <li>Existing items will be updated with the new data</li>
            <li>Stickers will be automatically generated for all imported items</li>
        </ul>
    </div>
</body>
</html>