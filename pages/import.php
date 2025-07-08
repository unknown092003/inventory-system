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
        $db->beginTransaction(); // Start database transaction

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
            $article = isset($data[$columns['article']]) ? trim($data[$columns['article']]) : null;

            // Process acquisition date if available
            if (isset($data[$columns['acquisition_date']]) && !empty(trim($data[$columns['acquisition_date']]))) {
                $dateValue = trim($data[$columns['acquisition_date']]);
                if (is_numeric($dateValue)) {
                    // Handle Excel serial date
                    $acquisition_date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                } else {
                    // Try multiple formats
                    $formats = ['F j, Y', 'F d Y', 'm/d/Y', 'Y-m-d'];
                    $acquisition_date = null;
                    foreach ($formats as $format) {
                        $tryValue = $dateValue;
                        if (strpos($format, 'F') !== false) {
                            $tryValue = ucwords(strtolower($dateValue)); // Capitalize month
                        }
                        $acquisition_date = DateTime::createFromFormat($format, $tryValue);
                        if ($acquisition_date !== false) break;
                    }
                }
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
            $check_stmt->execute([$property_number]);
            $exists = $check_stmt->rowCount() > 0;

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
                $stmt->execute([
                    $article,
                    $description,
                    $model_number,
                    $acquisition_date_str,
                    $person_accountable,
                    $cost,
                    $equipment_type,
                    $remarks,
                    $property_number
                ]);
            } else {
                // Insert new record
                $stmt = $db->prepare("
                    INSERT INTO inventory 
                    (article, property_number, description, model_number, acquisition_date, 
                     person_accountable, cost, equipment_type, remarks)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $acquisition_date_str = $acquisition_date ? $acquisition_date->format('Y-m-d') : null;
                $stmt->execute([
                    $article,
                    $property_number,
                    $description,
                    $model_number,
                    $acquisition_date_str,
                    $person_accountable,
                    $cost,
                    $equipment_type,
                    $remarks
                ]);
            }

            // Execute the query and handle results
            if ($stmt->rowCount() > 0) {
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
                $errors[] = "Row {$row->getRowIndex()}: Database error - " . $stmt->errorInfo()[2];
                $skipped_count++;
            }
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
        $db->rollBack(); // Rollback on spreadsheet error
        $_SESSION['import_error'] = "Error reading Excel file: " . $e->getMessage();
    } catch (Exception $e) {
        $db->rollBack(); // Rollback on general error
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Senior Web Designer Styles */
        :root {
            --primary-color: #4a90e2;
            --secondary-color: #50e3c2;
            --text-color: #4a4a4a;
            --bg-color: #f7f9fc;
            --card-bg: #ffffff;
            --border-color: #e6e6e6;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.07);
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 40px;
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        .main-container {
            display: flex;
            gap: 40px;
            width: 100%;
            max-width: 1200px;
        }
        .import-panel, .guide-panel {
            background: var(--card-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            flex-grow: 1;
        }
        .import-panel {
            flex-basis: 60%;
        }
        .guide-panel {
            flex-basis: 40%;
            position: sticky;
            top: 40px;
        }
        h1, h2, h3 {
            color: #333;
            font-weight: 600;
        }
        h1 {
            font-size: 28px;
            margin: 0 0 10px 0;
        }
        .breadcrumb {
            font-size: 14px;
            margin-bottom: 30px;
        }
        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
        }
        .file-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #fdfdfd;
        }
        .file-upload-area.drag-over {
            border-color: var(--primary-color);
            background-color: #f1f7ff;
        }
        .file-upload-area input[type="file"] {
            display: none;
        }
        .file-upload-icon {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
        .file-upload-text {
            font-size: 18px;
            font-weight: 500;
        }
        .file-info {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        #fileName {
            font-weight: 600;
            color: var(--primary-color);
        }
        .submit-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            margin-top: 30px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.4);
        }
        .submit-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            box-shadow: none;
        }
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 7px 20px rgba(74, 144, 226, 0.5);
        }
        #progressContainer {
            margin-top: 20px;
            display: none;
        }
        #progressBar {
            height: 12px;
            width: 0;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border-radius: 6px;
            transition: width 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }
        #progressStatus {
            margin-top: 10px;
            font-size: 14px;
            text-align: center;
        }
        .guide-panel h2 {
            font-size: 22px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .guide-section {
            margin-bottom: 30px;
        }
        .guide-section h3 {
            font-size: 18px;
            margin-bottom: 10px;
        }
        .guide-section ul, .guide-section ol {
            padding-left: 20px;
            line-height: 1.8;
        }
        .guide-section code {
            background-color: #eef2f7;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'SF Mono', 'Consolas', monospace;
        }
        .download-btn {
            display: block;
            background-color: #eef2f7;
            color: var(--primary-color);
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            margin-top: 10px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        .download-btn:hover {
            background-color: #e6f0ff;
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="import-panel">
            <div class="breadcrumb">
                <a href="/inventory-system/pages/landing.php">Dashboard</a> / Import
            </div>
            <h1>Import Inventory</h1>
            <p>Importing for: <strong><?= htmlspecialchars($equipment_type) ?></strong>. (<a href="equipment-type.php">Change</a>)</p>

            <form method="POST" enctype="multipart/form-data" id="importForm">
                <label for="excel_file" class="file-upload-area" id="fileUploadArea">
                    <div class="file-upload-icon">📤</div>
                    <div class="file-upload-text">Click to browse or drag & drop your file</div>
                    <div class="file-info">
                        <span id="fileName"></span>
                        <p style="font-size: 12px; color: #888;">Supports .xls and .xlsx files</p>
                    </div>
                </label>
                <input type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx" required>

                <button type="submit" id="submitBtn" class="submit-btn" disabled>
                    Import Items
                </button>

                <div id="progressContainer">
                    <div style="width: 100%; background-color: #e0e0e0; border-radius: 6px;">
                        <div id="progressBar"></div>
                    </div>
                    <p id="progressStatus">Starting import...</p>
                </div>
            </form>
        </div>

        <div class="guide-panel">
            <h2>Import Guide</h2>
            <div class="guide-section">
                <h3>Download Template</h3>
                <p>Use our template to ensure your data is formatted correctly.</p>
                <a href="../templates/import-template.xlsx" class="download-btn" download>
                    Download Excel Template
                </a>
            </div>
            <div class="guide-section">
                <h3>Column Order</h3>
                <p>Your file must contain these columns in the exact order:</p>
                <ol>
                    <li><code>Article</code> (Optional)</li>
                    <li><code>Acquisition Date</code></li>
                    <li><code>Model Number</code></li>
                    <li><code>Property Number</code> (Required)</li>
                    <li><code>Description</code> (Required)</li>
                    <li><code>Person Accountable</code></li>
                    <li><code>Cost</code></li>
                </ol>
            </div>
            <div class="guide-section">
                <h3>Duplicate Handling</h3>
                <p>The <code>Property Number</code> is the unique identifier. If a number already exists, the system will <strong>update</strong> the existing record instead of creating a new one.</p>
            </div>
            <div class="guide-section">
    <h3>Important Notes</h3>
    <ul>
        <li>
            The supported date formats in Excel include:
            <ul>
                <li>june 09 2003</li>
                <li>June 09 2003</li>
                <li>June 9, 2003</li>
                <li>06/09/2003</li>
                <li>2003-06-09</li>
                <li>Excel serial date numbers</li>
            </ul>
        </li>
        <li>Large files may take a few minutes to process.</li>
        <li>Please don't close this window during the import.</li>
        <li>QR codes are generated automatically for all items.</li>
    </ul>
</div>

        </div>
    </div>

    <script>
        const fileInput = document.getElementById('excel_file');
        const fileUploadArea = document.getElementById('fileUploadArea');
        const fileNameDisplay = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');
        const importForm = document.getElementById('importForm');

        // Drag and drop functionality
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, () => fileUploadArea.classList.add('drag-over'), false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadArea.addEventListener(eventName, () => fileUploadArea.classList.remove('drag-over'), false);
        });
        fileUploadArea.addEventListener('drop', (e) => {
            fileInput.files = e.dataTransfer.files;
            handleFileSelect();
        });

        fileInput.addEventListener('change', handleFileSelect);

        function handleFileSelect() {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = `File: ${fileInput.files[0].name}`;
                submitBtn.disabled = false;
            } else {
                fileNameDisplay.textContent = '';
                submitBtn.disabled = true;
            }
        }

        importForm.addEventListener('submit', function(e) {
            document.getElementById('progressContainer').style.display = 'block';
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Importing...';

            let progress = 0;
            const progressBar = document.getElementById('progressBar');
            const progressStatus = document.getElementById('progressStatus');

            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress > 100) progress = 100;
                
                progressBar.style.width = progress + '%';

                if (progress < 30) progressStatus.textContent = 'Uploading file...';
                else if (progress < 70) progressStatus.textContent = 'Processing data rows...';
                else if (progress < 95) progressStatus.textContent = 'Generating QR codes...';
                else progressStatus.textContent = 'Finalizing import...';

                if (progress === 100) {
                    clearInterval(interval);
                }
            }, 300);
        });
    </script>
</body>
</html>