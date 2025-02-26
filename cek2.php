<?php
session_start();
include 'koneksi.php';



if (isset($_POST['cek'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];


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

        // Jika username dan password tidak cocok dengan data admin atau guru
        header("Location: index.php?error=1");
        exit();
    } else {
        // Jika lokasi di luar jangkauan
        header("Location: index.php?error=2");
        exit();
    }


