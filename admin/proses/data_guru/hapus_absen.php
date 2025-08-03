<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus</title>
</head>

<body>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>

<?php
include '../../../koneksi.php';  // Pastikan koneksi ke database benar

if (isset($_GET['id'])) {
    $id = $_GET['id']; // Ambil ID dari URL

    // Query untuk menghapus data berdasarkan ID
    $sql = "DELETE FROM absen_msk WHERE id = ?";
    $stmt = $koneksi->prepare($sql);

    if ($stmt === false) {
        echo "Error dalam menyiapkan query: " . $koneksi->error;
        exit();
    }

    $stmt->bind_param("i", $id);  // Bind ID dengan tipe integer

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<script> 
                Swal.fire({
                    icon: 'success',
                    title: 'Data berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../data.php'; // Redirect ke halaman sebelumnya
                });
              </script>";
    } else {
        echo "Terjadi kesalahan saat menghapus data: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $koneksi->close();
} else {
    echo "ID tidak ditemukan!";
}
?>
