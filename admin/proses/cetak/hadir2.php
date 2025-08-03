<?php
require '../../../vendor/autoload.php';
include('../../../koneksi.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Ambil tanggal dari parameter URL, jika tidak ada maka gunakan tanggal hari ini
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Bayu')
    ->setLastModifiedBy('Bayu')
    ->setTitle('Rekap absen')
    ->setSubject('Rekap absen')
    ->setDescription('rekap data absensi guru menggunakan website')
    ->setKeywords('absen')
    ->setCategory('data');

// Add header row
$sheet = $spreadsheet->getActiveSheet();
$header = ['No', 'Nama Guru', 'Tanggal', 'Jam 0', 'Jam 1', 'Jam 2', 'Jam 3', 'Jam 4', 'Jam 5', 'Jam 6', 'Total absen'];
$column = 'A';

foreach ($header as $heading) {
    $sheet->setCellValue($column . '1', $heading);
    $sheet->getStyle($column . '1')->getFont()->setBold(true);
    $sheet->getStyle($column . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B8D9FF'); // Custom color
    $column++;
}

// Fetch data from the database based on the selected date
$query = "SELECT * FROM absen_msk WHERE tanggal = '$tanggal'";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi));
}

$rowNumber = 2; // Start at the second row
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    // Variabel untuk menyimpan jumlah
    $jumlahJam = 0;
    if (!empty($row['jam1'])) $jumlahJam++;
    if (!empty($row['jam2'])) $jumlahJam++;
    if (!empty($row['jam3'])) $jumlahJam++;
    if (!empty($row['jam4'])) $jumlahJam++;
    if (!empty($row['jam5'])) $jumlahJam++;
    if (!empty($row['jam6'])) $jumlahJam++;

    $sheet->setCellValue('A' . $rowNumber, $no)
        ->setCellValue('B' . $rowNumber, $row['nama'])
        ->setCellValue('C' . $rowNumber, $row['tanggal'] == '0000-00-00' ? 'Belum absen' : date('d-m-Y', strtotime($row['tanggal'])))
        ->setCellValue('D' . $rowNumber, $row['jam0'])
        ->setCellValue('E' . $rowNumber, $row['jam1'])
        ->setCellValue('F' . $rowNumber, $row['jam2'])
        ->setCellValue('G' . $rowNumber, $row['jam3'])
        ->setCellValue('H' . $rowNumber, $row['jam4'])
        ->setCellValue('I' . $rowNumber, $row['jam5'])
        ->setCellValue('J' . $rowNumber, $row['jam6'])
        ->setCellValue('K' . $rowNumber, $jumlahJam);

    // Apply red background color to empty cells
    foreach (range('A', 'K') as $col) {
        if ($sheet->getCell($col . $rowNumber)->getValue() == '') {
            $sheet->getStyle($col . $rowNumber)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF0000');
        }
    }

    $rowNumber++;
    $no++;
}

// Apply border to all cells
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
];
$sheet->getStyle('A1:K' . ($rowNumber - 1))->applyFromArray($styleArray);

// Apply italic and center alignment to columns D to K
$italicCenterStyle = [
    'font' => [
        'italic' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
];
$sheet->getStyle('D2:K' . ($rowNumber - 1))->applyFromArray($italicCenterStyle);

// Set column auto width
foreach (range('A', 'K') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Rename worksheet
$sheet->setTitle('Absen Guru');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data absen ' . $tanggal . '.xlsx"');
header('Cache-Control: max-age=0');
header('Cache-Control: max-age=1'); // If you're serving to IE 9, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // Always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
