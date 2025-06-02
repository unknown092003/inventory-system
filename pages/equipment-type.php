<?php
require_once __DIR__ . '/../api/config.php';
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
        <style>
    .create-page {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to right, #f0f4ff, #ffffff);
    margin: 0;
    padding: 30px;
    color: #333;
}

    .h1_equip {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 40px;
        font-size: 32px;
        font-weight: 600;
    }

    .type-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
        max-width: 1000px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .type-card {
        background: linear-gradient(to bottom right, #ffffff, #f4f6fa);
        border: 2px solid #ddd;
        border-radius: 14px;
        padding: 32px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .type-card:hover {
        background: linear-gradient(to bottom right, #e0f7fa, #ffffff);
        transform: translateY(-6px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
        border-color: #2196F3;
    }

    .type-card.selected {
        border-color: #4CAF50;
        background: #e8fbe9;
        box-shadow: 0 0 12px rgba(76, 175, 80, 0.4);
    }

    .type-card div {
        font-size: 50px;
        color: #4CAF50;
        margin-bottom: 12px;
        font-weight: bold;
    }

    .type-card h3 {
        margin: 0;
        font-size: 18px;
        color: #333;
        font-weight: 500;
    }

    .action-buttons {
        display: none;
        margin-top: 50px;
        text-align: center;
        animation: fadeIn 0.3s ease-in-out;
    }

    .action-buttons .button {
        display: inline-block;
        margin: 10px 15px;
        padding: 14px 30px;
        background: #4CAF50;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-size: 17px;
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .action-buttons .button:hover {
        background: #43a047;
        transform: scale(1.05);
    }

    .action-buttons .button:nth-child(2) {
        background: #2196F3;
    }

    .action-buttons .button:nth-child(2):hover {
        background: #1976D2;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 600px) {
        .type-card {
            padding: 24px 16px;
        }

        .type-card div {
            font-size: 40px;
        }

        .type-card h3 {
            font-size: 16px;
        }

        .action-buttons .button {
            padding: 12px 20px;
            font-size: 15px;
        }
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