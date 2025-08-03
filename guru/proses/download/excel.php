<?php
require '../../../vendor/autoload.php'; // sesuaikan path
include('../../../koneksi.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$kelasId = $_GET['kelas_id'];

// Ambil tanggal
$tanggalQuery = "SELECT DISTINCT tanggal FROM absensi 
                 WHERE tanggal BETWEEN ? AND ? ORDER BY tanggal ASC";
$tanggalStmt = $koneksi->prepare($tanggalQuery);
$tanggalStmt->bind_param("ss", $startDate, $endDate);
$tanggalStmt->execute();
$tanggalResult = $tanggalStmt->get_result();
$tanggalList = [];
foreach ($tanggalResult as $row) {
    $tanggalList[] = $row['tanggal'];
}

// Ambil siswa
$siswaQuery = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa ASC";
$siswaStmt = $koneksi->prepare($siswaQuery);
$siswaStmt->bind_param("s", $kelasId);
$siswaStmt->execute();
$siswaResult = $siswaStmt->get_result();
$siswaList = [];
foreach ($siswaResult as $row) {
    $siswaList[] = $row;
}

// Ambil absensi
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
    $symbol = '';
    if ($row['sakit']) $symbol = 'S';
    elseif ($row['izin']) $symbol = 'I';
    elseif ($row['alpa']) $symbol = 'A';
    // kalau hadir maka kosong (tidak tampil 'H')
    $absensiData[$siswaId][$tanggal] = $symbol;
}


// Rekap
$absensiStmt->execute();
$absensiResult = $absensiStmt->get_result();
$rekapData = [];
foreach ($absensiResult as $row) {
    $id = $row['siswa_id'];
    if (!isset($rekapData[$id])) {
        $rekapData[$id] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
    }
    if ($row['hadir']) $rekapData[$id]['H']++;
    if ($row['izin'])  $rekapData[$id]['I']++;
    if ($row['sakit']) $rekapData[$id]['S']++;
    if ($row['alpa'])  $rekapData[$id]['A']++;
}

// Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Absensi");

// Judul
$sheet->setCellValue('A1', 'Laporan Absensi Siswa');
$sheet->mergeCells('A1:E1');
$sheet->setCellValue('A2', "Periode: $startDate s.d. $endDate");

// Header
$rowNum = 4;
$col = 'A';
$sheet->setCellValue($col++ . $rowNum, 'No');
$sheet->setCellValue($col++ . $rowNum, 'Nama Siswa');

foreach ($tanggalList as $tgl) {
    $sheet->setCellValue($col++ . $rowNum, date('j', strtotime($tgl)));
}

// Hapus kolom 'H' pada header, langsung ke I, S, A
$sheet->setCellValue($col++ . $rowNum, 'I');
$sheet->setCellValue($col++ . $rowNum, 'S');
$sheet->setCellValue($col++ . $rowNum, 'A');

// Data siswa
$rowNum++;
$no = 1;
foreach ($siswaList as $siswa) {
    $col = 'A';
    $siswaId = $siswa['id'];

    $sheet->setCellValue($col++ . $rowNum, $no++);
    $sheet->setCellValue($col++ . $rowNum, $siswa['nama_siswa']);

    foreach ($tanggalList as $tanggal) {
        $symbol = $absensiData[$siswaId][$tanggal] ?? '';
        $sheet->setCellValue($col++ . $rowNum, $symbol);
    }

    // Hapus data kolom hadir 'H' di rekap
    // $sheet->setCellValue($col++ . $rowNum, $rekapData[$siswaId]['H'] ?? 0);
    $sheet->setCellValue($col++ . $rowNum, $rekapData[$siswaId]['I'] ?? 0);
    $sheet->setCellValue($col++ . $rowNum, $rekapData[$siswaId]['S'] ?? 0);
    $sheet->setCellValue($col++ . $rowNum, $rekapData[$siswaId]['A'] ?? 0);

    $rowNum++;
}

// Auto size hanya tanggal
$columnIndex = 'C';
foreach ($tanggalList as $_) {
    $sheet->getColumnDimension($columnIndex++)->setAutoSize(true);
}

// Set lebar tetap (hapus kolom H)
$sheet->getColumnDimension('A')->setWidth(5);   // No
$sheet->getColumnDimension('B')->setWidth(25);  // Nama
// Hilangkan pengaturan lebar kolom 'H'
// $sheet->getColumnDimension($columnIndex++)->setWidth(5);  // H
$sheet->getColumnDimension($columnIndex++)->setWidth(5);  // I
$sheet->getColumnDimension($columnIndex++)->setWidth(5);  // S
$sheet->getColumnDimension($columnIndex++)->setWidth(5);  // A

// Style
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
];

$sheet->getStyle("A4:" . $col . ($rowNum - 1))->applyFromArray($styleArray);

// Output
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"laporan_absensi.xlsx\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
