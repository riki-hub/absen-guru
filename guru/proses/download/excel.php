<?php
require '../../../vendor/autoload.php'; // sesuaikan path
include('../../../koneksi.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Get parameters from GET request
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$kelasId = $_GET['kelas_id'];

// Fetch class name
$kelasQuery = "SELECT nama_kelas FROM kelas WHERE id = ?";
$kelasStmt = $koneksi->prepare($kelasQuery);
$kelasStmt->bind_param("s", $kelasId);
$kelasStmt->execute();
$kelasResult = $kelasStmt->get_result();
$kelasName = $kelasResult->fetch_assoc()['nama_kelas'] ?? 'unknown_class';

// Fetch unique dates
$tanggalQuery = "SELECT DISTINCT tanggal FROM absensi 
                 WHERE tanggal BETWEEN ? AND ? 
                 ORDER BY tanggal ASC";
$tanggalStmt = $koneksi->prepare($tanggalQuery);
$tanggalStmt->bind_param("ss", $startDate, $endDate);
$tanggalStmt->execute();
$tanggalResult = $tanggalStmt->get_result();

$tanggalList = [];
while ($row = $tanggalResult->fetch_assoc()) {
    $tanggalList[] = $row['tanggal'];
}

// Fetch students in the specified class
$siswaQuery = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa ASC";
$siswaStmt = $koneksi->prepare($siswaQuery);
$siswaStmt->bind_param("s", $kelasId);
$siswaStmt->execute();
$siswaResult = $siswaStmt->get_result();

$siswaList = [];
while ($row = $siswaResult->fetch_assoc()) {
    $siswaList[] = $row;
}

// Fetch all attendance data
$absensiQuery = "SELECT siswa_id, tanggal, hadir, sakit, izin, alpa FROM absensi 
                 WHERE tanggal BETWEEN ? AND ?";
$absensiStmt = $koneksi->prepare($absensiQuery);
$absensiStmt->bind_param("ss", $startDate, $endDate);
$absensiStmt->execute();
$absensiResult = $absensiStmt->get_result();

$absensiData = [];
while ($row = $absensiResult->fetch_assoc()) {
    $siswaId = $row['siswa_id'];
    $tanggal = $row['tanggal'];
    // Display empty for present, only show S/I/A
    if ($row['sakit']) $symbol = 'S';
    elseif ($row['izin']) $symbol = 'I';
    elseif ($row['alpa']) $symbol = 'A';
    else $symbol = ''; // Present or no data
    $absensiData[$siswaId][$tanggal] = $symbol;
}

// Calculate attendance summary
$absensiStmt->execute(); // Re-execute as the result was consumed
$absensiResult = $absensiStmt->get_result();

$rekapData = [];
while ($row = $absensiResult->fetch_assoc()) {
    $id = $row['siswa_id'];
    if (!isset($rekapData[$id])) {
        $rekapData[$id] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
    }
    if ($row['hadir']) $rekapData[$id]['H']++;
    if ($row['izin'])  $rekapData[$id]['I']++;
    if ($row['sakit']) $rekapData[$id]['S']++;
    if ($row['alpa'])  $rekapData[$id]['A']++;
}

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator('Your Name')
    ->setTitle('Laporan Absensi Siswa')
    ->setSubject('Laporan Absensi')
    ->setDescription('Laporan absensi siswa untuk periode tertentu');

// Title and period
$sheet->setCellValue('A1', 'Laporan Absensi Siswa');
$sheet->setCellValue('A2', "Periode: $startDate s.d. $endDate");

// Merge cells for title
$sheet->mergeCells('A1:' . chr(65 + count($tanggalList) + 4) . '1'); // Adjust based on columns
$sheet->mergeCells('A2:' . chr(65 + count($tanggalList) + 4) . '2');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A2')->getFont()->setSize(11);
$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Headers
$sheet->setCellValue('A4', 'No');
$sheet->setCellValue('B4', 'Nama Siswa');
$col = 'C';
foreach ($tanggalList as $tgl) {
    $sheet->setCellValue($col . '4', date('j', strtotime($tgl)));
    $col++;
}
$sheet->setCellValue($col . '4', 'H');
$col++;
$sheet->setCellValue($col . '4', 'I');
$col++;
$sheet->setCellValue($col . '4', 'S');
$col++;
$sheet->setCellValue($col . '4', 'A');

// Apply styles to headers
$headerStyle = [
    'font' => ['bold' => true, 'size' => 9],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'borders' => [
        'allBorders' => ['borderStyle' => Border::BORDER_THIN],
    ],
];
$sheet->getStyle('A4:' . $col . '4')->applyFromArray($headerStyle);

// Data rows
$row = 5;
$no = 1;
foreach ($siswaList as $siswa) {
    $siswaId = $siswa['id'];
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $siswa['nama_siswa']);

    $col = 'C';
    foreach ($tanggalList as $tanggal) {
        $symbol = $absensiData[$siswaId][$tanggal] ?? '';
        $sheet->setCellValue($col . $row, $symbol);
        $col++;
    }

    $h = $rekapData[$siswaId]['H'] ?? 0;
    $i = $rekapData[$siswaId]['I'] ?? 0;
    $s = $rekapData[$siswaId]['S'] ?? 0;
    $a = $rekapData[$siswaId]['A'] ?? 0;

    $sheet->setCellValue($col . $row, $h);
    $col++;
    $sheet->setCellValue($col . $row, $i);
    $col++;
    $sheet->setCellValue($col . $row, $s);
    $col++;
    $sheet->setCellValue($col . $row, $a);

    // Apply borders to data row
    $sheet->getStyle('A' . $row . ':' . $col . $row)->applyFromArray([
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    ]);

    $row++;
}

// Set column widths
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(20);
for ($i = 0; $i < count($tanggalList); $i++) {
    $sheet->getColumnDimension(chr(67 + $i))->setWidth(3); // C onward for dates
}
$sheet->getColumnDimension(chr(67 + count($tanggalList)))->setWidth(5); // H
$sheet->getColumnDimension(chr(68 + count($tanggalList)))->setWidth(5); // I
$sheet->getColumnDimension(chr(69 + count($tanggalList)))->setWidth(5); // S
$sheet->getColumnDimension(chr(70 + count($tanggalList)))->setWidth(5); // A

// Set row height for headers and data
$sheet->getRowDimension(4)->setRowHeight(20);
for ($i = 5; $i < $row; $i++) {
    $sheet->getRowDimension($i)->setRowHeight(20);
}

// Sanitize class name for filename (remove invalid characters)
$safeKelasName = preg_replace('/[^A-Za-z0-9_-]/', '_', $kelasName);

// Output Excel file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_absensi_' . $safeKelasName . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;