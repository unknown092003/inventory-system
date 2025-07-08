<?php
require_once __DIR__ . '/../../api/config.php';
requireAuth();
// Display import messages
if (isset($_SESSION['import_success'])) {
    echo "<div style='color:green; margin:10px 0; padding:10px; border:1px solid green;'>" 
         . $_SESSION['import_success'] . "</div>";
    unset($_SESSION['import_success']);
}

if (isset($_SESSION['import_error'])) {
    echo "<div style='color:red; margin:10px 0; padding:10px; border:1px solid red;'>" 
         . $_SESSION['import_error'] . "</div>";
    unset($_SESSION['import_error']);
}

if (isset($_SESSION['import_errors']) && is_array($_SESSION['import_errors'])) {
    echo "<div style='color:orange; margin:10px 0; padding:10px; border:1px solid orange;'>";
    echo "<h3>Partial Import - Some Errors Occurred:</h3>";
    echo "<ul>";
    foreach ($_SESSION['import_errors'] as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul></div>";
    unset($_SESSION['import_errors']);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/inventory-system/public/styles/landingstyle/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Inventory Dashboard</title>
</head>
<body>
    <!-- introduction of current log in user  -->
    <div class="header_home">
        <h1>Inventory System</h1>
        <div class="welcome-message">
            <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>
        </div>  
        <!-- <a href="logout.php">Logout</a></p> -->
    </div>

    <div>
      <div class="card-grid">
        <div class="card fade-in" onclick="showEquipmentModal('Machinery')">
          <i class="fas fa-cogs"></i>
          <h3>Machinery</h3>
          <p>Heavy equipment and industrial machines</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('Construction')">
          <i class="fas fa-truck-pickup"></i>
          <h3>Construction</h3>
          <p>Tools and vehicles for construction</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('ICT Equipment')">
          <i class="fas fa-server"></i>
          <h3>ICT Equipment</h3>
          <p>Computers, servers, and networking</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('Communications')">
          <i class="fas fa-satellite-dish"></i>
          <h3>Communications</h3>
          <p>Radio and satellite equipment</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('Military/Security')">
          <i class="fas fa-shield-alt"></i>
          <h3>Military/Security</h3>
          <p>Defense and security apparatus</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('Office')">
          <i class="fas fa-print"></i>
          <h3>Office</h3>
          <p>Office supplies and equipment</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('DRRM Equipment')">
          <i class="fas fa-first-aid"></i>
          <h3>DRRM</h3>
          <p>Disaster risk reduction equipment</p>
        </div>
        <div class="card fade-in" onclick="showEquipmentModal('Furniture')">
          <i class="fas fa-couch"></i>
          <h3>Furniture</h3>
          <p>Office furniture and fixtures</p>
        </div>
      </div>
    </div>
    
    <!-- Equipment Modal -->
    <div id="equipment-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; padding:20px; overflow-y:auto;">
      <div style="background:#fff; max-width:800px; margin:50px auto; padding:25px; border-radius:8px; box-shadow:0 5px 15px rgba(0,0,0,0.3);">
        <h2 id="equipment-title" style="margin-top:0; color:#333; font-size:24px; text-align:center;"></h2>
        <div id="equipment-content" style="margin:15px 0;">
          <!-- Content will be added dynamically -->
        </div>
        <div style="text-align:center; margin-top:20px;">
          <button onclick="closeEquipmentModal()" style="background:#ff8000; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">Close</button>
        </div>
      </div>
    </div>


       <h2 class="h2_act">Recent Activity</h2>
<div class="table-wrapper" style="overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -10px; padding: 0 10px;">
<table cellpadding="5" id="activity-table" style="min-width: 800px; width: 100%;">
    <tr>
        <th>Time</th>
        <th>Action</th>
        <th>Equipment Type</th>
        <th>User</th>
        <th>Details</th>
    </tr>
    <?php foreach ($logger->getRecentLogs(5) as $log): 
        $details = json_decode($log['details'] ?? '{}', true);
    ?>
    <tr>
        <td><?= $log['timestamp'] ?></td>
        <td><?= ucfirst($log['action']) ?></td>
        <td><?= htmlspecialchars($log['description']) ?></td>
        <td><?= htmlspecialchars($log['user']) ?></td>
        <td>
            <?php if ($log['action'] === 'edited' && !empty($details)): ?>
                <button onclick="showChanges(this, <?= htmlspecialchars(json_encode($details), ENT_QUOTES, 'UTF-8') ?>)">
                    See changes
                </button>
            <?php else: ?>
                <?= htmlspecialchars($log['details'] ?? 'N/A') ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>
<!-- Change comparison modal -->
<div id="changes-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(20,30,40,0.85); z-index:1000; padding:10px; overflow-y:auto;">
  <div style="background:linear-gradient(135deg,#fffbe7 0%,#e3f0ff 100%); padding:24px 16px 20px 16px; max-width:900px; margin:40px auto; border-radius:12px; box-shadow:0 4px 16px rgba(0,0,0,0.15); position:relative;">
    <h2 style="text-align:center; color:#1a237e; letter-spacing:0.5px; margin-bottom:14px; font-size:1.5rem;">
      <i class="fas fa-exchange-alt" style="color:#ffa200; margin-right:6px;"></i>
      Change Comparison
    </h2>
    <div style="display:flex; flex-direction:column; gap:16px;">
      <div style="background:#fff; border-radius:8px; box-shadow:0 2px 6px #ffa20022; padding:14px;">
        <h3 style="color:#e53935; text-align:center; font-size:1rem; margin-bottom:8px;">
          <i class="fas fa-history"></i> Old Values
        </h3>
        <div style="overflow-x:auto;">
          <table border="0" cellpadding="4" id="old-values-table" style="width:100%; border-radius:6px; overflow:hidden; background:#fff8f6; font-size:0.9rem;">
          </table>
        </div>
      </div>
      <div style="background:#fff; border-radius:8px; box-shadow:0 2px 6px #2196f322; padding:14px;">
        <h3 style="color:#43a047; text-align:center; font-size:1rem; margin-bottom:8px;">
          <i class="fas fa-sync-alt"></i> New Values
        </h3>
        <div style="overflow-x:auto;">
          <table border="0" cellpadding="4" id="new-values-table" style="width:100%; border-radius:6px; overflow:hidden; background:#f6fff8; font-size:0.9rem;">
          </table>
        </div>
      </div>
    </div>
    <button onclick="document.getElementById('changes-modal').style.display='none'"
        style="margin:20px auto 0 auto; display:block; padding:8px 24px; background:linear-gradient(90deg,#ffa200,#ff8000); color:#fff; border:none; border-radius:5px; font-size:0.9rem; font-weight:600; box-shadow:0 2px 6px #ffa20033; cursor:pointer;">
      <i class="fas fa-times"></i> Close
    </button>
    <div style="position:absolute; top:12px; right:16px;">
      <button onclick="document.getElementById('changes-modal').style.display='none'"
        style="background:none; border:none; font-size:1.2rem; color:#ffa200; cursor:pointer;">
        <i class="fas fa-times-circle"></i>
      </button>
    </div>
  </div>
</div>

<style>
  /* Desktop styles */
  @media (min-width: 768px) {
    #changes-modal > div {
      padding: 32px 28px 24px 28px;
      margin: 60px auto;
      border-radius: 18px;
    }
    
    #changes-modal h2 {
      font-size: 2rem;
      margin-bottom: 18px;
    }
    
    #changes-modal > div > div {
      flex-direction: row;
      gap: 32px;
    }
    
    #changes-modal h3 {
      font-size: 1.1rem;
      margin-bottom: 10px;
    }
    
    #changes-modal table {
      padding: 6px;
      font-size: 1rem;
    }
    
    #changes-modal button[onclick] {
      padding: 10px 32px;
      font-size: 1rem;
    }
    
    #changes-modal > div > div:last-child button {
      font-size: 1.5rem;
    }
  }
  
  /* Mobile touch target improvements */
  button {
    min-height: 44px; /* Recommended minimum touch target size */
  }
  
  /* Table scrolling for mobile */
  table {
    min-width: 300px; /* Ensures tables don't get too narrow */
  }
