<?php
// PHP 8.4 kompatibel export PDF dengan TCPDF

// Include TCPDF
require_once(__DIR__ . '/../../../vendor/autoload.php');

// Sisanya kode Anda... Sesuaikan path jika TCPDF berada di lokasi lain
include '../../../koneksi.php';

// Pastikan pengguna telah login
session_start();

// Ambil parameter filter dari GET request
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');
$kelasId = isset($_GET['kelas_id']) ? intval($_GET['kelas_id']) : 0;
$mapelId = isset($_GET['mapel_id']) ? intval($_GET['mapel_id']) : 0;

// Validasi input
if (!$kelasId || !$startDate || !$endDate) {
    die('Parameter filter tidak lengkap.');
}
if (strtotime($endDate) < strtotime($startDate)) {
    die('Tanggal akhir tidak boleh sebelum tanggal awal.');
}

// Ambil nama kelas
$kelasQuery = "SELECT nama_kelas FROM kelas WHERE id = ?";
$stmt = $koneksi->prepare($kelasQuery);
if ($stmt === false) {
    error_log('Prepare failed for kelas query: ' . $koneksi->error);
    die('Terjadi kesalahan dalam query kelas: ' . htmlspecialchars($koneksi->error));
}
$stmt->bind_param('i', $kelasId);
$stmt->execute();
$kelasResult = $stmt->get_result();
$kelas = $kelasResult->fetch_assoc();
$namaKelas = $kelas ? htmlspecialchars($kelas['nama_kelas']) : 'Unknown';
$stmt->close();

// Ambil nama mata pelajaran jika ada
$namaMapel = 'Semua Mata Pelajaran';
if ($mapelId > 0) {
    $mapelQuery = "SELECT mata_pelajaran FROM pelajaran WHERE id = ?";
    $stmt = $koneksi->prepare($mapelQuery);
    if ($stmt === false) {
        error_log('Prepare failed for mapel query: ' . $koneksi->error);
        die('Terjadi kesalahan dalam query mata pelajaran: ' . htmlspecialchars($koneksi->error));
    }
    $stmt->bind_param('i', $mapelId);
    $stmt->execute();
    $mapelResult = $stmt->get_result();
    $mapel = $mapelResult->fetch_assoc();
    $namaMapel = $mapel ? htmlspecialchars($mapel['mata_pelajaran']) : 'Tidak Diketahui';
    $stmt->close();
}

