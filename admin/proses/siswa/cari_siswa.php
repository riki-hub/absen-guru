<?php
include '../../koneksi.php';

if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $query = "SELECT * FROM siswa WHERE LOWER(nama_siswa) LIKE LOWER('$keyword%') ORDER BY nama_siswa ASC LIMIT 10";
    $result = mysqli_query($koneksi, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<a href="#" class="list-group-item list-group-item-action item-guru">'
             . htmlspecialchars($row['nama_siswa']) .
             '</a>';
    }
}
?>
