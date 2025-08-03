<?php
include('../../../koneksi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nama = $_POST['nama_siswa'];
    $username = $_POST['kelas_id'];


    $query = "UPDATE siswa SET nama_siswa='$nama', kelas_id='$username' WHERE id='$id'";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        header("Location: ../../siswa.php?update=success");
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}
?>
