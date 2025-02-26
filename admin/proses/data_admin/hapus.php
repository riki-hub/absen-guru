<?php
include('../../../koneksi.php');
$id = $_GET['id'];

$id = mysqli_real_escape_string($koneksi, $id);

$result = mysqli_query($koneksi, "DELETE FROM admin WHERE id = '$id'");
$cek = mysqli_affected_rows($koneksi);
