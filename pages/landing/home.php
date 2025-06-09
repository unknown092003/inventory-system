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

    <style>
        .welcome-message {
            margin: 10px auto;
            padding: 10px;
            /* background: linear-gradient(to right, #1622a7, #dc4d00); */
            color: #fff;
            text-align: center;
            border-radius: 16px;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.6s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border: 2px solid #ffa200;
        }

        .welcome-message p {
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
            color: #ffa200;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        .header_home {
            text-align: center;
            font-weight: bold;
            color: white;
            /* padding: 15px; ✅ Add this back */
            /* border-bottom: 1px solid #ccc; */
            text-shadow: 1px 1px 3px rgb(255, 255, 255);
            font-size: 20px;
        }
        .h2_act {
            margin: 10px auto;
            font-size: 22px;
            font-weight: 600;
            /* text-align: center; */
            color: white;
        }

        #activity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #0b2545; /* Slightly lighter than #001938 */
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            border: 2px solid #ffffff10; /* Subtle border */
        }

        #activity-table th {
            background-color: #123456;
            color: white;
            font-weight: 600;
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #2c3e50;
        }

        #activity-table td {
            color: #e0e0e0;
            padding: 12px 16px;
            text-align: center;
            border-bottom: 1px solid #2c3e50;
        }

        #activity-table tr:nth-child(even) {
            background-color: #0e2238;
        }

        #activity-table tr:hover {
            background-color: #1a3a5f;
        }

        tr td {
            color: white;
        }

        button {
            background-color: #ff8000;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2);
        }

        button:hover {
            background-color: #e26d00;
        }


        .changed-field {
            font-weight: bold;
            color: #d84315;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <style>

        
/* Card Grid Component CSS */
.card-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.card {
  background-color: white;
  border-radius: 8px;
  padding: 20px;
  text-align: center;
  cursor: pointer;
  transition: transform 0.3s, box-shadow 0.3s;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.card i {
  font-size: 2rem;
  color: var(--primary-color);
  margin-bottom: 10px;
}

.card h3 {
  margin: 0;
  font-size: 1.1rem;
}

.card p {
  margin: 10px 0 0;
  font-size: 0.9rem;
  color: #666;
}

/* Animation */
.fade-in {
  animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Utility classes */
.hidden {
  display: none;
}

.text-center {
  text-align: center;
}

/* Define primary color if not already defined */
:root {
  --primary-color: #3498db;
}


    </style>

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
    <div id="equipment-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; padding:20px;">
      <div style="background:#fff; max-width:600px; margin:50px auto; padding:25px; border-radius:8px; box-shadow:0 5px 15px rgba(0,0,0,0.3);">
        <h2 id="equipment-title" style="margin-top:0; color:#333; font-size:24px; text-align:center;"></h2>
        <div id="equipment-content" style="margin:15px 0;">
          <!-- Content will be added here later -->
        </div>
        <div style="text-align:center; margin-top:20px;">
          <button onclick="closeEquipmentModal()" style="background:#ff8000; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">Close</button>
        </div>
      </div>
    </div>


       <h2 class="h2_act">Recent Activity</h2>
<table cellpadding="5" id="activity-table">
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

<!-- Change comparison modal -->
<div id="changes-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; padding:20px;">
    <div style="background:white; padding:20px; max-width:800px; margin:50px auto; border-radius:5px;">
        <h2>Change Comparison</h2>
        <div style="display:flex; gap:20px;">
            <div style="flex:1;">
                <h3 style="color:red;">Old Values</h3>
                <table border="1" cellpadding="5" id="old-values-table" style="width:100%;"></table>
            </div>
            <div style="flex:1;">
                <h3 style="color:green;">New Values</h3>
                <table border="1" cellpadding="5" id="new-values-table" style="width:100%;"></table>
            </div>
        </div>
        <button onclick="document.getElementById('changes-modal').style.display='none'" 
                style="margin-top:20px; padding:5px 10px;">
            Close
        </button>
    </div>
</div>

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
  
  // Show the modal
  document.getElementById('equipment-modal').style.display = 'block';
}

function closeEquipmentModal() {
  // Hide the modal
  document.getElementById('equipment-modal').style.display = 'none';
}
</script>
</body>
</html>
