<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah</title>
</head>

<body>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>

<?php
session_start();
include('../../../koneksi.php');

$guru = $_POST['nama_guru'];
$tanggal = $_POST['beda']; // Format input 'd/m/Y'
$jam_masuk = $_POST['jamcuy']; // Default waktu untuk pengujian
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$kordinat = $_POST['kordinat'];

// Debugging: log variabel input
error_log("Guru: $guru, Tanggal: $tanggal, Jam Masuk: $jam_masuk, Latitude: $latitude, Longitude: $longitude");

$jam = intval(explode(':', $jam_masuk)[0]);

// Tentukan kolom berdasarkan jam absensi
$kolom_jam = '';
if ($jam == 6) {
    $kolom_jam = 'jam0';
} else if ($jam == 7) {
    $kolom_jam = 'jam1';
} else if ($jam == 8) {
    $kolom_jam = 'jam2';
} else if ($jam == 9) {
    $kolom_jam = 'jam3';
} else if ($jam == 10) {
    $kolom_jam = 'jam4';
} else if ($jam == 11) {
    $kolom_jam = 'jam5';
} else if ($jam == 12) {
    $kolom_jam = 'jam6';
} else if ($jam == 13) {
    $kolom_jam = 'jam7';
} else if ($jam == 14) {
    $kolom_jam = 'jam8';
} else if ($jam == 15) {
    $kolom_jam = 'jam9';
} else if ($jam == 16) {
    $kolom_jam = 'jam10';
}

// Debugging: log kolom jam yang dipilih
error_log("Kolom Jam: $kolom_jam");

if (!empty($kolom_jam)) {
    // Cek apakah sudah ada data untuk guru ini pada tanggal yang sama
    $query_check = "SELECT * FROM absen_msk WHERE nama = '$guru' AND tanggal = '$tanggal'";
    $result = mysqli_query($koneksi, $query_check);

    if (!$result) {
        error_log("Query Check Error: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($result) > 0) {
        // Cek apakah sudah absen pada jam ini
        $row = mysqli_fetch_assoc($result);
        if (empty($row[$kolom_jam])) {
            // Update record yang sudah ada
            $query_update = "UPDATE absen_msk SET $kolom_jam = '$jam_masuk' WHERE nama = '$guru' AND tanggal = '$tanggal'";
            if (mysqli_query($koneksi, $query_update)) {
                echo "<script> Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    showConfirmButton: false,
                    timer: 1500
                  }).then(() => {
                    window.location.href = '../../scan.php';
                  });
                </script>";
                exit();
            } else {
                error_log("Update Error: " . $query_update . "<br>" . mysqli_error($koneksi));
                echo "Error: " . $query_update . "<br>" . mysqli_error($koneksi);
            }
        } else {
            echo "<script> Swal.fire({
                icon: 'info',
                title: 'Pemberitahuan',
                text: 'Anda sudah melakukan absen untuk jam ke $jam_masuk',
                showConfirmButton: false,
                timer: 2500
              }).then(() => {
                window.location.href = '../../scan.php';
              });
            </script>";
        }
    } else {
        // Insert record baru
        $query_insert = "INSERT INTO absen_msk (nama, tanggal, $kolom_jam,kordinat) VALUES ('$guru', '$tanggal', '$jam_masuk','$kordinat')";
        if (mysqli_query($koneksi, $query_insert)) {
            echo "<script> Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.href = '../../scan.php';
              });
            </script>";
            exit();
        } else {
            error_log("Insert Error: " . $query_insert . "<br>" . mysqli_error($koneksi));
            echo "Error: " . $query_insert . "<br>" . mysqli_error($koneksi);
        }
    }
} else {
    echo "<script> Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        showConfirmButton: false,
        timer: 1500
      }).then(() => {
        window.location.href = '../../scan.php';
      });
    </script>";
}

// Debugging: log penutupan koneksi
error_log("Connection closed.");

mysqli_close($koneksi);
