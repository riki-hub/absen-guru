<?php
// Include koneksi database
include('config.php');  // Sesuaikan dengan file koneksi Anda

// Ambil parameter pencarian
$query = isset($_GET['q']) ? $_GET['q'] : '';

// Query untuk mencari nama guru yang sesuai
$sql = "SELECT nama FROM akun_guru WHERE nama LIKE ? LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $search);
$search = "%" . $query . "%";
$stmt->execute();
$result = $stmt->get_result();

// Menyiapkan array hasil pencarian
$gurus = [];
while ($row = $result->fetch_assoc()) {
    $gurus[] = $row['nama'];
}

// Mengembalikan hasil dalam format JSON
echo json_encode($gurus);
?>
