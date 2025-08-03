<?php
session_start();
require '../../../vendor/autoload.php'; // sesuaikan path
include('../../../koneksi.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Pastikan user telah login
if (!isset($_SESSION['username'])) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Silakan login terlebih dahulu.']);
    exit;
}

// Ambil parameter GET
$mapelId = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;
$kelasId = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Validasi parameter
if ($mapelId <= 0 || $kelasId <= 0 || empty($tanggal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Parameter tidak valid.']);
    exit;
}

// Ambil nama mata pelajaran
$sqlMapel = "SELECT mata_pelajaran FROM pelajaran WHERE id = ?";
$stmtMapel = $koneksi->prepare($sqlMapel);
if ($stmtMapel) {
    $stmtMapel->bind_param('i', $mapelId);
    $stmtMapel->execute();
    $resultMapel = $stmtMapel->get_result();
    $mapel = $resultMapel->fetch_assoc();
    $stmtMapel->close();
    $namaMapel = $mapel ? htmlspecialchars($mapel['mata_pelajaran']) : 'Unknown';
} else {
    error_log("Gagal menyiapkan kueri mata pelajaran: " . $koneksi->error);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Gagal mengambil data mata pelajaran.']);
    exit;
}

// Ambil nama kelas
$sqlKelas = "SELECT nama_kelas FROM kelas WHERE id = ?";
$stmtKelas = $koneksi->prepare($sqlKelas);
if ($stmtKelas) {
    $stmtKelas->bind_param('i', $kelasId);
    $stmtKelas->execute();
    $resultKelas = $stmtKelas->get_result();
    $kelas = $resultKelas->fetch_assoc();
    $stmtKelas->close();
    $namaKelas = $kelas ? htmlspecialchars($kelas['nama_kelas']) : 'Unknown';
} else {
    error_log("Gagal menyiapkan kueri kelas: " . $koneksi->error);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Gagal mengambil data kelas.']);
    exit;
}

// Ambil semua siswa di kelas yang dipilih
$sqlSiswa = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa";
$stmtSiswa = $koneksi->prepare($sqlSiswa);
$allStudents = [];
if ($stmtSiswa) {
    $stmtSiswa->bind_param('i', $kelasId);
    $stmtSiswa->execute();
    $resultSiswa = $stmtSiswa->get_result();
    $allStudents = $resultSiswa->fetch_all(MYSQLI_ASSOC);
    $stmtSiswa->close();
} else {
    error_log("Gagal mengambil data siswa: " . $koneksi->error);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Gagal mengambil data siswa.']);
    exit;
}

// Ambil data nilai untuk mata pelajaran dan kelas yang dipilih, ambil nilai terbaru per siswa
$sqlNilai = "SELECT ns.id_siswa, ns.tanggal, ns.nilai_1, ns.nilai_2, ns.nilai_3, ns.nilai_4, ns.nilai_5 
             FROM nilai_siswa ns 
             WHERE ns.id_mapel = ? AND ns.id_kelas = ? 
             AND ns.tanggal = (
                 SELECT MAX(tanggal) 
                 FROM nilai_siswa 
                 WHERE id_siswa = ns.id_siswa 
                 AND id_mapel = ns.id_mapel 
                 AND id_kelas = ns.id_kelas
             )";
$stmtNilai = $koneksi->prepare($sqlNilai);
$existingGrades = [];
if ($stmtNilai) {
    $stmtNilai->bind_param('ii', $mapelId, $kelasId);
    $stmtNilai->execute();
    $resultNilai = $stmtNilai->get_result();
    $existingGrades = $resultNilai->fetch_all(MYSQLI_ASSOC);
    $stmtNilai->close();
} else {
    error_log("Gagal mengambil data nilai: " . $koneksi->error);
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Gagal mengambil data nilai.']);
    exit;
}

// Buat array untuk menyimpan data nilai berdasarkan id_siswa
$gradesBySiswaId = [];
foreach ($existingGrades as $grade) {
    $gradesBySiswaId[$grade['id_siswa']] = $grade;
}

// Inisialisasi PhpSpreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul dan header
$sheet->setCellValue('A1', 'Laporan Nilai Siswa');
$sheet->setCellValue('A2', 'Mata Pelajaran: ' . $namaMapel);
$sheet->setCellValue('A3', 'Kelas: ' . $namaKelas);
$sheet->setCellValue('A4', 'Tanggal: ' . $tanggal);

// Format header
$sheet->mergeCells('A1:H1');
$sheet->mergeCells('A2:H2');
$sheet->mergeCells('A3:H3');
$sheet->mergeCells('A4:H4');
$sheet->getStyle('A1:A4')->getFont()->setBold(true);
$sheet->getStyle('A1:A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Set header tabel
$sheet->setCellValue('A6', 'No');
$sheet->setCellValue('B6', 'Nama Siswa');
$sheet->setCellValue('C6', 'Tanggal');
$sheet->setCellValue('D6', 'Nilai 1');
$sheet->setCellValue('E6', 'Nilai 2');
$sheet->setCellValue('F6', 'Nilai 3');
$sheet->setCellValue('G6', 'Nilai 4');
$sheet->setCellValue('H6', 'Nilai 5');

// Format header tabel
$sheet->getStyle('A6:H6')->getFont()->setBold(true);
$sheet->getStyle('A6:H6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A6:H6')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('DDDDDD');
$sheet->getStyle('A6:H6')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Isi data siswa dan nilai
$row = 7;
$no = 1;
foreach ($allStudents as $siswa) {
    $siswaId = $siswa['id'];
    $grades = isset($gradesBySiswaId[$siswaId]) ? $gradesBySiswaId[$siswaId] : [];

    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, htmlspecialchars($siswa['nama_siswa']));
    $sheet->setCellValue('C' . $row, isset($grades['tanggal']) ? $grades['tanggal'] : '');
    $sheet->setCellValue('D' . $row, isset($grades['nilai_1']) ? $grades['nilai_1'] : '');
    $sheet->setCellValue('E' . $row, isset($grades['nilai_2']) ? $grades['nilai_2'] : '');
    $sheet->setCellValue('F' . $row, isset($grades['nilai_3']) ? $grades['nilai_3'] : '');
    $sheet->setCellValue('G' . $row, isset($grades['nilai_4']) ? $grades['nilai_4'] : '');
    $sheet->setCellValue('H' . $row, isset($grades['nilai_5']) ? $grades['nilai_5'] : '');

    // Format baris
    $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('C' . $row . ':H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    $row++;
    $no++;
}

// Set lebar kolom
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(10);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(10);

// Nama file
$filename = 'Nilai_' . str_replace(' ', '_', $namaMapel) . '_' . str_replace(' ', '_', $namaKelas) . '_' . str_replace('-', '', $tanggal) . '.xlsx';

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Tulis file ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>