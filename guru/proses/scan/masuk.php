<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah</title>
</head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>

</body>


</html>
<?php
session_start();
include('../../../koneksi.php');

$guru = $_POST['nama_guru'];
$tanggal = $_POST['beda'];
$jam_masuk = $_POST['jamcuy'];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$kordinat = $_POST['kordinat'];
$kelas = $_POST['kelas'];
$photoData = $_POST['photo'];





$kec = "Terakhir absen di kelas ,$kelas";
if (!empty($photoData)) {
    $folderPath = "../../../absen/";
    $image_parts = explode(";base64,", $photoData);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $fileName = uniqid() . '.png';
    $file = $folderPath . $fileName;

    file_put_contents($file, $image_base64);
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Wajib memfoto saat anda di kelas',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = '../../index.php';
        });
    </script>";
    exit();
}
$jam = intval(explode('.', $jam_masuk)[0]);
$menit = intval(explode('.', $jam_masuk)[1]);
$hari = date('l'); // Mendapatkan nama hari (misal: 'Monday' untuk Senin)
$kolom_jam = '';
$telat = false;
$isIstirahat = false;


// Jika waktu istirahat, tampilkan SweetAlert dan hentikan proses absen
if ($isIstirahat) {
    echo "<script>
    Swal.fire({
        icon: 'warning',
        title: 'Waktu Istirahat',
        text: 'Saat ini adalah waktu istirahat, absen tidak dilakukan.',
        showConfirmButton: false,
        timer: 2000
    }).then(() => {
        window.location.href = '../../index.php';
    });
</script>";
    exit(); // Menghentikan eksekusi script
}

if ($hari == 'Monday') { // Logika khusus untuk hari Senin
    if ($jam == 8 && $menit < 30) {
        $kolom_jam = 'jam1';
    } else if (($jam == 8 && $menit >= 35) || ($jam == 9 && $menit < 5)) {
        $kolom_jam = 'jam2';
    } else if ($jam == 9 && $menit < 40) {
        $kolom_jam = 'jam3';
    } else if (($jam == 9 && $menit >= 45) || $jam == 10 && $menit < 15) {
        $kolom_jam = 'jam4';
    } else if ($jam == 10 && $menit < 45) {
        $kolom_jam = 'Istirahat 1';
    } else if (($jam == 10 && $menit >= 50 && $menit < 55) || ($jam == 11 && $menit < 15)) {
        $kolom_jam = 'jam5';
    } else if ($jam == 11 && $menit >= 20 && $menit < 55) {
        $kolom_jam = 'jam6';
    } else if ($jam == 12 && $menit < 30) {
        $kolom_jam = 'Istirahat 2';
    } else if ($jam == 12 && $menit >= 30 && $menit < 60) {
        $kolom_jam = 'jam7';
    } else if ($jam == 13 && $menit < 35 ) {
        $kolom_jam = 'jam8';
    } else if (($jam == 13 && $menit >= 40) || ($jam == 14 && $menit < 10)) {
        $kolom_jam = 'jam9';
    } else if ($jam == 14 && $menit < 30) {
        $kolom_jam = 'jam10';
    }
}

else if ($hari == 'Friday') {
    // Logika untuk hari Jumat
    if (($jam == 7 && $menit >= 45) || ($jam == 8 && $menit < 15)) {
        $kolom_jam = 'jam1'; // 07:45 - 08:19
    } else if (($jam == 8 && $menit >= 20) || ($jam == 8 && $menit < 50)) {
        $kolom_jam = 'jam2'; // 08:20 - 08:54
    } else if (($jam == 8 && $menit >= 55) || ($jam == 9 && $menit < 25)) {
        $kolom_jam = 'jam3'; // 08:55 - 09:29
    } else if (($jam == 9 && $menit >= 30) || ($jam == 9 && $menit < 50)) {
        $kolom_jam = 'istirahat'; // Istirahat (09:30 - 09:49)
    } else if (($jam == 9 && $menit >= 50) || ($jam == 10 && $menit < 20)) {
        $kolom_jam = 'jam4'; // 09:50 - 10:24
    } else if ($jam == 10 && $menit >= 25 && $menit < 55) {
        $kolom_jam = 'jam5'; // 10:25 - 10:59
    } else if (($jam == 11 && $menit >= 0) && ($jam == 11 && $menit < 30)) {
        $kolom_jam = 'jam6'; // 11:00 - 11:34
    }
}

