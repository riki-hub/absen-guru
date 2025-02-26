<?php
session_start();
include 'koneksi.php';

// Set koordinat yang diperbolehkan
$allowed_latitude = -6.408622; // Adjust with the correct coordinates
$allowed_longitude = 106.9046324; // Adjust with the correct coordinates
$allowed_radius = 900 / 111320; // Radius dalam derajat (500 meter)

if (isset($_POST['cek'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Periksa apakah koordinat pengguna berada dalam radius yang diperbolehkan
    if (abs($latitude - $allowed_latitude) <= $allowed_radius && abs($longitude - $allowed_longitude) <= $allowed_radius) {

        // Query untuk tabel user (akun_guru)
        $userQuery = "SELECT * FROM akun_guru WHERE username = ?";
        $stmtUser = $koneksi->prepare($userQuery);
        if ($stmtUser === false) {
            die('Query prepare failed: ' . $koneksi->error);
        }
        $stmtUser->bind_param("s", $username);
        $stmtUser->execute();
        $resultUser = $stmtUser->get_result();

        if ($resultUser->num_rows > 0) {
            $userData = $resultUser->fetch_assoc();

            // Verifikasi password tanpa enkripsi
            if ($password === $userData['password']) {
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['role'] = 'guru';
                $_SESSION['username'] = $userData['username'];
                $_SESSION['nama'] = $userData['nama'];
                $_SESSION['gambar'] = $userData['gambar'];
                header("Location: guru/index.php");
                exit();
            }
        }

        // Jika username dan password tidak cocok dengan data guru
        header("Location: index.php?error=1");
        exit();
    } else {
        // Jika lokasi di luar jangkauan
        header("Location: index.php?error=2");
        exit();
    }
} else {
    // Jika form tidak disubmit
    header("Location: index.php");
    exit();
}
