<?php
session_start();
include 'koneksi.php';

if (isset($_POST['cek'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query untuk tabel admin
    $adminQuery = "SELECT * FROM admin WHERE username = ?";
    $stmtAdmin = $koneksi->prepare($adminQuery);
    if ($stmtAdmin === false) {
        die('Query prepare failed: ' . $koneksi->error);
    }
    $stmtAdmin->bind_param("s", $username);
    $stmtAdmin->execute();
    $resultAdmin = $stmtAdmin->get_result();

    if ($resultAdmin->num_rows > 0) {
        $adminData = $resultAdmin->fetch_assoc();

        // Verifikasi password tanpa enkripsi
        if ($password === $adminData['password']) {
            $_SESSION['user_id'] = $adminData['id'];
            $_SESSION['role'] = 'admin';
            $_SESSION['username'] = $adminData['username'];
            $_SESSION['nama'] = $adminData['nama'];
            $_SESSION['gambar'] = $adminData['gambar'];
            header("Location: admin/index.php");
            exit();
        }
    }

    // Jika username dan password tidak cocok dengan data admin
    header("Location: admin.php?error=1");
    exit();
} else {
    // Jika form tidak disubmit
    header("Location: admin.php");
    exit();
}
