<?php
include '../../../koneksi.php'; // Pastikan koneksi benar

if (isset($_GET['nama'])) {
    $nama = $_GET['nama']; // Ambil nama dari URL

    // Gunakan prepared statement untuk menghapus data berdasarkan nama
    $sql = "DELETE FROM absen_msk WHERE nama = ?";
    $stmt = $koneksi->prepare($sql);

    if ($stmt === false) {
        echo "Error dalam menyiapkan query: " . $koneksi->error;
        exit();
    }

    $stmt->bind_param("s", $nama); // Ganti "i" dengan "s" karena nama adalah string

    if ($stmt->execute()) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Hapus Bulanan</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Data berhasil dihapus',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../data_bulanan.php';
                });
            </script>
        </body>
        </html>";
    } else {
        echo "Terjadi kesalahan saat menghapus data: " . $stmt->error;
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo "Nama tidak ditemukan!";
}
?>
