<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah absen</title>
</head>

<body>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>
<?php
include '../../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    
    // Cek apakah sudah ada data dengan tanggal yang sama
    $cek = "SELECT nama,tanggal FROM absen_msk WHERE tanggal = ? AND nama = ?";
    $stmt = $koneksi->prepare($cek);
    $stmt->bind_param('ss', $tanggal,$nama);
    $stmt->execute();
    $stmt->store_result();

    // Jika data sudah ada
    if ($stmt->num_rows > 0) {
        echo "<script> 
                Swal.fire({
                    icon: 'warning',
                    title: 'Guru ini sudah absen',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../data.php';
                });
              </script>";
        exit(); // Berhenti eksekusi lebih lanjut jika data sudah ada
    }

    // Query untuk memasukkan data absen baru
    $sql = "INSERT INTO absen_msk (nama, tanggal) VALUES (?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $nama, $tanggal);

    // Menjalankan query
    if ($stmt->execute()) {
        echo "<script> 
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../data.php';
                });
              </script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $koneksi->close();
}
?>