</style>

<script>
function showChanges(button, changes) {
    const modal = document.getElementById('changes-modal');
    const oldTable = document.getElementById('old-values-table');
    const newTable = document.getElementById('new-values-table');
    
    // Clear previous content
    oldTable.innerHTML = '<tr><th>Field</th><th>Value</th></tr>';
    newTable.innerHTML = '<tr><th>Field</th><th>Value</th></tr>';
    
    // Populate tables
    for (const [field, value] of Object.entries(changes.old)) {
        const row = oldTable.insertRow();
        row.insertCell(0).textContent = field;
        row.insertCell(1).textContent = value;
    }
    
    for (const [field, value] of Object.entries(changes.new)) {
        const row = newTable.insertRow();
        row.insertCell(0).textContent = field;
        row.insertCell(1).textContent = value;
    }
    
    // Highlight changed fields
    for (const [field, oldValue] of Object.entries(changes.old)) {
        if (changes.new[field] !== oldValue) {
            const oldRows = oldTable.getElementsByTagName('tr');
            const newRows = newTable.getElementsByTagName('tr');
            
            for (let i = 0; i < oldRows.length; i++) {
                if (oldRows[i].cells[0]?.textContent === field) {
                    oldRows[i].style.backgroundColor = '#ffeeee';
                }
            }
            
            for (let i = 0; i < newRows.length; i++) {
                if (newRows[i].cells[0]?.textContent === field) {
                    newRows[i].style.backgroundColor = '#eeffee';
                }
            }
        }
    }
    
    modal.style.display = 'block';
}

