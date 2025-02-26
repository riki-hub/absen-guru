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
include '../../../koneksi.php';

$nama = $_POST['nama_guru'];
$tanggal = $_POST['tanggal'];
$keterangan = $_POST['keterangan'];
$photoData = $_POST['photo'];
$status = "belum di acc";
$jenis = "izin";


// Cek apakah user sudah izin di hari yang sama
$sql_cek = "SELECT * FROM izin WHERE nama = ? AND tanggal = ?";
$stmt_cek = $koneksi->prepare($sql_cek);
$stmt_cek->bind_param("ss", $nama, $tanggal);
$stmt_cek->execute();
$result = $stmt_cek->get_result();

if ($result->num_rows > 0) {
    // Jika sudah ada izin di hari yang sama
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: 'Anda sudah mengajukan izin hari ini',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = '../../index.php';
        });
    </script>";
    exit;
}

// Jika belum ada izin, lanjutkan dengan menyimpan data izin
if (!empty($photoData)) {
    $folderPath = "../../../izin/";
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
    exit;
}

$sql = "INSERT INTO izin (tanggal, nama, jenis, keterangan, gambar, status) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ssssss", $tanggal, $nama, $jenis, $keterangan, $fileName, $status);

if ($stmt->execute()) {
    echo "<script> Swal.fire({
icon: 'success',
title: 'Berhasil',
showConfirmButton: false,
timer: 1500
}).then(() => {
window.location.href = '../../index.php';
});
</script>";
} else {
    echo "Terjadi kesalahan: " . $stmt->error;
}

// Tutup statement dan koneksi
$stmt_cek->close();
$stmt->close();
$koneksi->close();
