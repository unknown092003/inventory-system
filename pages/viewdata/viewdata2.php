<!DOCTYPE html>
<html lang="en">
<head>
    <!-- BASIC PAGE METADATA -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RTP - Personnel Management</title>
    
    <!-- EXTERNAL RESOURCES -->
    <link rel="stylesheet" href="viewdata.css"> <!-- Main stylesheet -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script> <!-- For image export -->
    
    <!-- EMBEDDED STYLES -->
    <style>
        /* ========== DROPDOWN STYLES ========== */
        /* Settings and Export dropdown menus */
        .settings-dropdown, #exportDropdownMenu {
            display: none; /* Hidden by default */
            position: absolute; /* Positioned relative to parent */
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); /* Shadow effect */
            z-index: 1; /* Ensure it appears above other elements */
            border-radius: 4px;
            padding: 5px 0;
        }
        
        /* Dropdown button styles */
        .settings-dropdown button, #exportDropdownMenu button {
            width: 100%;
            text-align: left;
            padding: 8px 16px;
            border: none;
            background: none;
            cursor: pointer;
        }
        
        /* Dropdown button hover effect */
        .settings-dropdown button:hover, #exportDropdownMenu button:hover {
            background-color: #f1f1f1;
        }
        
        /* Container positioning */
        .settings-container, .export-dropdown {
            position: relative; /* Needed for absolute positioning of dropdown */
            display: inline-block;
        }
        
        /* Show dropdown when active class is added */
        .settings-container.active .settings-dropdown,
        .export-dropdown.active #exportDropdownMenu {
            display: block;
        }
        
        /* ========== FILTER PANEL STYLES ========== */
        .filter-panel {
            display: none;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .filter-panel.active {
            display: block;
        }
        
        /* Filter row layout */
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 10px;
        }
        
        /* Individual filter group styling */
        .filter-group {
            flex: 1;
            min-width: 150px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
        }
        
        /* Filter input styling */
        .filter-group select, 
        .filter-group input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        /* Filter action buttons */
        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        /* Filter toggle button */
        .filter-toggle {
            background-color: #f0f0f0;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
        }
        
        .filter-toggle:hover {
            background-color: #e0e0e0;
        }
        
        /* Toggle icon animation */
        .filter-toggle .icon {
            transition: transform 0.2s;
        }
        
        .filter-toggle.active .icon {
            transform: rotate(180deg);
        }
        
        /* ========== PRINT STYLES ========== */
        @media print {
            /* Hide elements during printing */
            .no-print, .form-container, .personnel-list, .remove-btn, 
            .settings-container, .export-dropdown, .sort-select, 
            #filterForm, #printBtn, .filter-panel, .filter-toggle {
                display: none !important;
            }
            body {
                background: #fff !important;
            }
            .main-nav {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- ========== MAIN NAVIGATION BAR ========== -->
    <nav class="main-nav">
        <div class="nav-links no-print">
            <div class="header-group">
                <div class="controls">
                    <!-- SETTINGS DROPDOWN -->
                    <div class="settings-container" id="settingsContainer">
                        <button class="settings-btn" id="settingsBtn">Settings</button>
                        <div class="settings-dropdown">
                            <button onclick="showForm('certifiedForm')">Add Certified By</button>
                            <button onclick="showForm('approvedForm')">Add Approved By</button>
                            <button onclick="showForm('verifiedForm')">Add Verified By</button>
                            <button onclick="window.print()">Print Document</button>
                        </div>
                    </div>
                    
                    <!-- FILTER TOGGLE BUTTON -->
                    <button class="filter-toggle" id="filterToggle">
                        <span>Filters</span>
                        <span class="icon">▼</span>
                    </button>
                    
                    <!-- COPY DROPDOWN -->
                    <div class="export-dropdown" id="exportDropdown">
                        <button id="exportDropdownBtn">Copy ▼</button>
                        <div id="exportDropdownMenu">
                            <button id="copyTableBtn">Copy Basic Table</button>
                            <button id="copyTableWithLayoutBtn">Copy Table with Layout</button>
                            <button id="copyAsImageBtn">Copy as Image</button>
                        </div>
                    </div>
                    
                    <!-- PRINT BUTTON -->
                    <button id="printBtn">Print</button>
                    
                    <!-- REGISTRY BUTTON -->
                    <button class="settings-btn" type="button" onclick="window.location.href='viewdata1.php'">Registry</button>
                </div>
            </div>
        </div>
    </nav>

    <!-- ========== FILTER PANEL ========== -->
    <div class="filter-panel" id="filterPanel">
        <div class="filter-row">
            <!-- SEARCH FILTER -->
            <div class="filter-group">
                <label>Search</label>
                <input type="text" id="searchInput" placeholder="Search all fields...">
            </div>
            
            <!-- EQUIPMENT TYPE FILTER -->
            <div class="filter-group">
                <label>Equipment Type</label>
                <select id="equipmentTypeFilter">
                    <option value="all">All Types</option>
                    <option value="Machinery">Machinery</option>
                    <option value="Construction">Construction</option>
                    <option value="ICT Equipment">ICT Equipment</option>
                    <option value="Communications">Communications</option>
                    <option value="Military/Security">Military/Security</option>
                    <option value="Office">Office</option>
                    <option value="DRRM">DRRM</option>
                    <option value="Furniture">Furniture</option>
                </select>
            </div>
            
            <!-- STATUS FILTER -->
            <div class="filter-group">
                <label>Status</label>
                <select id="remarksFilter">
                    <option value="all">All Status</option>
                    <option value="service">Serviceable</option>
                    <option value="unservice">Unserviceable</option>
                    <option value="disposed">Disposed</option>
                </select>
            </div>
        </div>
        
        <div class="filter-row">
            <!-- MONTH FILTER -->
            <div class="filter-group">
                <label>Month</label>
                <select id="monthFilter">
                    <option value="all">All Months</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </div>
            
            <!-- YEAR FILTER -->
            <div class="filter-group">
                <label>Year</label>
                <select id="yearFilter">
                    <option value="all">All Years</option>
                    <?php
                    // Generate year options from current year to 10 years back
                    $currentYear = date('Y');
                    for ($y = $currentYear; $y >= $currentYear - 10; $y--) {
                        echo "<option value=\"$y\">$y</option>";
                    }
                    ?>
                </select>
            </div>
            
            <!-- VALUE FILTER -->
            <div class="filter-group">
                <label>Value</label>
                <select id="valueFilter">
                    <option value="all">All Values</option>
                    <option value="high">High Value (≥₱5,000)</option>
                    <option value="low">Low Value (<₱5,000)</option>
                </select>
            </div>
        </div>
        
        <!-- FILTER ACTION BUTTONS -->
        <div class="filter-actions">
            <button onclick="applyFilters()">Apply Filters</button>
            <button onclick="resetFilters()">Reset Filters</button>
        </div>
    </div>

    <!-- ========== PERSONNEL FORMS ========== -->
    <!-- Certified By Form -->
    <div id="certifiedForm" class="form-container">
        <h3>Add Certified By Personnel</h3>
        <div class="form-group">
            <label for="certifiedName">Name:</label>
            <input type="text" id="certifiedName" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="certifiedPosition">Position:</label>
            <input type="text" id="certifiedPosition" placeholder="Enter position">
        </div>
        <button class="form-btn" onclick="addCertifiedPerson()">Add Person</button>
        <button class="form-btn" onclick="hideForms()">Close</button>
        <div class="personnel-list" id="certifiedList"></div>
    </div>

    <!-- Approved By Form -->
    <div id="approvedForm" class="form-container">
        <h3>Add Approved By Personnel</h3>
        <div class="form-group">
            <label for="approvedName">Name:</label>
            <input type="text" id="approvedName" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="approvedPosition">Position:</label>
            <input type="text" id="approvedPosition" placeholder="Enter position">
        </div>
        <button class="form-btn" onclick="addApprovedPerson()">Add Person</button>
        <button class="form-btn" onclick="hideForms()">Close</button>
        <div class="personnel-list" id="approvedList"></div>
    </div>

    <!-- Verified By Form -->
    <div id="verifiedForm" class="form-container">
        <h3>Add Verified By Personnel</h3>
        <div class="form-group">
            <label for="verifiedName">Name:</label>
            <input type="text" id="verifiedName" placeholder="Enter name">
        </div>
        <div class="form-group">
            <label for="verifiedPosition">Position:</label>
            <input type="text" id="verifiedPosition" placeholder="Enter position">
        </div>
        <button class="form-btn" onclick="updateVerifiedPerson()">Update</button>
        <button class="form-btn" onclick="hideForms()">Close</button>
    </div>

    <!-- ========== MAIN INVENTORY TABLE ========== -->
    <div class="rtp_table">
        <table>
            <!-- TABLE HEADER -->
            <tr>
                <th colspan="11" style="border:none">
                    <div class="headerlogo">
                        <div class="ocd-logo">
                            <img src="/inventory-system/public/img/ocd.png" alt="OCD Logo">
                        </div>
                        <div class="ocd-text">
                            <h4>Republic of the Philippines</h4>
                            <h4>Department of National Defense</h4>
                            <h1>OFFICE OF CIVIL DEFENSE</h1>
                            <h2>CORDILLERA ADMINISTRATIVE REGION</h2>
                            <h5>NO. 55 First Road, Quazon HILL PROPER, BAGUIO CITY, 2600</h5>
                        </div>
                        <div class="bp-logo">
                            <img src="/inventory-system/public/img/bp.png" alt="BP Logo">
                        </div>
                    </div>
                    <div class="headertype">
                        <h3>REPORT ON THE PHYSICAL COUNT OF PROPERTY, PLANT AND EQUIPMENT</h3>
                        <p><strong>Information, Communication and Technology Equipment</strong></p>
                        <p>As of (date)</p>
                    </div>
                </th>
            </tr>
            <!-- COLUMN HEADERS -->
            <tr>
                <th rowspan="2">Article</th>
                <th rowspan="2">Description</th>
                <th rowspan="2">Acquisition Date</th>
                <th rowspan="2">New Property Number</th>
                <th rowspan="2">Unit of Measure</th>
                <th rowspan="2">Unit Value</th>
                <th rowspan="2">Quantity per Property Card</th>
                <th rowspan="2">Quantity per Physical Count</th>
                <th colspan="2">Shortage/Overage</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                <th>Quantity</th>
                <th>Value</th>
            </tr>
            
            <!-- TABLE DATA (DYNAMICALLY GENERATED FROM DATABASE) -->
            <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "inventory_system");
                if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

                // Build WHERE clause based on filters
                $where = [];
                if (!empty($_GET['monthPicker'])) {
                    $monthYear = $_GET['monthPicker'];
                    $where[] = "acquisition_date LIKE '" . $conn->real_escape_string($monthYear) . "%'";
                }
                $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

                // Query database
                $sql = "SELECT * FROM inventory $whereClause";
                $result = $conn->query($sql);

                $totalUnitValue = 0;
                if ($result && $result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Add data attributes for filtering
                        echo "<tr data-equipment='".htmlspecialchars($row['equipment_type'] ?? '')."' 
                                  data-status='".htmlspecialchars($row['remarks'])."'
                                  data-date='".htmlspecialchars($row['acquisition_date'])."'
                                  data-value='".htmlspecialchars($row['cost'])."'>";
                        echo "<td>" . htmlspecialchars($row['article']) . "</td>"; // Article
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>"; // Description
                        echo "<td>" . htmlspecialchars($row['acquisition_date']) . "</td>"; // Acquisition Date
                        echo "<td>" . htmlspecialchars($row['property_number']) . "</td>"; // Property Number
                        echo "<td></td>"; // Unit of Measure
                        echo "<td>" . htmlspecialchars($row['cost']) . "</td>"; // Unit Value
                        $unitValue = floatval($row['cost']);
                        $totalUnitValue += $unitValue;
                        echo "<td></td>"; // Quantity per Property Card
                        echo "<td></td>"; // Quantity per Physical Count
                        echo "<td></td>"; // Shortage/Overage Quantity
                        echo "<td></td>"; // Shortage/Overage Value
                        echo "<td>" . htmlspecialchars($row['remarks']) . "</td>"; // Remarks
                        echo "</tr>";
                    }
                    // TOTAL ROW
                    echo "<tr style='font-weight:bold; background:#f2f2f2'>";
                    echo "<td colspan='4' style='text-align:right;'>TOTAL</td>";
                    echo "<td></td>";
                    echo "<td>" . number_format($totalUnitValue, 2) . "</td>";
                    echo "<td colspan='5'></td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='11'>No inventory items found</td></tr>";
                }
                $conn->close();
            ?>
        </table>
    </div>

    <!-- ========== APPROVAL SIGNATURE AREAS ========== -->
    <div class="approved-section">
        <div class="signature-box">
            <p>Certified Correct by:</p>
            <div class="people-container" id="certifiedDisplay">
                <div class="person-item">
                    <div class="signature-person">[No one certified yet]</div>
                </div>
            </div>
        </div>
        <div class="signature-box">
            <p>Approved by:</p>
            <div class="people-container" id="approvedDisplay">
                <div class="person-item">
                    <div class="signature-person">[No one approved yet]</div>
                </div>
            </div>
        </div>
        <div class="signature-box">
            <p>Verified by:</p>
            <div class="people-container" id="verifiedDisplay">
                <div class="person-item">
                    <div class="signature-person">[No one verified yet]</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== JAVASCRIPT ========== -->
    <script>
        // GLOBAL VARIABLES
        let certifiedPersonnel = []; // Stores certified personnel data
        let approvedPersonnel = []; // Stores approved personnel data
        let verifiedPerson = { name: '', position: '' }; // Stores verified person data

        // ========== FORM MANAGEMENT FUNCTIONS ==========
        /**
         * Shows a specific form and hides others
         * @param {string} formId - The ID of the form to show
         */
        function showForm(formId) {
            hideForms();
            document.getElementById(formId).classList.add('active-form');
        }

        /**
         * Hides all personnel forms
         */
        function hideForms() {
            const forms = document.querySelectorAll('.form-container');
            forms.forEach(form => {
                form.classList.remove('active-form');
            });
        }

        // ========== PERSONNEL MANAGEMENT FUNCTIONS ==========
        /**
         * Adds a new certified person to the list
         */
        function addCertifiedPerson() {
            const name = document.getElementById('certifiedName').value;
            const position = document.getElementById('certifiedPosition').value;
            if (name && position) {
                certifiedPersonnel.push({ name, position });
                updateCertifiedDisplay();
                updateCertifiedList();
                document.getElementById('certifiedName').value = '';
                document.getElementById('certifiedPosition').value = '';
            } else {
                alert('Please enter both name and position');
            }
        }

        /**
         * Adds a new approved person to the list
         */
        function addApprovedPerson() {
            const name = document.getElementById('approvedName').value;
            const position = document.getElementById('approvedPosition').value;
            if (name && position) {
                approvedPersonnel.push({ name, position });
                updateApprovedDisplay();
                updateApprovedList();
                document.getElementById('approvedName').value = '';
                document.getElementById('approvedPosition').value = '';
            } else {
                alert('Please enter both name and position');
            }
        }

        /**
         * Updates the verified person information
         */
        function updateVerifiedPerson() {
            const name = document.getElementById('verifiedName').value;
            const position = document.getElementById('verifiedPosition').value;
            if (name && position) {
                verifiedPerson = { name, position };
                updateVerifiedDisplay();
                document.getElementById('verifiedName').value = '';
                document.getElementById('verifiedPosition').value = '';
                hideForms();
            } else {
                alert('Please enter both name and position');
            }
        }

        /**
         * Removes a certified person from the list
         * @param {number} index - The index of the person to remove
         */
        function removeCertified(index) {
            certifiedPersonnel.splice(index, 1);
            updateCertifiedDisplay();
            updateCertifiedList();
        }

        /**
         * Removes an approved person from the list
         * @param {number} index - The index of the person to remove
         */
        function removeApproved(index) {
            approvedPersonnel.splice(index, 1);
            updateApprovedDisplay();
            updateApprovedList();
        }

        // ========== DISPLAY UPDATE FUNCTIONS ==========
        /**
         * Updates the certified personnel display area
         */
        function updateCertifiedDisplay() {
            const display = document.getElementById('certifiedDisplay');
            display.innerHTML = certifiedPersonnel.length === 0 ? 
                '<div class="person-item"><div class="signature-person">[No one certified yet]</div></div>' :
                certifiedPersonnel.map(person => 
                    `<div class="person-item">
                        <div class="signature-person">${person.name}<br><hr>${person.position}</div>
                    </div>`
                ).join('');
        }

        /**
         * Updates the approved personnel display area
         */
        function updateApprovedDisplay() {
            const display = document.getElementById('approvedDisplay');
            display.innerHTML = approvedPersonnel.length === 0 ? 
                '<div class="person-item"><div class="signature-person">[No one approved yet]</div></div>' :
                approvedPersonnel.map(person => 
                    `<div class="person-item">
                        <div class="signature-person">${person.name}<br><hr>${person.position}</div>
                    </div>`
                ).join('');
        }

        /**
         * Updates the verified personnel display area
         */
        function updateVerifiedDisplay() {
            const display = document.getElementById('verifiedDisplay');
            display.innerHTML = '';
            
            if (!verifiedPerson.name) {
                display.innerHTML = `
                    <div class="person-item">
                        <div class="signature-person">[No one verified yet]</div>
                    </div>
                `;
                return;
            }
            
            const personElement = document.createElement('div');
            personElement.className = 'person-item';
            personElement.innerHTML = `
                <div class="signature-person">
                    ${verifiedPerson.name}<br><hr>${verifiedPerson.position}
                </div>
            `;
            display.appendChild(personElement);
        }

        /**
         * Updates the certified personnel list in the form
         */
        function updateCertifiedList() {
            const list = document.getElementById('certifiedList');
            list.innerHTML = '';
            
            certifiedPersonnel.forEach((person, index) => {
                const item = document.createElement('div');
                item.className = 'personnel-item';
                item.innerHTML = `
                    <span>${person.name} - ${person.position}</span>
                    <button class="remove-btn" onclick="removeCertified(${index})">×</button>
                `;
                list.appendChild(item);
            });
        }

        /**
         * Updates the approved personnel list in the form
         */
        function updateApprovedList() {
            const list = document.getElementById('approvedList');
            list.innerHTML = '';
            
            approvedPersonnel.forEach((person, index) => {
                const item = document.createElement('div');
                item.className = 'personnel-item';
                item.innerHTML = `
                    <span>${person.name} - ${person.position}</span>
                    <button class="remove-btn" onclick="removeApproved(${index})">×</button>
                `;
                list.appendChild(item);
            });
        }

        // ========== FILTER FUNCTIONS ==========
        /**
         * Toggles the filter panel visibility
         */
        function toggleFilterPanel() {
            const panel = document.getElementById('filterPanel');
            const toggle = document.getElementById('filterToggle');
            panel.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        /**
         * Applies all active filters to the table
         */
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const equipmentType = document.getElementById('equipmentTypeFilter').value.toLowerCase();
            const remarksFilter = document.getElementById('remarksFilter').value.toLowerCase();
            const month = document.getElementById('monthFilter').value;
            const year = document.getElementById('yearFilter').value;
            const valueFilter = document.getElementById('valueFilter').value;

            const rows = document.querySelectorAll('.rtp_table table tr');
            
            for (let i = 3; i < rows.length; i++) {
                const row = rows[i];
                // Skip non-data rows and the total row
                if (row.cells.length < 6 || row.style.fontWeight === 'bold') continue;
                
                // Get row data from cells and data attributes
                const rowData = {
                    description: row.cells[1].textContent.toLowerCase(),
                    acquisitionDate: row.cells[2].textContent,
                    cost: parseFloat(row.cells[5].textContent.replace(/[^0-9.-]/g, '')) || 0,
                    remarks: row.cells[10].textContent.toLowerCase(),
                    equipment: row.getAttribute('data-equipment')?.toLowerCase() || '',
                    status: row.getAttribute('data-status')?.toLowerCase() || ''
                };

                // Parse date from acquisition date
                let rowMonth = '';
                let rowYear = '';
                try {
                    const date = new Date(rowData.acquisitionDate);
                    rowMonth = (date.getMonth() + 1).toString().padStart(2, '0');
                    rowYear = date.getFullYear().toString();
                } catch (e) {
                    console.error("Error parsing date:", rowData.acquisitionDate);
                }
                
                // Check each filter condition
                const matchesSearch = searchTerm === '' || 
                                    rowData.description.includes(searchTerm) || 
                                    rowData.acquisitionDate.toLowerCase().includes(searchTerm) || 
                                    rowData.remarks.includes(searchTerm);
                
                const matchesEquipment = equipmentType === 'all' || 
                                       rowData.equipment.includes(equipmentType) || 
                                       rowData.description.includes(equipmentType);
                
                const matchesStatus = remarksFilter === 'all' || 
                                    (remarksFilter === 'service' && rowData.status.includes('service')) ||
                                    (remarksFilter === 'unservice' && (rowData.status.includes('unservice') || rowData.status.includes('not service'))) ||
                                    (remarksFilter === 'disposed' && rowData.status.includes('dispose'));
                
                const matchesMonth = month === 'all' || rowMonth === month;
                const matchesYear = year === 'all' || rowYear === year;
                
                let matchesValue = true;
                if (valueFilter !== 'all') {
                    if (valueFilter === 'high') {
                        matchesValue = rowData.cost >= 5000;
                    } else if (valueFilter === 'low') {
                        matchesValue = rowData.cost > 0 && rowData.cost < 5000;
                    }
                }
                
                // Show/hide row based on filter matches
                row.style.display = (matchesSearch && matchesEquipment && matchesStatus && 
                                    matchesMonth && matchesYear && matchesValue) ? '' : 'none';
            }
            
            // Ensure TOTAL row is always visible
            const totalRow = document.querySelector('.rtp_table table tr[style*="font-weight:bold"]');
            if (totalRow) totalRow.style.display = '';
        }

        /**
         * Resets all filters to their default state
         */
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('equipmentTypeFilter').value = 'all';
            document.getElementById('remarksFilter').value = 'all';
            document.getElementById('monthFilter').value = 'all';
            document.getElementById('yearFilter').value = 'all';
            document.getElementById('valueFilter').value = 'all';
            
            // Show all rows
            const rows = document.querySelectorAll('.rtp_table table tr');
            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = '';
            }
        }

        // ========== COPY FUNCTIONALITY ==========
        /**
         * Copies the basic table structure to clipboard (text only)
         */
        function copyTableToClipboard() {
            const table = document.querySelector('.rtp_table table');
            const range = document.createRange();
            range.selectNode(table);
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);
            
            try {
                const successful = document.execCommand('copy');
                alert(successful ? 'Basic table copied!' : 'Unable to copy. Please try Ctrl+C after selecting.');
            } catch (err) {
                alert('Error copying: ' + err);
            }
            window.getSelection().removeAllRanges();
        }

        /**
         * Copies the table with layout and formatting to clipboard
         */
        async function copyTableWithLayout() {
            try {
                const tableSection = document.querySelector('.rtp_table').cloneNode(true);
                const images = tableSection.querySelectorAll('img');
                
                // Convert images to base64 for Excel compatibility
                for (const img of images) {
                    await convertImgToBase64(img.src, function(base64) {
                        img.src = base64;
                    });
                }
                
                // Create Excel-compatible header structure
                const headerHTML = `
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:20%; vertical-align:middle; text-align:center;">
                            ${tableSection.querySelector('.ocd-logo').outerHTML}
                        </td>
                        <td style="width:60%; vertical-align:middle; text-align:center;">
                            ${tableSection.querySelector('.ocd-text').outerHTML}
                        </td>
                        <td style="width:20%; vertical-align:middle; text-align:center;">
                            ${tableSection.querySelector('.bp-logo').outerHTML}
                        </td>
                    </tr>
                </table>
                ${tableSection.querySelector('.headertype').outerHTML}
                `;
                
                // Replace original header with Excel-compatible version
                const headerContainer = tableSection.querySelector('th[colspan="11"]');
                headerContainer.innerHTML = headerHTML;
                
                // Create HTML with Excel-specific markup
                const html = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" 
                      xmlns:x="urn:schemas-microsoft-com:office:excel"
                      xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <!--[if gte mso 9]>
                    <xml>
                        <x:ExcelWorkbook>
                            <x:ExcelWorksheets>
                                <x:ExcelWorksheet>
                                    <x:Name>Inventory</x:Name>
                                    <x:WorksheetOptions>
                                        <x:DisplayGridlines/>
                                    </x:WorksheetOptions>
                                </x:ExcelWorksheet>
                            </x:ExcelWorksheets>
                        </x:ExcelWorkbook>
                    </xml>
                    <![endif]-->
                    <style>
                        td {mso-number-format:\@;}
                        br {mso-data-placement:same-cell;}
                        .headerlogo { display: table; width: 100%; }
                        .headerlogo > div { display: table-cell; vertical-align: middle; }
                        .ocd-logo, .bp-logo { width: 20%; text-align: center; }
                        .ocd-text { width: 60%; text-align: center; }
                        img { max-height: 80px; width: auto; }
                        table { border-collapse: collapse; width: 100%; }
                    </style>
                </head>
                <body>
                    ${tableSection.innerHTML}
                </body>
                </html>
                `;
                
                // Copy to clipboard as both HTML and plain text
                const blob = new Blob([html], {type: 'text/html'});
                await navigator.clipboard.write([
                    new ClipboardItem({
                        'text/html': blob,
                        'text/plain': new Blob([tableSection.innerText], {type: 'text/plain'})
                    })
                ]);
                alert('Table with layout copied! Paste into Excel to preserve formatting.');
            } catch (err) {
                console.error('Copy failed:', err);
                alert('Could not copy with layout. Try the basic copy or image options.');
            }
        }

        /**
         * Copies the table as an image to clipboard
         */
        function copyAsImage() {
            html2canvas(document.querySelector('.rtp_table'), {
                logging: false,
                useCORS: true,
                allowTaint: true,
                scale: 2 // Higher quality
            }).then(canvas => {
                canvas.toBlob(blob => {
                    navigator.clipboard.write([
                        new ClipboardItem({ 'image/png': blob })
                    ]).then(() => {
                        alert('Image copied! Paste anywhere as a picture.');
                    }).catch(err => {
                        console.error('Image copy failed:', err);
                        alert('Could not copy image. Try saving it instead.');
                    });
                }, 'image/png');
            });
        }

        /**
         * Converts an image URL to base64 format
         * @param {string} url - The image URL to convert
         * @param {function} callback - Function to call with the base64 result
         */
        function convertImgToBase64(url, callback) {
            return new Promise((resolve, reject) => {
                if (url.startsWith('data:')) {
                    callback(url);
                    return resolve();
                }
                
                const img = new Image();
                img.crossOrigin = 'Anonymous';
                
                img.onload = function() {
                    try {
                        const canvas = document.createElement('canvas');
                        canvas.width = this.naturalWidth;
                        canvas.height = this.naturalHeight;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(this, 0, 0);
                        callback(canvas.toDataURL('image/png'));
                        resolve();
                    } catch (err) {
                        console.error('Image conversion error:', err);
                        callback(url);
                        resolve();
                    }
                };
                
                img.onerror = function() {
                    console.warn('Could not load image:', url);
                    callback(url);
                    resolve();
                };
                
                img.src = url;
            });
        }

        // ========== INITIALIZATION ==========
        /**
         * Initializes the application when DOM is loaded
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize dropdown toggles
            const settingsBtn = document.getElementById('settingsBtn');
            const settingsContainer = document.getElementById('settingsContainer');
            const exportDropdownBtn = document.getElementById('exportDropdownBtn');
            const exportDropdown = document.getElementById('exportDropdown');
            
            // Settings dropdown toggle
            settingsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                settingsContainer.classList.toggle('active');
                exportDropdown.classList.remove('active');
            });
            
            // Export dropdown toggle
            exportDropdownBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                exportDropdown.classList.toggle('active');
                settingsContainer.classList.remove('active');
            });
            
            // Close dropdowns when clicking elsewhere
            document.addEventListener('click', function() {
                settingsContainer.classList.remove('active');
                exportDropdown.classList.remove('active');
            });
            
            // Prevent dropdown from closing when clicking inside
            document.querySelector('.settings-dropdown').addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            document.getElementById('exportDropdownMenu').addEventListener('click', function(e) {
                e.stopPropagation();
            });

            // Initialize displays
            updateCertifiedDisplay();
            updateApprovedDisplay();
            updateVerifiedDisplay();
            
            // Initialize filter toggle
            document.getElementById('filterToggle').addEventListener('click', toggleFilterPanel);
            
            // Initialize filter inputs
            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('equipmentTypeFilter').addEventListener('change', applyFilters);
            document.getElementById('remarksFilter').addEventListener('change', applyFilters);
            document.getElementById('monthFilter').addEventListener('change', applyFilters);
            document.getElementById('yearFilter').addEventListener('change', applyFilters);
            document.getElementById('valueFilter').addEventListener('change', applyFilters);

            // Print button
            document.getElementById('printBtn').addEventListener('click', function() {
                window.print();
            });

            // Copy functionality
            document.getElementById('copyTableBtn').addEventListener('click', copyTableToClipboard);
            document.getElementById('copyTableWithLayoutBtn').addEventListener('click', copyTableWithLayout);
            document.getElementById('copyAsImageBtn').addEventListener('click', copyAsImage);
        });
    </script>
</body>
</html>