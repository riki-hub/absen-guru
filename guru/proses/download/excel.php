<?php
require __DIR__ . '/../../../vendor/autoload.php'; // Menggunakan __DIR__ untuk path yang lebih portabel
require __DIR__ . '/../../../koneksi.php'; // File koneksi database
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// Validasi parameter input
$startDate = $_GET['startDate'] ?? '';
$endDate = $_GET['endDate'] ?? '';
$kelasId = $_GET['kelas_id'] ?? '';
$mapelId = $_GET['mapel_id'] ?? '';

try {
    // Validasi format tanggal
    if (!strtotime($startDate) || !strtotime($endDate) || !is_numeric($kelasId)) {
        throw new Exception('Parameter startDate, endDate, atau kelas_id tidak valid.');
    }

    // Pastikan startDate <= endDate
    if (strtotime($startDate) > strtotime($endDate)) {
        throw new Exception('Tanggal awal harus sebelum atau sama dengan tanggal akhir.');
    }

    // Ambil nama kelas
    $kelasQuery = "SELECT nama_kelas FROM kelas WHERE id = ?";
    $kelasStmt = $koneksi->prepare($kelasQuery);
    if (!$kelasStmt) {
        throw new Exception('Gagal menyiapkan kueri untuk kelas.');
    }
    $kelasStmt->bind_param("i", $kelasId);
    $kelasStmt->execute();
    $kelasResult = $kelasStmt->get_result();
    $kelasName = $kelasResult->fetch_assoc()['nama_kelas'] ?? 'Kelas_Tidak_Diketahui';

    // Ambil nama mata pelajaran (jika ada)
    $mapelName = 'Semua Mata Pelajaran';
    if (!empty($mapelId)) {
        $mapelQuery = "SELECT mata_pelajaran FROM pelajaran WHERE id = ?";
        $stmtMapel = $koneksi->prepare($mapelQuery);
        if (!$stmtMapel) {
            throw new Exception('Gagal menyiapkan kueri mata pelajaran: ' . $koneksi->error);
        }
        $stmtMapel->bind_param("i", $mapelId);
        $stmtMapel->execute();
        $mapelResult = $stmtMapel->get_result();
        $mapel = $mapelResult->fetch_assoc();
        $mapelName = $mapel ? $mapel['mata_pelajaran'] : 'Tidak Diketahui';
        $stmtMapel->close();
    }

    // Ambil daftar tanggal unik dalam rentang waktu, difilter berdasarkan kelas dan mapel jika ada
    $tanggalQuery = "SELECT DISTINCT a.tanggal FROM absensi a
                     JOIN siswa s ON a.siswa_id = s.id
                     WHERE a.tanggal BETWEEN ? AND ? AND s.kelas_id = ? ";
    $tanggalParamTypes = "ssi";
    $tanggalParams = [&$startDate, &$endDate, &$kelasId];
    if (!empty($mapelId)) {
        $tanggalQuery .= "AND a.id_mapel = ? ";
        $tanggalParamTypes .= "i";
        $tanggalParams[] = &$mapelId;
    }
    $tanggalQuery .= "ORDER BY a.tanggal ASC";
    $tanggalStmt = $koneksi->prepare($tanggalQuery);
    if (!$tanggalStmt) {
        throw new Exception('Gagal menyiapkan kueri untuk tanggal.');
    }
    $tanggalStmt->bind_param($tanggalParamTypes, ...$tanggalParams);
    $tanggalStmt->execute();
    $tanggalResult = $tanggalStmt->get_result();

    $tanggalList = [];
    while ($row = $tanggalResult->fetch_assoc()) {
        $tanggalList[] = $row['tanggal'];
    }

    if (empty($tanggalList)) {
        throw new Exception('Tidak ada data absensi untuk periode dan kelas yang dipilih.');
    }

    // Ambil daftar siswa berdasarkan kelas
    $siswaQuery = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa ASC";
    $siswaStmt = $koneksi->prepare($siswaQuery);
    if (!$siswaStmt) {
        throw new Exception('Gagal menyiapkan kueri untuk siswa.');
    }
    $siswaStmt->bind_param("i", $kelasId);
    $siswaStmt->execute();
    $siswaResult = $siswaStmt->get_result();

    $siswaList = [];
    while ($row = $siswaResult->fetch_assoc()) {
        $siswaList[] = $row;
    }

    if (empty($siswaList)) {
        throw new Exception('Tidak ada siswa ditemukan untuk kelas yang dipilih.');
    }

    // Ambil data absensi dengan agregasi untuk mencegah duplikasi
    $absensiQuery = "SELECT a.siswa_id, a.tanggal,
                    MAX(CASE WHEN a.hadir = 1 THEN 1 ELSE 0 END) as hadir,
                    MAX(CASE WHEN a.sakit = 1 THEN 1 ELSE 0 END) as sakit,
                    MAX(CASE WHEN a.izin = 1 THEN 1 ELSE 0 END) as izin,
                    MAX(CASE WHEN a.alpa = 1 THEN 1 ELSE 0 END) as alpa
                    FROM absensi a
                    JOIN siswa s ON a.siswa_id = s.id
                    WHERE a.tanggal BETWEEN ? AND ? AND s.kelas_id = ? ";
    $absensiParamTypes = "ssi";
    $absensiParams = [&$startDate, &$endDate, &$kelasId];
    if (!empty($mapelId)) {
        $absensiQuery .= "AND a.id_mapel = ? ";
        $absensiParamTypes .= "i";
        $absensiParams[] = &$mapelId;
    }
    $absensiQuery .= "GROUP BY a.siswa_id, a.tanggal";
    $absensiStmt = $koneksi->prepare($absensiQuery);
    if (!$absensiStmt) {
        throw new Exception('Gagal menyiapkan kueri untuk absensi.');
    }
    $absensiStmt->bind_param($absensiParamTypes, ...$absensiParams);
    $absensiStmt->execute();
    $absensiResult = $absensiStmt->get_result();

    $absensiData = [];
    $rekapData = [];
    while ($row = $absensiResult->fetch_assoc()) {
        $siswaId = $row['siswa_id'];
        $tanggal = $row['tanggal'];
        // Tentukan simbol: kosong untuk hadir, S untuk sakit, I untuk izin, A untuk alpa
        $symbol = '';
        if ($row['hadir']) {
            $symbol = '';
        } elseif ($row['sakit']) {
            $symbol = 'S';
        } elseif ($row['izin']) {
            $symbol = 'I';
        } elseif ($row['alpa']) {
            $symbol = 'A';
        }
        $absensiData[$siswaId][$tanggal] = $symbol;

        // Inisialisasi rekap jika belum ada
        if (!isset($rekapData[$siswaId])) {
            $rekapData[$siswaId] = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
        }
        // Hitung rekap berdasarkan prioritas
        if ($row['hadir']) {
            $rekapData[$siswaId]['H']++;
        } elseif ($row['sakit']) {
            $rekapData[$siswaId]['S']++;
        } elseif ($row['izin']) {
            $rekapData[$siswaId]['I']++;
        } elseif ($row['alpa']) {
            $rekapData[$siswaId]['A']++;
        }
    }

    // Buat objek Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set properti dokumen
    $spreadsheet->getProperties()
        ->setCreator('Sistem Absensi')
        ->setTitle('Laporan Absensi Bulanan')
        ->setSubject('Rekap Absensi Siswa')
        ->setDescription('Laporan absensi bulanan siswa untuk kelas dan periode tertentu');

    // Judul dan periode
    $periodeText = date('d F Y', strtotime($startDate)) . ' s.d. ' . date('d F Y', strtotime($endDate));
    $sheet->setCellValue('A1', 'Laporan Absensi Siswa - ' . $kelasName);
    $sheet->setCellValue('A2', "Periode: $periodeText");
    $sheet->setCellValue('A3', "Mata Pelajaran: $mapelName");

    // Gabungkan sel untuk judul
    $lastColumn = chr(65 + count($tanggalList) + 4); // Kolom terakhir (C + jumlah tanggal + 4 kolom rekap)
    $sheet->mergeCells("A1:{$lastColumn}1");
    $sheet->mergeCells("A2:{$lastColumn}2");
    $sheet->mergeCells("A3:{$lastColumn}3");
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $sheet->getStyle('A2')->getFont()->setSize(11);
    $sheet->getStyle('A3')->getFont()->setSize(11);
    $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Header tabel
    $sheet->setCellValue('A4', 'No');
    $sheet->setCellValue('B4', 'Nama Siswa');
    $col = 'C';
    foreach ($tanggalList as $tgl) {
        $sheet->setCellValue($col . '4', date('d', strtotime($tgl))); // Hanya tampilkan hari (tanpa bulan)
        $col++;
    }
    $sheet->setCellValue($col . '4', 'H');
    $col++;
    $sheet->setCellValue($col . '4', 'I');
    $col++;
    $sheet->setCellValue($col . '4', 'S');
    $col++;
    $sheet->setCellValue($col . '4', 'A');

    // Terapkan gaya pada header
    $headerStyle = [
        'font' => ['bold' => true, 'size' => 9],
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => [
            'allBorders' => ['borderStyle' => Border::BORDER_THIN],
        ],
    ];
    $sheet->getStyle("A4:{$col}4")->applyFromArray($headerStyle);

    // Isi data absensi
    $row = 5;
    $no = 1;
    foreach ($siswaList as $siswa) {
        $siswaId = $siswa['id'];
        // Hanya isi data jika siswa memiliki absensi
        if (!isset($absensiData[$siswaId])) {
            continue; // Lewati siswa tanpa data absensi
        }

        $sheet->setCellValue('A' . $row, $no++);
        $sheet->setCellValue('B' . $row, $siswa['nama_siswa']);

        $col = 'C';
        foreach ($tanggalList as $tanggal) {
            $symbol = $absensiData[$siswaId][$tanggal] ?? '';
            $sheet->setCellValue($col . $row, $symbol);
            $col++;
        }

        // Isi rekap
        $h = $rekapData[$siswaId]['H'] ?? 0;
        $i = $rekapData[$siswaId]['I'] ?? 0;
        $s = $rekapData[$siswaId]['S'] ?? 0;
        $a = $rekapData[$siswaId]['A'] ?? 0;

        $sheet->setCellValue($col . $row, $h);
        $col++;
        $sheet->setCellValue($col . $row, $i);
        $col++;
        $sheet->setCellValue($col . $row, $s);
        $col++;
        $sheet->setCellValue($col . $row, $a);

        // Terapkan gaya pada baris data
        $sheet->getStyle("A{$row}:{$col}{$row}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $row++;
    }

    // Atur lebar kolom
    $sheet->getColumnDimension('A')->setWidth(5); // No
    // Atur lebar kolom B berdasarkan nama siswa terpanjang
    $maxNameLength = max(array_map('strlen', array_column($siswaList, 'nama_siswa')));
    $sheet->getColumnDimension('B')->setWidth(max(20, $maxNameLength * 1.2));
    for ($i = 0; $i < count($tanggalList); $i++) {
        $sheet->getColumnDimension(chr(67 + $i))->setWidth(4); // Kolom tanggal (C dst)
    }
    $sheet->getColumnDimension(chr(67 + count($tanggalList)))->setWidth(5); // H
    $sheet->getColumnDimension(chr(68 + count($tanggalList)))->setWidth(5); // I
    $sheet->getColumnDimension(chr(69 + count($tanggalList)))->setWidth(5); // S
    $sheet->getColumnDimension(chr(70 + count($tanggalList)))->setWidth(5); // A

    // Atur tinggi baris
    $sheet->getRowDimension(4)->setRowHeight(20);
    for ($i = 5; $i < $row; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(20);
    }

    // Sanitasi nama kelas untuk nama file
    $safeKelasName = preg_replace('/[^A-Za-z0-9_-]/', '_', $kelasName);
    $bulan = date('F_Y', strtotime($startDate)); // Nama bulan dan tahun untuk nama file
    $filename = "laporan_absensi_{$safeKelasName}_{$bulan}.xlsx";

    // Keluarkan file Excel
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$filename\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo 'Error: ' . $e->getMessage();
    exit;
}
?>