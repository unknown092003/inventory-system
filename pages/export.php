<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/styles/export.css">
    <title>Inventory Management System</title>
   
</head>
<body>
    <div class="header-group">
        <h1>Inventory Management System</h1>
        <div class="controls">
            <button onclick="addNewItem()">Add New Item</button>
            <button onclick="exportToExcel()">Export to Excel</button>
        </div>
    </div>
    
    <div class="inventory-container">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ARTICLE</th>
                    <th>DESCRIPTION</th>
                    <th>ACQUISITION DATE</th>
                    <th>NEW PROPERTY NUMBER</th>
                    <th>UNIT OF MEASURE</th>
                    <th>UNIT VALUE</th>
                    <th>QUANTITY per PROPERTY CARD</th>
                    <th>QUANTITY per PHYSICAL COUNT</th>
                    <th colspan="2">SHORTAGE/OVERAGE</th>
                    <th>REMARKS</th>
                </tr>
                <tr>
                    <th colspan="6"></th>
                    <th colspan="2"></th>
                    <th>Quantity</th>
                    <th>Value</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Office Chair</td>
                    <td>Ergonomic executive chair</td>
                    <td>2023-05-15</td>
                    <td>PROP-2023-001</td>
                    <td>pcs</td>
                    <td class="numeric-cell">6,500.00</td>
                    <td class="numeric-cell">5</td>
                    <td class="numeric-cell">5</td>
                    <td class="numeric-cell">0</td>
                    <td class="numeric-cell">0.00</td>
                    <td>New purchase</td>
                </tr>
                <tr>
                    <td>Desktop Computer</td>
                    <td>Core i7, 16GB RAM</td>
                    <td>2023-06-20</td>
                    <td>PROP-2023-002</td>
                    <td>pcs</td>
                    <td class="numeric-cell high-value">45,000.00</td>
                    <td class="numeric-cell">10</td>
                    <td class="numeric-cell">9</td>
                    <td class="numeric-cell">-1</td>
                    <td class="numeric-cell">-45,000.00</td>
                    <td>One unit missing</td>
                </tr>
                <tr>
                    <td>Printer</td>
                    <td>Laser printer A4</td>
                    <td>2023-07-10</td>
                    <td>PROP-2023-003</td>
                    <td>pcs</td>
                    <td class="numeric-cell">8,200.00</td>
                    <td class="numeric-cell">3</td>
                    <td class="numeric-cell">3</td>
                    <td class="numeric-cell">0</td>
                    <td class="numeric-cell">0.00</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Paper</td>
                    <td>A4 bond paper</td>
                    <td>2023-08-05</td>
                    <td>PROP-2023-004</td>
                    <td>ream</td>
                    <td class="numeric-cell low-value">220.00</td>
                    <td class="numeric-cell">50</td>
                    <td class="numeric-cell">52</td>
                    <td class="numeric-cell">+2</td>
                    <td class="numeric-cell">+440.00</td>
                    <td>Extra from supplier</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        function addNewItem() {
            alert("Add new item functionality would go here");
            // In a real implementation, this would open a form to add new items
        }
        
        function exportToExcel() {
            alert("Export to Excel functionality would go here");
            // In a real implementation, this would generate an Excel file
        }
        
        // Automatically highlight high value items
        document.addEventListener('DOMContentLoaded', function() {
            const valueCells = document.querySelectorAll('.inventory-table td:nth-child(6)');
            valueCells.forEach(cell => {
                const value = parseFloat(cell.textContent.replace(/,/g, ''));
                if (value >= 5000) {
                    cell.classList.add('high-value');
                } else {
                    cell.classList.add('low-value');
                }
            });
        });
    </script>
</body>
</html>