else if ($hari == 'Tuesday' ||  $hari == 'Wednesday' || $hari == 'Thursday') { // Logika khusus untuk hari selasa - kamis
    if ($jam == 7 && $menit >= 30 || ($jam == 8 && $menit < 0)) {
        $kolom_jam = 'jam1'; // 07:30 - 08:05
    } else if (($jam == 8 && $menit >= 5) && ($jam == 8 && $menit < 35)) {
        $kolom_jam = 'jam2'; // 08:05 - 08:40
    } else if (($jam == 8 && $menit >= 40) || ($jam == 9 && $menit < 10)) {
        $kolom_jam = 'jam3'; // 08:40 - 09:15
    } else if (($jam == 9 && $menit >= 15) || ($jam == 9 && $menit < 45)) {
        $kolom_jam = 'jam4'; // 09:15 - 09:50
    } else if ($jam == 9 && $menit >= 50 && $jam == 10 && $menit < 25) {
        $kolom_jam = 'Istirahat 1'; // 09:50 - 10:25
    } else if ($jam == 10 && $menit >= 25 && $menit < 55) {
        $kolom_jam = 'jam5'; // 10:25 - 11:00
    } else if ($jam == 11 && $menit >= 0  && $menit < 30) {
        $kolom_jam = 'jam6'; // 11:10 - 11:35
    } else if (($jam == 11 && $menit >= 35) || ($jam == 12 && $menit < 5)) {
        $kolom_jam = 'jam7'; // 11:45 - 12:20
    } else if ($jam == 12 && $menit >= 10 && $menit < 45) {
        $kolom_jam = 'Istirahat 2'; // 12:20 - 12:50
    } else if (($jam == 12 && $menit >= 45) || ($jam == 13 && $menit < 15)) {
        $kolom_jam = 'jam8'; // 12:50 - 13:25
    } else if ($jam == 13 && $menit >= 20 && $menit < 50) {
        $kolom_jam = 'jam9'; // 13:25 - 14:00
    } else if (($jam == 13 && $menit >= 55) || ($jam == 14 && $menit < 25)) {
        $kolom_jam = 'jam10'; // 14:00 - 14:35
    }
}


if (!empty($kolom_jam)) {
    $query_check = "SELECT * FROM absen_msk WHERE nama = '$guru' AND tanggal = '$tanggal'";
    $result = mysqli_query($koneksi, $query_check);

    if (!$result) {
        error_log("Query Check Error: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (empty($row[$kolom_jam])) {
            $oldFileName = $row['foto_absen'];
            if (!empty($oldFileName) && file_exists($folderPath . $oldFileName)) {
                unlink($folderPath . $oldFileName); // Menghapus file lama
            }
            $query_update = "UPDATE absen_msk SET $kolom_jam = '$kelas', foto_absen = '$fileName',activity='$kec' WHERE nama = '$guru' AND tanggal = '$tanggal'";
            if (mysqli_query($koneksi, $query_update)) {
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = '../../index.php';
                    });
                </script>";
                exit();
            } else {
                error_log("Update Error: " . $query_update . "<br>" . mysqli_error($koneksi));
                echo "Error: " . $query_update . "<br>" . mysqli_error($koneksi);
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'info',
                    title: 'Pemberitahuan',
                    text: 'Anda sudah melakukan absen untuk jam ke $jam_masuk',
                    showConfirmButton: false,
                    timer: 2500
                }).then(() => {
                    window.location.href = '../../index.php';
                });
            </script>";
        }
    } else {
        $query_insert = "INSERT INTO absen_msk (nama, tanggal, $kolom_jam, kordinat,activity, foto_absen) VALUES ('$guru', '$tanggal', '$kelas', '$kordinat','$kec', '$fileName')";
        if (mysqli_query($koneksi, $query_insert)) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Bagus,kamu berhasil absen',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../index.php';
                });
            </script>";
            exit();
        } else {
            error_log("Insert Error: " . $query_insert . "<br>" . mysqli_error($koneksi));
            echo "Error: " . $query_insert . "<br>" . mysqli_error($koneksi);
        }
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = '../../index.php';
        });
    </script>";
}

mysqli_close($koneksi);
