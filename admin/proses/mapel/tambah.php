<?php
session_start();
include('../../../koneksi.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mata_pelajaran = $_POST['mata_pelajaran'];

    $query = "INSERT INTO pelajaran (mata_pelajaran) VALUES (?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $mata_pelajaran);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>
            alert('Data mata pelajaran berhasil ditambahkan!');
            window.location='../../mapel.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menambahkan data: " . mysqli_error($koneksi) . "');
            window.location='../../mapel.php';
        </script>";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($koneksi);
}
?>