<?php
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
        
        // Initialize counters
        $imported_count = 0;
        $updated_count = 0;
        $skipped_count = 0;
        $errors = [];
        $success_rows = [];
        $db->begin_transaction(); // Start database transaction

        // Iterate through each row in the Excel sheet
        foreach ($sheet->getRowIterator() as $row) {
            if ($row->getRowIndex() == 1) continue; // Skip header row

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true);
            
            // Get cell values
            $data = [];
            foreach ($cellIterator as $cell) {
                $data[] = $cell->getValue();
            }

            // Validate minimum columns exist
            if (count($data) < 6) {
                $errors[] = "Row {$row->getRowIndex()}: Insufficient columns (expected 6, found " . count($data) . ")";
                $skipped_count++;
                continue;
            }

            // Process and validate data from each cell
            $property_number = trim($data[2] ?? '');
            $description = trim($data[3] ?? '');
            $model_number = trim($data[1] ?? null);
            // Parse acquisition date in different formats
            $acquisition_date = !empty($data[0]) ? 
                (DateTime::createFromFormat('F j, Y', $data[0]) ?: 
                 DateTime::createFromFormat('m/d/Y', $data[0]) ?: null) : null;
            $person_accountable = trim($data[4] ?? null);
            // Format cost by removing commas and validating as float
            $cost = !empty($data[5]) ? filter_var(str_replace(',', '', $data[5]), FILTER_VALIDATE_FLOAT) : 0;
            $remarks = 'service';
            
            // Log equipment type for debugging
            error_log("Using equipment_type: " . $equipment_type . " for property number: " . $property_number);

            // Validate required fields
            $row_errors = [];
            if (empty($property_number)) {
                $row_errors[] = "Property number is required";
            }
            if (empty($description)) {
                $row_errors[] = "Description is required";
            }
            if ($cost === false) {
                $row_errors[] = "Invalid cost format";
            }

            if (!empty($row_errors)) {
                $errors[] = "Row {$row->getRowIndex()}: " . implode(', ', $row_errors);
                $skipped_count++;
                continue;
            }

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
                    "ssssdsss",
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
                if (empty($equipment_type)) {
                    $equipment_type = $_GET['type'] ?? 'Unknown';
                }
                
                $stmt = $db->prepare("
                    INSERT INTO inventory 
                    (property_number, description, model_number, acquisition_date, 
                     person_accountable, cost, equipment_type, remarks)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $acquisition_date_str = $acquisition_date ? $acquisition_date->format('Y-m-d') : null;
                
                // Debug log for equipment type
                error_log("Inserting new record with equipment_type: " . $equipment_type);
                
                $stmt->bind_param(
                    "sssssdss",
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


<!DOCTYPE html>
<html>
<head>
    <title>Import Inventory from Excel</title>
    <style>
        /* CSS styles for the import page */
        .instructions {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .instructions ol {
            margin-left: 20px;
        }
        .type-confirmation {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .file-requirements {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .upload-form {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .upload-form input[type="file"] {
            margin-bottom: 15px;
        }
        .duplicate-options {
            margin: 15px 0;
            padding: 15px;
            background: #fff3cd;
            border-radius: 5px;
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
            <li><strong>Acquisition Date</strong> (e.g. "September 27, 2024" or "09/27/2024")</li>
            <li><strong>Model Number</strong></li>
            <li><strong>Property Number</strong> (required)</li>
            <li><strong>Description</strong> (required)</li>
            <li><strong>Person Accountable</strong></li>
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
    
    <!-- Export functionality scripts -->
    <script>
        document.getElementById('exportBtn').addEventListener('click', function() {
            const exportContent = document.getElementById('exportContent').cloneNode(true);
            const buttons = exportContent.querySelectorAll('button');
            buttons.forEach(button => button.remove());

            const tempDiv = document.createElement('div');
            tempDiv.style.textAlign = 'center';
            tempDiv.appendChild(exportContent);
            document.body.appendChild(tempDiv);

            const wb = XLSX.utils.table_to_book(exportContent.querySelector('table'), {
                sheet: "Inventory",
                raw: true
            });

            const today = new Date();
            const dateString = today.getFullYear() + '-' + 
                              (today.getMonth() + 1).toString().padStart(2, '0') + '-' + 
                              today.getDate().toString().padStart(2, '0');
            const filename = `OCD_Inventory_Report_${dateString}.xlsx`;

            XLSX.writeFile(wb, filename);
            document.body.removeChild(tempDiv);
        });

        document.getElementById('exportPdfBtn').addEventListener('click', function () {
            const element = document.getElementById('exportContent');
            element.style.width = element.scrollWidth + 'px';
            element.style.transform = 'scale(0.84) translateX(-90px)';
            element.style.transformOrigin = 'center top';

            const opt = {
                margin: 0,
                filename: 'OCD_Inventory_Report.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: {
                    scale: 1,
                    scrollX: 0,
                    scrollY: -window.scrollY,
                    windowWidth: element.scrollWidth,
                    useCORS: true
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'landscape'
                }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                element.style.width = '';
                element.style.transform = '';
            });
        });

        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });

        document.getElementById('exportWordBtn').addEventListener('click', function() {
            const content = document.getElementById('exportContent').cloneNode(true);
            const buttons = content.querySelectorAll('button');
            buttons.forEach(button => button.remove());

            const wrapper = document.createElement('div');
            wrapper.style.textAlign = 'center';
            wrapper.appendChild(content);

            const converted = htmlDocx.asBlob(wrapper.innerHTML);
            saveAs(converted, 'OCD_Inventory_Report.docx');
        });
    </script>
</body>
</html>