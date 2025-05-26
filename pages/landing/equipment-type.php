<?php
require_once __DIR__ . '/../../api/config.php';
requireAuth();

$equipment_types = [
    'Machinery',
    'Construction',
    'ICT Equipment',
    'Communications',
    'Military/Security',
    'Office',
    'DRRM Equipment',
    'Furniture'
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Equipment Type</title>
    <style>
        .type-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 800px;
            margin: 30px auto;
        }
        .type-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .type-card:hover {
            background: #f5f5f5;
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .type-card img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 10px;
        }
        .action-buttons {
            display: none;
            margin-top: 20px;
        }
        .selected {
            border: 2px solid #4CAF50;
            background: #f8fff8;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Select Equipment Type</h1>
    
    <div class="type-container">
        <?php foreach ($equipment_types as $type): ?>
        <div class="type-card" onclick="selectType(this, '<?= htmlspecialchars($type) ?>')">
            <!-- You can add icons for each type later -->
            <div style="font-size: 40px;"><?= substr($type, 0, 1) ?></div>
            <h3><?= htmlspecialchars($type) ?></h3>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div id="action-buttons" class="action-buttons" style="text-align: center;">
        <a id="create-link" href="#" class="button" style="display: inline-block; margin: 10px; padding: 12px 24px; background: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">
            Create New <span id="selected-type"></span>
        </a>
        <a id="import-link" href="#" class="button" style="display: inline-block; margin: 10px; padding: 12px 24px; background: #2196F3; color: white; text-decoration: none; border-radius: 4px;">
            Import <span id="selected-type-import"></span>
        </a>
    </div>

    <script>
        let selectedType = '';
        
        function selectType(card, type) {
            // Remove selected class from all cards
            document.querySelectorAll('.type-card').forEach(c => {
                c.classList.remove('selected');
            });
            
            // Add selected class to clicked card
            card.classList.add('selected');
            selectedType = type;
            
            // Show action buttons
            document.getElementById('action-buttons').style.display = 'block';
            
            // Update button texts
            document.getElementById('selected-type').textContent = type;
            document.getElementById('selected-type-import').textContent = type + ' Data';
            
            // Update links with proper URL encoding
            document.getElementById('create-link').href = `create.php?type=${encodeURIComponent(type)}`;
            document.getElementById('import-link').href = `import.php?type=${encodeURIComponent(type)}`;
            
            // Debug output
            console.log('Selected type:', type);
            console.log('Create link:', document.getElementById('create-link').href);
            console.log('Import link:', document.getElementById('import-link').href);
        }
    </script>
</body>
</html>