// Ambil tanggal unik dalam rentang
$tanggalQuery = "SELECT DISTINCT a.tanggal FROM absensi a JOIN siswa s ON a.siswa_id = s.id WHERE a.tanggal BETWEEN ? AND ? AND s.kelas_id = ? ";
$params = [$startDate, $endDate, $kelasId];
$types = 'ssi';
if ($mapelId > 0) {
    $tanggalQuery .= "AND a.id_mapel = ? ";
    $types .= 'i';
    $params[] = $mapelId;
}
$tanggalQuery .= "ORDER BY a.tanggal ASC";
$stmt = $koneksi->prepare($tanggalQuery);
if ($stmt === false) {
    error_log('Prepare failed for tanggal query: ' . $koneksi->error);
    die('Terjadi kesalahan dalam query tanggal: ' . htmlspecialchars($koneksi->error));
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$tanggalResult = $stmt->get_result();
$tanggalList = [];
while ($row = $tanggalResult->fetch_assoc()) {
    $tanggalList[] = $row['tanggal'];
}
$stmt->close();

if (empty($tanggalList)) {
    die('Tidak ada data absensi untuk periode dan kelas yang dipilih.');
}

// Ambil daftar siswa
$siswaQuery = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa ASC";
$stmt = $koneksi->prepare($siswaQuery);
if ($stmt === false) {
    error_log('Prepare failed for siswa query: ' . $koneksi->error);
    die('Terjadi kesalahan dalam query siswa: ' . htmlspecialchars($koneksi->error));
}
$stmt->bind_param('i', $kelasId);
$stmt->execute();
$siswaResult = $stmt->get_result();
$siswaList = [];
while ($row = $siswaResult->fetch_assoc()) {
    $siswaList[$row['id']] = $row['nama_siswa'];
}
$stmt->close();

if (empty($siswaList)) {
    die('Tidak ada siswa ditemukan untuk kelas yang dipilih.');
}

// Ambil data absensi dengan prioritas
$absensiQuery = "SELECT a.siswa_id, a.tanggal,
                MAX(CASE WHEN a.hadir = 1 THEN 1 ELSE 0 END) as hadir,
                MAX(CASE WHEN a.sakit = 1 THEN 1 ELSE 0 END) as sakit,
                MAX(CASE WHEN a.izin = 1 THEN 1 ELSE 0 END) as izin,
                MAX(CASE WHEN a.alpa = 1 THEN 1 ELSE 0 END) as alpa
                FROM absensi a
                JOIN siswa s ON a.siswa_id = s.id
                WHERE a.tanggal BETWEEN ? AND ? AND s.kelas_id = ? ";
$params = [$startDate, $endDate, $kelasId];
$types = 'ssi';
if ($mapelId > 0) {
    $absensiQuery .= "AND a.id_mapel = ? ";
    $types .= 'i';
    $params[] = $mapelId;
}
$absensiQuery .= "GROUP BY a.siswa_id, a.tanggal";
$stmt = $koneksi->prepare($absensiQuery);
if ($stmt === false) {
    error_log('Prepare failed for absensi query: ' . $koneksi->error);
    die('Terjadi kesalahan dalam query absensi: ' . htmlspecialchars($koneksi->error));
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$absensiResult = $stmt->get_result();
$absensiData = [];
$rekapData = [];
while ($row = $absensiResult->fetch_assoc()) {
    $siswaId = $row['siswa_id'];
    $tanggal = $row['tanggal'];
    $symbol = '';
    if ($row['hadir']) {
        $symbol = '';
    } elseif ($row['sakit']) {
        $symbol = 'S';
    } elseif ($row['izin']) {
        $symbol = 'I';
    } elseif ($row['alpa']) {
        $symbol = 'A';
    }
    if (!isset($absensiData[$siswaId])) {
        $absensiData[$siswaId] = [];
    }
    $absensiData[$siswaId][$tanggal] = $symbol;

    if (!isset($rekapData[$siswaId])) {
        $rekapData[$siswaId] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
    }
    if ($row['hadir']) {
        $rekapData[$siswaId]['H']++;
    } elseif ($row['sakit']) {
        $rekapData[$siswaId]['S']++;
    } elseif ($row['izin']) {
        $rekapData[$siswaId]['I']++;
    } elseif ($row['alpa']) {
        $rekapData[$siswaId]['A']++;
    }
}
$stmt->close();
$koneksi->close();

// Buat dokumen PDF baru
class MYPDF extends TCPDF {
    public function Header() {
        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 10, 'Laporan Absensi Siswa - ' . $this->namaKelas, 0, 1, 'C');
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'Periode: ' . date('d F Y', strtotime($this->startDate)) . ' s.d. ' . date('d F Y', strtotime($this->endDate)), 0, 1, 'C');
        $this->Cell(0, 5, 'Mata Pelajaran: ' . $this->namaMapel, 0, 1, 'C');
        $this->Ln(10); // Tingkatkan jarak sebelum tabel
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Dicetak pada: ' . date('d/m/Y H:i') . ' WIB - Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    public $namaKelas;
    public $namaMapel;
    public $startDate;
    public $endDate;
}

// Hindari ketergantungan pada konstanta global TCPDF
$pdf = new MYPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Set informasi dokumen
$pdf->SetCreator('TCPDF');
$pdf->SetAuthor('Sistem Absensi Guru');
$pdf->SetTitle('Laporan Absensi Siswa');
$pdf->SetSubject('Attendance Report');
$pdf->SetKeywords('attendance, report, student');

// Set properti
$pdf->namaKelas = $namaKelas;
$pdf->namaMapel = $namaMapel;
$pdf->startDate = $startDate;
$pdf->endDate = $endDate;

// Set margin dan auto page breaks
$pdf->SetMargins(15, 30, 15); // Margin kiri, atas, kanan
$pdf->SetHeaderMargin(10);
$pdf->SetFooterMargin(15);
$pdf->SetAutoPageBreak(TRUE, 25); // Tingkatkan untuk penanganan tabel yang lebih baik

// Tambah halaman
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 9); // Ukuran font dikurangi untuk menyesuaikan kertas

