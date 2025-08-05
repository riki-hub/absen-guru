<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=data_mapel.xls");

include('../koneksi.php');

echo "<table border='1'>";
echo "<tr>
        <th>No</th>
        <th>Mata Pelajaran</th>
      </tr>";

$query = "SELECT * FROM pelajaran";
$result = mysqli_query($koneksi, $query);
$no = 1;
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td align='center'>" . $no++ . "</td>";
    echo "<td>" . htmlspecialchars($row['mata_pelajaran']) . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
