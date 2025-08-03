<?php
session_start();
include '../koneksi.php';

// Set header untuk response JSON
header('Content-Type: application/json');

// Fungsi untuk mengirim response JSON
function sendResponse($success, $message) {
    echo json_encode([
        'success' => $success,
        'message' => $message
    ]);
    exit;
}

// Periksa apakah user sudah login
if (!isset($_SESSION['username'])) {
    sendResponse(false, 'Silakan login terlebih dahulu.');
}

// Periksa apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Metode request tidak valid.');
}

// Ambil data dari form
$siswa_id = isset($_POST['siswa_id']) ? intval($_POST['siswa_id']) : 0;
$tanggal = isset($_POST['tanggal']) ? trim($_POST['tanggal']) : '';
$kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;
$mapel_id = isset($_POST['mapel_id']) && !empty($_POST['mapel_id']) ? intval($_POST['mapel_id']) : null;
$hadir = isset($_POST['hadir']) ? 1 : 0;
$sakit = isset($_POST['sakit']) ? 1 : 0;
$izin = isset($_POST['izin']) ? 1 : 0;
$alpa = isset($_POST['alpa']) ? 1 : 0;

// Validasi input
if ($siswa_id <= 0 || empty($tanggal) || $kelas_id <= 0) {
    sendResponse(false, 'Data siswa, tanggal, atau kelas tidak valid.');
}

// Validasi hanya satu status kehadiran yang dipilih
$status_count = $hadir + $sakit + $izin + $alpa;
if ($status_count > 1) {
    sendResponse(false, 'Hanya satu status kehadiran yang dapat dipilih.');
}
if ($status_count === 0) {
    sendResponse(false, 'Pilih salah satu status kehadiran.');
}

// Validasi format tanggal
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || !strtotime($tanggal)) {
    sendResponse(false, 'Format tanggal tidak valid.');
}

// Siapkan query untuk memeriksa apakah data absensi sudah ada
$check_sql = "SELECT id FROM absensi WHERE siswa_id = ? AND tanggal = ? AND kelas_id = ?";
$params = [$siswa_id, $tanggal, $kelas_id];
$types = 'isi';

if ($mapel_id !== null) {
    $check_sql .= " AND id_mapel = ?";
    $params[] = $mapel_id;
    $types .= 'i';
}

$stmt = $koneksi->prepare($check_sql);
if (!$stmt) {
    sendResponse(false, 'Gagal mempersiapkan query: ' . $koneksi->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$existing_id = $result->fetch_assoc()['id'] ?? null;
$stmt->close();

try {
    // Mulai transaksi
    $koneksi->begin_transaction();

    if ($existing_id) {
        // Update data absensi yang sudah ada
        $update_sql = "UPDATE absensi SET hadir = ?, sakit = ?, izin = ?, alpa = ? WHERE id = ?";
        $stmt = $koneksi->prepare($update_sql);
        if (!$stmt) {
            throw new Exception('Gagal mempersiapkan query update: ' . $koneksi->error);
        }
        $stmt->bind_param('iiiii', $hadir, $sakit, $izin, $alpa, $existing_id);
    } else {
        // Insert data absensi baru
        $insert_sql = "INSERT INTO absensi (siswa_id, tanggal, kelas_id, id_mapel, hadir, sakit, izin, alpa) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($insert_sql);
        if (!$stmt) {
            throw new Exception('Gagal mempersiapkan query insert: ' . $koneksi->error);
        }
        $stmt->bind_param('isiiiiii', $siswa_id, $tanggal, $kelas_id, $mapel_id, $hadir, $sakit, $izin, $alpa);
    }

    // Eksekusi query
    if (!$stmt->execute()) {
        throw new Exception('Gagal menyimpan data absensi: ' . $stmt->error);
    }

    // Commit transaksi
    $koneksi->commit();
    $stmt->close();

    sendResponse(true, 'Data kehadiran berhasil diperbarui.');
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $koneksi->rollback();
    sendResponse(false, $e->getMessage());
} finally {
    $koneksi->close();
}
?>