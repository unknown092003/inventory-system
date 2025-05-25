<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Advanced Inventory System</title>
<link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/create.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Inventory Manager</h1>
      <button class="theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
      </button>
    </header>

    <div class="steps">
      <div class="step active" id="step1-indicator">
        <div class="step-indicator">1</div>
        <div class="step-label">Select Type</div>
      </div>
      <div class="step" id="step2-indicator">
        <div class="step-indicator">2</div>
        <div class="step-label">Choose Action</div>
      </div>
      <div class="step" id="step3-indicator">
        <div class="step-indicator">3</div>
        <div class="step-label">Enter Details</div>
      </div>
    </div>

    <!-- Step 1: Equipment Selection -->
    <div id="step1">
      <h2>What are you adding to inventory?</h2>
      <div class="card-grid">
        <div class="card fade-in" onclick="selectType('Machinery')">
          <i class="fas fa-cogs"></i>
          <h3>Machinery</h3>
        </div>
        <div class="card fade-in" onclick="selectType('Construction')">
          <i class="fas fa-truck-pickup"></i>
          <h3>Construction</h3>
        </div>
        <div class="card fade-in" onclick="selectType('ICT')">
          <i class="fas fa-server"></i>
          <h3>ICT Equipment</h3>
        </div>
        <div class="card fade-in" onclick="selectType('Communications')">
          <i class="fas fa-satellite-dish"></i>
          <h3>Communications</h3>
        </div>
        <div class="card fade-in" onclick="selectType('Military')">
          <i class="fas fa-shield-alt"></i>
          <h3>Military/Security</h3>
        </div>
        <div class="card fade-in" onclick="selectType('Office')">
          <i class="fas fa-print"></i>
          <h3>Office</h3>
        </div>
        <div class="card fade-in" onclick="selectType('DRRM')">
          <i class="fas fa-first-aid"></i>
          <h3>DRRM</h3>
        </div>
        <div class="card fade-in" onclick="selectType('Furniture')">
          <i class="fas fa-couch"></i>
          <h3>Furniture</h3>
        </div>
      </div>
    </div>

    <!-- Step 2: Action Selection -->
    <div id="step2" class="hidden">
      <h2>How would you like to proceed?</h2>
      <p class="text-center" style="margin-bottom: 2rem;">You selected: <strong id="selected-type"></strong></p>
      
      <div class="action-buttons">
        <button class="btn btn-outline" onclick="showImport()">
          <i class="fas fa-file-import"></i> Import Data
        </button>
        <button class="btn btn-primary" onclick="showForm()">
          <i class="fas fa-plus-circle"></i> Create New
        </button>
      </div>

      <button class="btn btn-outline" onclick="backToStep1()" style="margin: 0 auto; display: block;">
        <i class="fas fa-arrow-left"></i> Back
      </button>
    </div>

    <!-- Step 3: Form -->
    <div id="step3" class="hidden">
      <div class="form-container fade-in">
        <h2>Add New <span id="form-type"></span></h2>
        
        <form method="post" id="inventory-form">
          <input type="hidden" name="equipment_type" id="equipment_type">
          
          <div class="form-group">
            <label for="property_number">Property Number</label>
            <input type="text" class="form-control" name="property_number" id="property_number" required>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" name="description" id="description" required>
          </div>

          <div class="form-group">
            <label for="model_number">Model Number (optional)</label>
            <input type="text" class="form-control" name="model_number" id="model_number">
          </div>

          <div class="form-group">
            <label for="acquisition_date">Acquisition Date</label>
            <input type="date" class="form-control" name="acquisition_date" id="acquisition_date">
          </div>

          <div class="form-group">
            <label for="cost">Cost</label>
            <input type="number" step="0.01" class="form-control" name="cost" id="cost">
          </div>

          <div class="form-group">
            <label for="person_accountable">Person Accountable</label>
            <input type="text" class="form-control" name="person_accountable" id="person_accountable" required>
          </div>

          <div class="form-group">
            <label for="remarks">Status</label>
            <select class="form-control" name="remarks" id="remarks">
              <option value="service">In Service</option>
              <option value="unservice">Unserviceable</option>
              <option value="dispose">For Disposal</option>
            </select>
          </div>

          <div class="form-group">
            <label for="inventory_date">Inventory Date</label>
            <input type="date" class="form-control" name="inventory_date" id="inventory_date">
          </div>

          <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="button" class="btn btn-outline" onclick="backToStep2()">
              <i class="fas fa-arrow-left"></i> Back
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-save"></i> Save Item
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Import Section (Hidden) -->
    <div id="import-section" class="hidden">
      <div class="form-container fade-in">
        <h2>Import <span id="import-type"></span> Data</h2>
        <p style="margin-bottom: 1.5rem;">Upload a CSV file containing your inventory data:</p>
        
        <form id="import-form">
          <div class="form-group">
            <label for="csv-file">CSV File</label>
            <input type="file" class="form-control" id="csv-file" accept=".csv" required>
          </div>

          <div class="form-group">
            <label>
              <input type="checkbox" id="has-headers"> File contains headers
            </label>
          </div>

          <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="button" class="btn btn-outline" onclick="backToStep2()">
              <i class="fas fa-arrow-left"></i> Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-upload"></i> Import
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

 <script src="/inventory-system/public/scripts/create.js"></script>

  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $host = 'localhost';
      $dbname = 'inventory-system';
      $username = 'root';
      $password = '';

      try {
          $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

          $stmt = $conn->prepare("
              INSERT INTO inventory (
                  property_number, description, model_number, acquisition_date, cost,
                  person_accountable, remarks, signature_of_inventory_team_date, equipment_type
              ) VALUES (
                  :property_number, :description, :model_number, :acquisition_date, :cost,
                  :person_accountable, :remarks, :inventory_date, :equipment_type
              )
          ");

          $stmt->execute([
              ':property_number' => $_POST['property_number'],
              ':description' => $_POST['description'],
              ':model_number' => $_POST['model_number'],
              ':acquisition_date' => $_POST['acquisition_date'],
              ':cost' => $_POST['cost'],
              ':person_accountable' => $_POST['person_accountable'],
              ':remarks' => $_POST['remarks'],
              ':inventory_date' => $_POST['inventory_date'],
              ':equipment_type' => $_POST['equipment_type'],
          ]);

          echo "<script>alert('Record added successfully!');</script>";
      } catch (PDOException $e) {
          echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
      }
  }
  ?>
</body>
</html>