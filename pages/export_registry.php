<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Load the template
$templatePath = __DIR__ . '/../templates/registry.xlsx';
$spreadsheet = IOFactory::load($templatePath);
$sheet = $spreadsheet->getActiveSheet();

// Set default font to Arial 11 for all cells, including template content
$highestColumn = $sheet->getHighestColumn();
$highestRow = $sheet->getHighestRow();
$sheet->getStyle('A1:' . $highestColumn . $highestRow)
    ->getFont()->setName('Arial')->setSize(11);

// Fetch data from the database
$conn = Database::getInstance()->getConnection();

// Build WHERE clause based on filters from GET parameters
$where = [];
$params = [];
if (!empty($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $where[] = "(property_number LIKE ? OR description LIKE ? OR model_number LIKE ? OR equipment_type LIKE ? OR remarks LIKE ? OR person_accountable LIKE ?)";
    $params = array_fill(0, 6, $search);
}
if (!empty($_GET['monthPicker'])) {
    $monthYear = $_GET['monthPicker'];
    $where[] = "acquisition_date LIKE ?";
    $params[] = $monthYear . '%';
}
if (!empty($_GET['yearFilter']) && $_GET['yearFilter'] !== 'all') {
    $where[] = "YEAR(acquisition_date) = ?";
    $params[] = $_GET['yearFilter'];
}
if (!empty($_GET['monthFilter']) && $_GET['monthFilter'] !== 'all') {
    $where[] = "MONTH(acquisition_date) = ?";
    $params[] = $_GET['monthFilter'];
}
if (!empty($_GET['equipmentFilter']) && $_GET['equipmentFilter'] !== 'all') {
    $where[] = "equipment_type = ?";
    $params[] = $_GET['equipmentFilter'];
}
if (!empty($_GET['remarksFilter']) && $_GET['remarksFilter'] !== 'all') {
    if ($_GET['remarksFilter'] === 'service') {
        $where[] = "remarks LIKE ?";
        $params[] = '%service%';
    } elseif ($_GET['remarksFilter'] === 'unservice') {
        $where[] = "(remarks LIKE ? OR remarks LIKE ?)";
        $params[] = '%unservice%';
        $params[] = '%not service%';
    } elseif ($_GET['remarksFilter'] === 'disposed') {
        $where[] = "remarks LIKE ?";
        $params[] = '%dispose%';
    }
}
if (!empty($_GET['valueFilter']) && $_GET['valueFilter'] !== 'all') {
    if ($_GET['valueFilter'] === 'high') {
        $where[] = "cost >= 5000";
    } elseif ($_GET['valueFilter'] === 'low') {
        $where[] = "cost > 0 AND cost < 5000";
    }
}
$whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";
$sql = "SELECT * FROM inventory $whereClause ORDER BY id ASC";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$dataRows = $stmt->fetchAll();

// If exportData is posted, use it instead of querying the database
if (!empty($_POST['exportData'])) {
    $dataRows = json_decode($_POST['exportData'], true);
    $isManualData = true;
} else {
    $isManualData = false;
    // ... existing query logic ...
}

// Start inserting at row 15 (A15)
$startRow = 15;
$currentRow = $startRow;
$totalAmount = 0;

if ($isManualData) {
    foreach ($dataRows as $row) {
        // Detect total row: first cell empty and one cell contains 'TOTAL'
        $isTotalRow = false;
        foreach ($row as $cell) {
            if (stripos($cell, 'total') !== false) {
                $isTotalRow = true;
                break;
            }
        }
        if ($isTotalRow) {
            // Place 'TOTAL:' in M, value in N, rest empty
            for ($col = 0; $col < 12; $col++) {
                $sheet->setCellValue(chr(65 + $col) . $currentRow, '');
            }
            $sheet->setCellValue('M' . $currentRow, 'TOTAL:');
            $sheet->setCellValue('N' . $currentRow, $row[count($row)-2]); // Amount value (second to last cell)
            $sheet->setCellValue('O' . $currentRow, '');
        } else {
            $col = 'A';
            foreach ($row as $cell) {
                $sheet->setCellValue($col . $currentRow, $cell);
                $col++;
            }
        }
        $currentRow++;
    }
} else {
    foreach ($dataRows as $row) {
        $amount = floatval($row['cost']);
        $totalAmount += $amount;
        $sheet->setCellValue('A' . $currentRow, $row['acquisition_date']);
        $sheet->setCellValue('B' . $currentRow, $row['model_number']);
        $sheet->setCellValue('C' . $currentRow, $row['property_number']);
        $sheet->setCellValue('D' . $currentRow, $row['description']);
        $sheet->setCellValue('E' . $currentRow, ''); // ESTIMATE USEFUL LIFE (empty)
        $sheet->setCellValue('F' . $currentRow, '1'); // QTY (Issued)
        $sheet->setCellValue('G' . $currentRow, $row['person_accountable']); // OFFICE/OFFICER (Issued)
        $sheet->setCellValue('H' . $currentRow, ''); // QTY (Returned)
        $sheet->setCellValue('I' . $currentRow, ''); // OFFICE/OFFICER (Returned)
        $sheet->setCellValue('J' . $currentRow, ''); // QTY (Re-issued)
        $sheet->setCellValue('K' . $currentRow, ''); // OFFICE/OFFICER (Re-issued)
        $sheet->setCellValue('L' . $currentRow, ''); // DISPOSE
        $sheet->setCellValue('M' . $currentRow, ''); // BALANCE
        $sheet->setCellValue('N' . $currentRow, number_format($amount, 2)); // AMOUNT
        $sheet->setCellValue('O' . $currentRow, $row['remarks']); // REMARKS
        $currentRow++;
    }
}

// Apply borders to all data and total rows
$tableStart = $startRow;
$tableEnd = $currentRow - 1; // Last data row (before total)
$totalRow = $currentRow; // Total row

// Apply border to data rows
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
];
$sheet->getStyle('A'.$tableStart.':O'.$tableEnd)->applyFromArray($borderStyle);
// Apply border to total row
$sheet->getStyle('A'.$totalRow.':O'.$totalRow)->applyFromArray($borderStyle);

// Output to browser as download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="final_export.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 