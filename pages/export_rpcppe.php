<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/db.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

// Debug: log received POST data
file_put_contents(__DIR__ . '/debug_export.txt', print_r($_POST, true));

// Load the template
$templatePath = __DIR__ . '/../templates/rpcppe.xlsx';
$spreadsheet = IOFactory::load($templatePath);
$sheet = $spreadsheet->getActiveSheet();

// Set default font to Arial 11 for all cells
$highestColumn = $sheet->getHighestColumn();
$highestRow = $sheet->getHighestRow();
$sheet->getStyle('A1:' . $highestColumn . $highestRow)
    ->getFont()->setName('Arial')->setSize(11);

// Get posted data or fetch from database
$dataRows = [];
if (!empty($_POST['exportData'])) {
    $dataRows = json_decode($_POST['exportData'], true);
} else {
    // Fetch from database
    $conn = Database::getInstance()->getConnection();
    $sql = "SELECT * FROM inventory ORDER BY id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $dbRows = $stmt->fetchAll();
    foreach ($dbRows as $row) {
        $dataRows[] = [
            $row['article'],              // A: Article
            $row['description'],          // B: Description
            $row['acquisition_date'],     // C: Acquisition Date
            $row['property_number'],      // D: Property Number
            '',                           // E: Unit of Measure (empty)
            $row['cost'],                 // F: Unit Value
            '',                           // G: Quantity per Property Card
            '',                           // H: Quantity per Physical Count
            '',                           // I: Shortage/Overage Quantity
            '',                           // J: Shortage/Overage Value
            $row['remarks']               // K: Remarks
        ];
    }
}

// Insert data starting at row 16 (A16)
$startRow = 16;
$currentRow = $startRow;
$totalUnitValue = 0;
foreach ($dataRows as $row) {
    $col = 'A';
    for ($i = 0; $i < 11; $i++) { // Only write up to column K (11 columns)
        $cellValue = isset($row[$i]) ? $row[$i] : '';
        if ($i === 5) { // Unit Value column (F)
            $totalUnitValue += floatval(str_replace(',', '', $cellValue));
        }
        $sheet->setCellValue($col . $currentRow, $cellValue);
        $col++;
    }
    $currentRow++;
}
// Add total row
$sheet->setCellValue('E' . $currentRow, 'TOTAL');
$sheet->setCellValue('F' . $currentRow, number_format($totalUnitValue, 2));
for ($i = 0; $i < 11; $i++) {
    $col = chr(65 + $i);
    if ($col !== 'E' && $col !== 'F') {
        $sheet->setCellValue($col . $currentRow, '');
    }
}
// Apply borders to all data rows (including total row)
$endRow = $currentRow;
$borderStyle = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
];
$sheet->getStyle('A'.$startRow.':K'.$endRow)->applyFromArray($borderStyle);

// Output to browser as download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="final_export.xlsx"');
header('Cache-Control: max-age=0');
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit; 