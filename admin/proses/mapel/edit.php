<?php
session_start();
include('../../../koneksi.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $mata_pelajaran = $_POST['mata_pelajaran'];

    $query = "UPDATE pelajaran SET mata_pelajaran = ? WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "si", $mata_pelajaran, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Data mata pelajaran berhasil diperbarui!');
            window.location='../../mapel.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');
            window.location='../../mapel.php';
        </script>";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>