// Hitung lebar tabel dan posisi kolom
$totalWidth = 160; // Lebar total sesuai dengan kertas A4 (190mm - 2 x 15mm margin)
$colWidthNo = 10; // Kolom No
$colWidthName = 50; // Kolom Nama Siswa
$colWidthDate = ($totalWidth - $colWidthNo - $colWidthName - (2 * 12)) / count($tanggalList); // Lebar kolom tanggal disesuaikan dinamis
$colWidthSummary = 12; // Kolom ringkasan (H, I, S, A)

// Baris header dengan styling
$html = '<table border="1" cellpadding="2" cellspacing="0" style="border-collapse: collapse; font-size: 9px; width: ' . $totalWidth . 'mm;">';
$html .= '<thead><tr style="background-color: #e2e8f0;">';
$html .= '<th width="' . $colWidthNo . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">No</th>';
$html .= '<th width="' . $colWidthName . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">Nama Siswa</th>';
foreach ($tanggalList as $tanggal) {
    $html .= '<th width="' . $colWidthDate . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">' . date('d', strtotime($tanggal)) . '</th>';
}
$html .= '<th width="' . $colWidthSummary . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">H</th>';
$html .= '<th width="' . $colWidthSummary . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">I</th>';
$html .= '<th width="' . $colWidthSummary . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">S</th>';
$html .= '<th width="' . $colWidthSummary . 'mm" style="text-align: center; font-weight: bold; border: 1px solid #000;">A</th>';
$html .= '</tr></thead><tbody>';

// Baris data
$no = 1;
foreach ($siswaList as $siswaId => $namaSiswa) {
    $html .= '<tr>';
    $html .= '<td width="' . $colWidthNo . 'mm" style="text-align: center; border: 1px solid #000; padding: 2px;">' . $no++ . '</td>';
    $html .= '<td width="' . $colWidthName . 'mm" style="text-align: left; border: 1px solid #000; padding: 2px; word-break: break-all;">' . htmlspecialchars($namaSiswa, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
    foreach ($tanggalList as $tanggal) {
        $symbol = $absensiData[$siswaId][$tanggal] ?? '';
        $html .= '<td width="' . $colWidthDate . 'mm" style="text-align: center; border: 1px solid #000; padding: 2px;">' . $symbol . '</td>';
    }
    $h = $rekapData[$siswaId]['H'] ?? 0;
    $i = $rekapData[$siswaId]['I'] ?? 0;
    $s = $rekapData[$siswaId]['S'] ?? 0;
    $a = $rekapData[$siswaId]['A'] ?? 0;
    $html .= '<td width="' . $colWidthSummary . 'mm" style="text-align: center; border: 1px solid #000; padding: 2px;">' . $h . '</td>';
    $html .= '<td width="' . $colWidthSummary . 'mm" style="text-align: center; border: 1px solid #000; padding: 2px;">' . $i . '</td>';
    $html .= '<td width="' . $colWidthSummary . 'mm" style="text-align: center; border: 1px solid #000; padding: 2px;">' . $s . '</td>';
    $html .= '<td width="' . $colWidthSummary . 'mm" style="text-align: center; border: 1px solid #000; padding: 2px;">' . $a . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody></table>';

// Keluarkan konten HTML
// Tulis HTML ke PDF
$pdf->writeHTML($html, true, false, true, false, '');

// Tutup dan keluarkan dokumen PDF
$filename = 'Laporan_Absensi_' . str_replace(' ', '_', $namaKelas) . '_' . date('Ymd', strtotime($startDate)) . '_to_' . date('Ymd', strtotime($endDate)) . '.pdf';

// Pastikan tidak ada output buffer yang tertinggal agar PDF tidak korup
if (function_exists('ob_get_length') && ob_get_length()) {
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
}

$pdf->Output($filename, 'D');
?>
