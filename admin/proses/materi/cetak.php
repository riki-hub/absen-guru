<?php
require '../../../vendor/autoload.php';
include('../../../koneksi.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Set document properties
$spreadsheet->getProperties()->setCreator('Bayu')
    ->setLastModifiedBy('Bayu')
    ->setTitle('Rekap Materi')
    ->setSubject('Rekap Materi')
    ->setDescription('rekap data materi guru menggunakan website')
    ->setKeywords('materi')
    ->setCategory('data');

// Add header row
$sheet = $spreadsheet->getActiveSheet();
$header = ['No', 'Tanggal Upload', 'Nama Guru', 'Mata Pelajaran', 'Materi'];
$column = 'A';

foreach ($header as $heading) {
    $sheet->setCellValue($column . '1', $heading);
    $sheet->getStyle($column . '1')->getFont()->setBold(true);
    $sheet->getStyle($column . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('B8D9FF'); // Custom color
    $column++;
}

// Fetch data from the database (tabel materi)
$query = "SELECT * FROM materi ORDER BY tanggal_upload DESC";
$result = mysqli_query($koneksi, $query);
if (!$result) {
    die("Query error: " . mysqli_error($koneksi) . "-" . mysqli_error($koneksi));
}

$rowNumber = 2; // Start at the second row
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $no)
        ->setCellValue('B' . $rowNumber, $row['tanggal_upload'] == '0000-00-00' ? 'Belum diunggah' : date('d-m-Y', strtotime($row['tanggal_upload'])))
        ->setCellValue('C' . $rowNumber, $row['nama_guru'])
        ->setCellValue('D' . $rowNumber, $row['mapel'])
        ->setCellValue('E' . $rowNumber, $row['nama_materi']);

    foreach (range('A', 'E') as $col) {
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
$sheet->getStyle('A1:E' . ($rowNumber - 1))->applyFromArray($styleArray);

// Set column auto width
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Rename worksheet
$sheet->setTitle('Data Materi');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);

// Get the current date
$date = date('d-m-Y');

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Data Materi ' . $date . '.xlsx"');
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