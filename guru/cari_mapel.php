<?php
include '../koneksi.php';
if (isset($_GET['cari'])) {
  $k = mysqli_real_escape_string($koneksi, $_GET['cari']);
  $q = "SELECT * FROM pelajaran WHERE LOWER(mata_pelajaran) LIKE '$k%' ORDER BY mata_pelajaran LIMIT 10";
  $r = mysqli_query($koneksi, $q) or die(mysqli_error($koneksi));
  while ($row = mysqli_fetch_assoc($r)) {
    echo '<a href="#" class="list-group-item list-group-item-action item-mapel" data-id="' . $row['id'] . '">' . htmlspecialchars($row['mata_pelajaran']) . '</a>';
  }
}
?>
