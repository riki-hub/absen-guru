<?php
// Adjust the path to koneksi.php based on your project structure
include '../../../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if connection is successful
    if (!$koneksi) {
        die("Koneksi gagal: " . mysqli_connect_error());
    }

    $nama_siswa = $_POST['nama_siswa'];
    $kelas_id = $_POST['kelas_id'];

    // Use prepared statement to prevent SQL injection
    $query = "INSERT INTO siswa (nama_siswa, kelas_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $nama_siswa, $kelas_id);
        $result = mysqli_stmt_execute($stmt);

        if ($result) {
            header("Location: ../../siswa.php?add=success");
            exit();
        } else {
            echo "Gagal simpan: " . mysqli_error($koneksi);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Gagal menyiapkan statement: " . mysqli_error($koneksi);
    }
}
?>