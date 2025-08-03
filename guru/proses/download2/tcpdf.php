<?php
require_once('../../tcpdf/tcpdf.php');
include('../../../koneksi.php');

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];
$kelasId = $_GET['kelas_id'];

// Ambil semua tanggal unik
$tanggalQuery = "SELECT DISTINCT tanggal FROM absensi 
                 WHERE tanggal BETWEEN ? AND ? 
                 ORDER BY tanggal ASC";
$tanggalStmt = $koneksi->prepare($tanggalQuery);
$tanggalStmt->bind_param("ss", $startDate, $endDate);
$tanggalStmt->execute();
$tanggalResult = $tanggalStmt->get_result();

$tanggalList = [];
foreach ($tanggalResult as $row) {
    $tanggalList[] = $row['tanggal'];
}

// Ambil semua siswa di kelas tersebut
$siswaQuery = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa ASC";
$siswaStmt = $koneksi->prepare($siswaQuery);
$siswaStmt->bind_param("s", $kelasId);
$siswaStmt->execute();
$siswaResult = $siswaStmt->get_result();

$siswaList = [];
foreach ($siswaResult as $row) {
    $siswaList[] = $row;
}

// Ambil semua data absensi sekaligus
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
    // Tampilkan kosong jika hadir, hanya tampilkan S/I/A
    if ($row['sakit']) $symbol = 'S';
    elseif ($row['izin']) $symbol = 'I';
    elseif ($row['alpa']) $symbol = 'A';
    else $symbol = ''; // hadir atau tidak ada data tampil kosong
    $absensiData[$siswaId][$tanggal] = $symbol;
}

// Hitung total kehadiran
$absensiStmt->execute(); // ulangi karena sudah habis dibaca di atas
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

// Siapkan PDF
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Laporan Absensi Siswa', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 11);
$pdf->Cell(0, 6, "Periode: $startDate s.d. $endDate", 0, 1);
$pdf->Ln(2);

// Hitung jumlah kolom
$jmlTanggal = count($tanggalList);
$jmlRekap = 4; // H, I, S, A
$totalLebar = 277; // Lebar halaman A4 lanskap dikurangi margin (297 - 2*10)

$lebarNo = 10;
$lebarNama = 50;
$sisaLebar = $totalLebar - $lebarNo - $lebarNama;

$lebarTanggal = $jmlTanggal > 0 ? ($sisaLebar * 0.7) / $jmlTanggal : 0;
$lebarRekap = ($sisaLebar * 0.3) / $jmlRekap;

// Header
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell($lebarNo, 8, 'No', 1, 0, 'C');
$pdf->Cell($lebarNama, 8, 'Nama Siswa', 1, 0, 'C');

foreach ($tanggalList as $tgl) {
    $pdf->Cell($lebarTanggal, 8, date('j', strtotime($tgl)), 1, 0, 'C');
}
$pdf->Cell($lebarRekap, 8, 'H', 1, 0, 'C');
$pdf->Cell($lebarRekap, 8, 'I', 1, 0, 'C');
$pdf->Cell($lebarRekap, 8, 'S', 1, 0, 'C');
$pdf->Cell($lebarRekap, 8, 'A', 1, 1, 'C');

// Data siswa
$pdf->SetFont('helvetica', '', 9);
$no = 1;
foreach ($siswaList as $siswa) {
    $siswaId = $siswa['id'];
    $pdf->Cell($lebarNo, 8, $no++, 1);
    $pdf->Cell($lebarNama, 8, $siswa['nama_siswa'], 1);

    foreach ($tanggalList as $tanggal) {
        $symbol = $absensiData[$siswaId][$tanggal] ?? '';
        $pdf->Cell($lebarTanggal, 8, $symbol, 1, 0, 'C');
    }

    $h = $rekapData[$siswaId]['H'] ?? 0;
    $i = $rekapData[$siswaId]['I'] ?? 0;
    $s = $rekapData[$siswaId]['S'] ?? 0;
    $a = $rekapData[$siswaId]['A'] ?? 0;

    $pdf->Cell($lebarRekap, 8, $h, 1, 0, 'C');
    $pdf->Cell($lebarRekap, 8, $i, 1, 0, 'C');
    $pdf->Cell($lebarRekap, 8, $s, 1, 0, 'C');
    $pdf->Cell($lebarRekap, 8, $a, 1, 1, 'C');
}

$pdf->Output('laporan_absensi_final.pdf', 'I');
