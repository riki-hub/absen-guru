<?php
include '../../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Dapatkan data dari request
    $data = json_decode(file_get_contents('php://input'), true);
    $choice = $data['choice'];
    $id = $data['id'];


    // Simpan data ke dalam database
    $stmt = "UPDATE izin SET status='$choice' WHERE id ='$id'";

    if (mysqli_query($koneksi, $stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    $koneksi->close();
}
