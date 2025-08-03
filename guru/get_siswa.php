<?php
header('Content-Type: application/json');
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kelas_id']) && is_numeric($_POST['kelas_id'])) {
    $kelasId = (int)$_POST['kelas_id'];
    $query = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ?";
    $stmt = $koneksi->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('i', $kelasId);
        $stmt->execute();
        $result = $stmt->get_result();
        $siswaList = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        // Escape nama_siswa untuk mencegah XSS
        foreach ($siswaList as &$siswa) {
            $siswa['nama_siswa'] = htmlspecialchars($siswa['nama_siswa']);
        }
        
        echo json_encode($siswaList);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>