<?php
include '../../../koneksi.php';

$result = mysqli_query($koneksi, "DELETE FROM izin");
$cek = mysqli_affected_rows($koneksi);
