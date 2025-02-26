<?php
include '../../../koneksi.php';

$result = mysqli_query($koneksi, "DELETE FROM absen_msk");
$cek = mysqli_affected_rows($koneksi);
