<?php
require '../../../vendor/autoload.php';
include('../../../koneksi.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Dapatkan tanggal dari form
$startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-01');
$endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-t');

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header utama
$sheet->setCellValue('A1', 'DATA HADIR GURU SEBULAN');
$sheet->mergeCells('A1:' . 'Z1'); // Sesuaikan lebar kolom jika perlu
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);


// Header kolom (No, Nama Guru)
$sheet->setCellValue('A2', 'No');
$sheet->setCellValue('B2', 'NAMA GURU');

// Tambahkan warna biru pada header "No" dan "Nama Guru"
$sheet->getStyle('A2')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('1E90FF');  // Warna biru

$sheet->getStyle('B2')->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('1E90FF');  // Warna biru

$sheet->getStyle('A2:B2')->getFont()->getColor()->setARGB('FFFFFF');  // Teks putih

// Query tanggal dan data guru
$queryDates = "SELECT DISTINCT tanggal FROM absen_msk WHERE tanggal BETWEEN '$startDate' AND '$endDate' ORDER BY tanggal ASC";
$resultDates = mysqli_query($koneksi, $queryDates);
if (!$resultDates) {
    die("Query error: " . mysqli_error($koneksi));
}

$dates = [];
$dateColumns = [];  // Menyimpan kolom tanggal
$colIndex = 3;  // Kolom untuk tanggal dimulai dari kolom C (indeks 3)

while ($rowDate = mysqli_fetch_assoc($resultDates)) {
    $dates[] = $rowDate['tanggal'];
    // Setiap tanggal dimasukkan ke dalam kolom, dan merge header
    $sheet->setCellValueByColumnAndRow($colIndex, 2, $rowDate['tanggal']);  // Menambahkan tanggal pada header
    $sheet->getColumnDimensionByColumn($colIndex)->setWidth(15);  // Sesuaikan lebar kolom
    $sheet->getStyleByColumnAndRow($colIndex, 2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Tambahkan warna biru pada header tanggal
    $sheet->getStyleByColumnAndRow($colIndex, 2)->getFill()
        ->setFillType(Fill::FILL_SOLID)
        ->getStartColor()->setARGB('1E90FF');  // Warna biru

    $sheet->getStyleByColumnAndRow($colIndex, 2)->getFont()->getColor()->setARGB('FFFFFF');  // Teks putih

    $colIndex++;
}

// Tambahkan header untuk Total Jam
$sheet->setCellValueByColumnAndRow($colIndex, 2, 'Total Jam');

// Tambahkan warna biru pada header "Total Jam"
$sheet->getStyleByColumnAndRow($colIndex, 2)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setARGB('1E90FF');  // Warna biru

$sheet->getStyleByColumnAndRow($colIndex, 2)->getFont()->getColor()->setARGB('FFFFFF');  // Teks putih

// Query guru untuk mengisi data
$queryGuru = "SELECT DISTINCT nama FROM absen_msk ORDER BY nama ASC";
$resultGuru = mysqli_query($koneksi, $queryGuru);
if (!$resultGuru) {
    die("Query error: " . mysqli_error($koneksi));
}

// Generate data ke Excel
$rowIndex = 3;  // Mulai dari baris ke-3 untuk data
$no = 1;
while ($rowGuru = mysqli_fetch_assoc($resultGuru)) {
    $namaGuru = strtoupper($rowGuru['nama']);  // Nama guru diubah menjadi huruf kapital

    $sheet->setCellValue('A' . $rowIndex, $no);
    $sheet->setCellValue('B' . $rowIndex, $namaGuru);

    $totalJamMengajar = 0;  // Total jam untuk setiap guru

    $colIndex = 3;  // Mulai dari kolom C
    foreach ($dates as $date) {
        // Hitung total jam untuk setiap tanggal
        $queryJam = "SELECT jam1, jam2, jam3, jam4, jam5, jam6, jam7, jam8, jam9, jam10 
                     FROM absen_msk 
                     WHERE nama = '$namaGuru' AND tanggal = '$date'";
        $resultJam = mysqli_query($koneksi, $queryJam);
        $jamCount = 0;

        if ($resultJam->num_rows > 0) {
            $rowJam = mysqli_fetch_assoc($resultJam);
            foreach ($rowJam as $jam) {
                if (!empty($jam)) {
                    $jamCount++;
                }
            }
        }

        // Isi data jam di kolom tanggal
        $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $jamCount);
        $totalJamMengajar += $jamCount; // Tambah total jam

        $colIndex++;
    }

    // Total jam
    $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, $totalJamMengajar);

    $rowIndex++;
    $no++;
}

// Terapkan gaya border pada seluruh tabel
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => '000000'],
        ],
    ],
];
$sheet->getStyle('A2:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styleArray);

// Export ke file Excel dengan nama dinamis berdasarkan tanggal
$fileName = "data-hadir-" . $startDate . "-" . $endDate . ".xlsx";

// Export ke file Excel
$writer = new Xlsx($spreadsheet);
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit();
