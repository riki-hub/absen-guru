<?php
include '../koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Daftar Materi</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
  <h4>Daftar Materi</h4>
  <ul class="list-group">
    <?php
    $result = $koneksi->query("SELECT * FROM materi ORDER BY tanggal_upload DESC");
    while ($row = $result->fetch_assoc()) {
        echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
        echo htmlspecialchars($row['nama_materi']);
        echo '<a class="btn btn-sm btn-success" href="' . htmlspecialchars($row['file_path']) . '" target="_blank">Unduh</a>';
        echo '</li>';
    }
    ?>
  </ul>
</div>
</body>
</html>
