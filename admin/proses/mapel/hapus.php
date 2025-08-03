<?php
session_start();
include('../../../koneksi.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM pelajaran WHERE id = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../../mapel.php");
    } else {
        echo "<script>
            alert('Gagal menghapus data: " . mysqli_error($koneksi) . "');
            window.location='../../mapel.php';
        </script>";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>