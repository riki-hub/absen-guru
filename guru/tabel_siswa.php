<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Silakan login terlebih dahulu.</td></tr></tbody>";
    exit;
}

// Ambil parameter dari POST
$startDate = isset($_POST['startDate']) && !empty($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
$endDate = isset($_POST['endDate']) && !empty($_POST['endDate']) ? $_POST['endDate'] : $startDate;
$selectedKelas = isset($_POST['kelas_id']) && is_numeric($_POST['kelas_id']) ? intval($_POST['kelas_id']) : 0;
$mapelId = isset($_POST['mapel_id']) && is_numeric($_POST['mapel_id']) ? intval($_POST['mapel_id']) : 0;
$isSingleDate = isset($_POST['isSingleDate']) && $_POST['isSingleDate'] === 'true';
$isMultiMonth = isset($_POST['isMultiMonth']) && $_POST['isMultiMonth'] === 'true';

error_log("Parameter diterima - startDate: $startDate, endDate: $endDate, kelas_id: $selectedKelas, mapel_id: " . ($mapelId > 0 ? $mapelId : 'semua') . ", isSingleDate: $isSingleDate, isMultiMonth: $isMultiMonth");

// Validasi input
if (empty($selectedKelas)) {
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Pilih kelas terlebih dahulu.</td></tr></tbody>";
    exit;
}

if (strtotime($endDate) < strtotime($startDate)) {
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Tanggal Akhir tidak boleh sebelum Tanggal Awal.</td></tr></tbody>";
    exit;
}

// Generate array of dates
$dates = [];
$currentDate = new DateTime($startDate);
$endDateTime = new DateTime($endDate);
while ($currentDate <= $endDateTime) {
    $dates[] = $currentDate->format('Y-m-d');
    $currentDate->modify('+1 day');
}
error_log("Tanggal yang akan ditampilkan: " . implode(', ', $dates));

// Query siswa
$querySiswa = "SELECT s.id, s.nama_siswa FROM siswa s WHERE s.kelas_id = ?";
$stmtSiswa = mysqli_prepare($koneksi, $querySiswa);
if (!$stmtSiswa) {
    error_log("Error kueri siswa: " . mysqli_error($koneksi));
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Error: " . mysqli_error($koneksi) . "</td></tr></tbody>";
    exit;
}
mysqli_stmt_bind_param($stmtSiswa, 'i', $selectedKelas);
mysqli_stmt_execute($stmtSiswa);
$resultSiswa = mysqli_stmt_get_result($stmtSiswa);

if (!$resultSiswa) {
    error_log("Error kueri siswa: " . mysqli_error($koneksi));
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Error: " . mysqli_error($koneksi) . "</td></tr></tbody>";
    exit;
}

$siswaList = mysqli_fetch_all($resultSiswa, MYSQLI_ASSOC);
error_log("Jumlah siswa ditemukan: " . mysqli_num_rows($resultSiswa));

if (empty($siswaList)) {
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Tidak ada siswa di kelas ini.</td></tr></tbody>";
    exit;
}

// Query absensi
$absensiData = [];
$queryAbsensi = "SELECT a.siswa_id, a.tanggal, a.hadir, a.sakit, a.izin, a.alpa, a.id_mapel 
                 FROM absensi a 
                 WHERE a.tanggal BETWEEN ? AND ? 
                 AND a.siswa_id IN (SELECT id FROM siswa WHERE kelas_id = ?)";
$params = [$startDate, $endDate, $selectedKelas];
$types = 'ssi';

if ($mapelId > 0) {
    $queryAbsensi .= " AND a.id_mapel = ?";
    $params[] = $mapelId;
    $types .= 'i';
}

$stmtAbsensi = mysqli_prepare($koneksi, $queryAbsensi);
if (!$stmtAbsensi) {
    error_log("Error kueri absensi: " . mysqli_error($koneksi));
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-red-500 font-semibold'>Error: " . mysqli_error($koneksi) . "</td></tr></tbody>";
    exit;
}
mysqli_stmt_bind_param($stmtAbsensi, $types, ...$params);
mysqli_stmt_execute($stmtAbsensi);
$resultAbsensi = mysqli_stmt_get_result($stmtAbsensi);

$absensiCount = mysqli_num_rows($resultAbsensi);
error_log("Jumlah baris absensi: $absensiCount untuk kelas_id: $selectedKelas, mapel_id: " . ($mapelId > 0 ? $mapelId : 'semua') . ", tanggal: $startDate hingga $endDate");

// Jika tidak ada data absensi yang cocok, kembalikan tbody kosong
if ($absensiCount === 0) {
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-gray-500 font-semibold'>Tidak ada data absensi untuk parameter yang diberikan.</td></tr></tbody>";
    mysqli_stmt_close($stmtAbsensi);
    mysqli_stmt_close($stmtSiswa);
    mysqli_close($koneksi);
    exit;
}

while ($rowAbsensi = mysqli_fetch_assoc($resultAbsensi)) {
    $siswaId = $rowAbsensi['siswa_id'];
    $tanggal = $rowAbsensi['tanggal'];
    if (!isset($absensiData[$siswaId])) {
        $absensiData[$siswaId] = [];
    }
    $absensiData[$siswaId][$tanggal] = [
        'hadir' => $rowAbsensi['hadir'],
        'sakit' => $rowAbsensi['sakit'],
        'izin' => $rowAbsensi['izin'],
        'alpa' => $rowAbsensi['alpa'],
        'id_mapel' => $rowAbsensi['id_mapel']
    ];
    error_log("Absensi untuk siswa_id: $siswaId, tanggal: $tanggal, hadir: {$rowAbsensi['hadir']}, sakit: {$rowAbsensi['sakit']}, izin: {$rowAbsensi['izin']}, alpa: {$rowAbsensi['alpa']}, id_mapel: {$rowAbsensi['id_mapel']}");
}
mysqli_stmt_close($stmtAbsensi);

// Cek apakah ada siswa dengan data absensi
$hasAttendance = false;
foreach ($siswaList as $siswa) {
    if (isset($absensiData[$siswa['id']]) && !empty($absensiData[$siswa['id']])) {
        $hasAttendance = true;
        break;
    }
}

if (!$hasAttendance) {
    echo "<tbody><tr><td colspan='7' align='center' class='text-center text-gray-500 font-semibold'>Tidak ada data absensi untuk siswa di kelas ini.</td></tr></tbody>";
    mysqli_stmt_close($stmtSiswa);
    mysqli_close($koneksi);
    exit;
}

echo "<tbody>";
$no = 1;
foreach ($siswaList as $rowSiswa) {
    $siswaId = $rowSiswa['id'];
    // Hanya tampilkan siswa yang memiliki data absensi
    if (!isset($absensiData[$siswaId]) || empty($absensiData[$siswaId])) {
        continue;
    }

    $nama = htmlspecialchars($rowSiswa['nama_siswa']);

    // Hitung total kehadiran, sakit, izin, dan alpa untuk siswa ini dalam rentang tanggal
    $totalHadir = 0;
    $totalSakit = 0;
    $totalIzin = 0;
    $totalAlpa = 0;
    $attendanceData = [];

    foreach ($dates as $date) {
        $attendanceData[$date] = [
            'hadir' => 0,
            'sakit' => 0,
            'izin' => 0,
            'alpa' => 0,
            'id_mapel' => $mapelId > 0 ? $mapelId : 0
        ];
        if (isset($absensiData[$siswaId][$date])) {
            $attendanceData[$date] = $absensiData[$siswaId][$date];
            $totalHadir += $absensiData[$siswaId][$date]['hadir'];
            $totalSakit += $absensiData[$siswaId][$date]['sakit'];
            $totalIzin += $absensiData[$siswaId][$date]['izin'];
            $totalAlpa += $absensiData[$siswaId][$date]['alpa'];
        }
    }

    echo "<tr>";
    echo "<td>$no</td>";
    echo "<td class='text-left'>$nama</td>";
    echo "<td><span class='badge badge-hadir'>" . ($totalHadir == 0 ? '-' : $totalHadir) . "</span></td>";
    echo "<td><span class='badge badge-sakit'>" . ($totalSakit == 0 ? '-' : $totalSakit) . "</span></td>";
    echo "<td><span class='badge badge-izin'>" . ($totalIzin == 0 ? '-' : $totalIzin) . "</span></td>";
    echo "<td><span class='badge badge-alpa'>" . ($totalAlpa == 0 ? '-' : $totalAlpa) . "</span></td>";
    // Encode attendance data as JSON for edit modal
    $attendanceJson = htmlspecialchars(json_encode($attendanceData), ENT_QUOTES, 'UTF-8');
    echo "<td><button class='edit-btn' 
                    data-siswa-id='$siswaId' 
                    data-nama-siswa='$nama' 
                    data-attendance='$attendanceJson'>Edit</button></td>";
    echo "</tr>";
    $no++;
}
echo "</tbody>";

mysqli_stmt_close($stmtSiswa);
mysqli_close($koneksi);
?>