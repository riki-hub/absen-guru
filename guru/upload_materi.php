<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href = 'login.php';</script>";
    exit;
}

// Tangani form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal_upload'];
    $mapel = $_POST['mapel']; // Nama mata pelajaran
    $kelas_id = $_POST['kelas'];
    $nama_guru = $_SESSION['username']; // Ambil nama guru dari session

    // Validasi input
    if (empty($tanggal) || empty($mapel) || empty($kelas_id)) {
        echo "<script>alert('Lengkapi semua kolom yang diperlukan (tanggal, mata pelajaran, dan kelas).'); window.history.back();</script>";
        exit;
    }

    // Validasi format tanggal (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        echo "<script>alert('Format tanggal tidak valid! Harus YYYY-MM-DD'); window.history.back();</script>";
        exit;
    }

    // Mulai transaksi
    $koneksi->begin_transaction();

    try {
        // Simpan data ke tabel materi (tanpa kolom nama_materi)
        $sql_materi = "INSERT INTO materi (nama_guru, mapel, kelas_id, tanggal_upload) VALUES (?, ?, ?, ?)";
        $stmt_materi = $koneksi->prepare($sql_materi);
        if ($stmt_materi) {
            $stmt_materi->bind_param('ssis', $nama_guru, $mapel, $kelas_id, $tanggal);
            if (!$stmt_materi->execute()) {
                throw new Exception('Gagal menyimpan data materi: ' . $stmt_materi->error);
            }
            $stmt_materi->close();
        } else {
            throw new Exception('Gagal mempersiapkan query materi: ' . $koneksi->error);
        }

        // Tangani data absensi
        $siswa_ids = array_filter(array_keys($_POST), function($key) {
            return strpos($key, 'status_') === 0;
        });

        if (empty($siswa_ids)) {
            throw new Exception('Tidak ada data absensi siswa yang dipilih.');
        }

        foreach ($siswa_ids as $key) {
            $siswa_id = str_replace('status_', '', $key);
            $status = $_POST[$key];

            // Status absensi
            $hadir = $status === 'hadir' ? 1 : 0;
            $sakit = $status === 'sakit' ? 1 : 0;
            $izin  = $status === 'izin'  ? 1 : 0;
            $alpa  = $status === 'alpa'  ? 1 : 0;

            // Cek apakah data absensi sudah ada
            $sql_check = "SELECT id FROM absensi WHERE siswa_id = ? AND tanggal = ? AND kelas_id = ? AND id_mapel = (SELECT id FROM pelajaran WHERE mata_pelajaran = ? LIMIT 1)";
            $stmt_check = $koneksi->prepare($sql_check);
            if (!$stmt_check) {
                throw new Exception('Gagal mempersiapkan query cek absensi: ' . $koneksi->error);
            }
            $stmt_check->bind_param('isis', $siswa_id, $tanggal, $kelas_id, $mapel);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                throw new Exception('Data absensi untuk siswa ID ' . $siswa_id . ' pada tanggal, kelas, dan mata pelajaran ini sudah ada.');
            }
            $stmt_check->close();

            // Simpan absensi
            $sql_absensi = "INSERT INTO absensi (siswa_id, tanggal, kelas_id, id_mapel, hadir, sakit, izin, alpa) 
                            VALUES (?, ?, ?, (SELECT id FROM pelajaran WHERE mata_pelajaran = ? LIMIT 1), ?, ?, ?, ?)";
            $stmt_absensi = $koneksi->prepare($sql_absensi);
            if ($stmt_absensi) {
                $stmt_absensi->bind_param('isissiii', $siswa_id, $tanggal, $kelas_id, $mapel, $hadir, $sakit, $izin, $alpa);
                if (!$stmt_absensi->execute()) {
                    throw new Exception('Gagal menyimpan absensi untuk siswa ID ' . $siswa_id . ': ' . $stmt_absensi->error);
                }
                $stmt_absensi->close();
            } else {
                throw new Exception('Gagal mempersiapkan query absensi: ' . $koneksi->error);
            }
        }

        // Commit transaksi
        $koneksi->commit();
        echo "<script>alert('Data materi dan absensi berhasil disimpan!'); window.location.href = 't_agenda.php';</script>";
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        $koneksi->rollback();
        echo "<script>alert('Gagal menyimpan data: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    } finally {
        $koneksi->close();
    }
} else {
    echo "<script>alert('Metode tidak valid.'); window.location.href = 't_agenda.php';</script>";
    exit;
}
?>