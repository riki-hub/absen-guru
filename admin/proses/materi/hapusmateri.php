<?php
session_start();

// Validasi sesi admin
if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../");
    exit();
}

include '../../../koneksi.php';

// Validasi parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID tidak valid!'); window.location.href='../../../agenda.php';</script>";
    exit();
}

$id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Ambil data materi untuk mendapatkan kelas_id dan tanggal_upload
$sql = "SELECT kelas_id, tanggal_upload FROM materi WHERE id = '$id'";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $kelas_id = $row['kelas_id'];
    $tanggal_upload = $row['tanggal_upload'];

    // Hapus data absensi berdasarkan kelas_id dan tanggal
    $hapus_absensi = "DELETE FROM absensi WHERE tanggal = '$tanggal_upload' AND siswa_id IN (
        SELECT id FROM siswa WHERE kelas_id = '$kelas_id'
    )";
    $koneksi->query($hapus_absensi);

    // Hapus data materi
    $delete_sql = "DELETE FROM materi WHERE id = '$id'";
    if ($koneksi->query($delete_sql) === TRUE) {
        // Redirect ke halaman agenda
        echo "<script>alert('Data agenda dan absensi berhasil dihapus!'); window.location.href='http://localhost/guru/admin/agenda.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data materi: " . addslashes($koneksi->error) . "'); window.location.href='http://localhost/guru/admin/agenda.php';</script>";
    }
} else {
    echo "<script>alert('Data materi tidak ditemukan!'); window.location.href='http://localhost/guru/admin/agenda.php';</script>";
}

// Tutup koneksi
$koneksi->close();
?>