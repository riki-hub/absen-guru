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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama_kelas'];

    $sql = "INSERT INTO kelas(nama_kelas) VALUES ( ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $nama);

    if ($stmt->execute()) {
        echo "<script> Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    showConfirmButton: false,
    timer: 1500
  }).then(() => {
    window.location.href = '../../kelas.php';
  });
</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $koneksi->close();
}