function showEquipmentModal(equipmentType) {
  // Set the title in the modal
  document.getElementById('equipment-title').textContent = equipmentType;
  
  // Show loading state
  document.getElementById('equipment-content').innerHTML = '<div style="text-align:center;padding:20px;">Loading statistics...</div>';
  
  // Show the modal
  document.getElementById('equipment-modal').style.display = 'block';
  
  // Fetch equipment statistics
  fetch(`/inventory-system/api/get_equipment_stats.php?type=${encodeURIComponent(equipmentType)}`)
    .then(response => response.json())
    .then(data => {
      if (data.error) {
        document.getElementById('equipment-content').innerHTML = `<div style="color:red;text-align:center;">${data.error}</div>`;
        return;
      }
      
      // Format currency function
      const formatCurrency = (amount) => {
        return '₱' + parseFloat(amount).toLocaleString('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
      };
      
      // Create statistics display
      const statsHtml = `
        <div style="padding:10px;">
          <div style="display:grid; grid-template-columns:repeat(auto-fill , minmax(150px, 1fr)); gap:15px; text-align:center;">
            <div style="background:#f0f0f0; padding:15px; border-radius:8px;">
              <div style="font-size:24px; font-weight:bold;">${data.total}</div>
              <div>Total Items</div>
              <div style="margin-top:10px; border-top:1px solid #ddd; padding-top:8px;">
                <div style="font-size:14px; color:#666;">Total Cost</div>
                <div style="font-size:18px; font-weight:bold;">${formatCurrency(data.total_cost)}</div>
              </div>
            </div>
            <div style="background:#e8f5e9; padding:15px; border-radius:8px;">
              <div style="font-size:24px; font-weight:bold; color:#2e7d32;">${data.serviceable}</div>
              <div>Serviceable</div>
              <div style="margin-top:10px; border-top:1px solid #ddd; padding-top:8px;">
                <div style="font-size:14px; color:#666;">Total Cost</div>
                <div style="font-size:18px; font-weight:bold; color:#2e7d32;">${formatCurrency(data.serviceable_cost)}</div>
              </div>
            </div>
            <div style="background:#fffde7; padding:15px; border-radius:8px;">
              <div style="font-size:24px; font-weight:bold; color:#fbc02d;">${data.standby}</div>
              <div>Standby</div>
              <div style="margin-top:10px; border-top:1px solid #ddd; padding-top:8px;">
                <div style="font-size:14px; color:#666;">Total Cost</div>
                <div style="font-size:18px; font-weight:bold; color:#fbc02d;">${formatCurrency(data.standby_cost)}</div>
              </div>
            </div>
            <div style="background:#fff3e0; padding:15px; border-radius:8px;">
              <div style="font-size:24px; font-weight:bold; color:#ef6c00;">${data.unserviceable}</div>
              <div>Unserviceable</div>
              <div style="margin-top:10px; border-top:1px solid #ddd; padding-top:8px;">
                <div style="font-size:14px; color:#666;">Total Cost</div>
                <div style="font-size:18px; font-weight:bold; color:#ef6c00;">${formatCurrency(data.unserviceable_cost)}</div>
              </div>
            </div>
            <div style="background:#ffebee; padding:15px; border-radius:8px;">
              <div style="font-size:24px; font-weight:bold; color:#c62828;">${data.disposed}</div>
              <div>Disposed</div>
              <div style="margin-top:10px; border-top:1px solid #ddd; padding-top:8px;">
                <div style="font-size:14px; color:#666;">Total Cost</div>
                <div style="font-size:18px; font-weight:bold; color:#c62828;">${formatCurrency(data.disposed_cost)}</div>
              </div>
            </div>
          </div>
          <div style="margin-top:20px; text-align:center;">
            <a href="/inventory-system/pages/landing/data.php?search=${encodeURIComponent(equipmentType)}" 
               style="display:inline-block; padding:8px 16px; background:#2196F3; color:white; text-decoration:none; border-radius:4px;">
              View All ${equipmentType} Items
            </a>
          </div>
        </div>
      `;
      
      document.getElementById('equipment-content').innerHTML = statsHtml;
    })
    .catch(error => {
      document.getElementById('equipment-content').innerHTML = `<div style="color:red;text-align:center;">Error loading statistics: ${error.message}</div>`;
    });
}

function closeEquipmentModal() {
  // Hide the modal
  document.getElementById('equipment-modal').style.display = 'none';
}
</script>
</body>
</html>
