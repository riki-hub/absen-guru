<?php
require '../../../vendor/autoload.php'; // Composer autoload PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\IOFactory;

include '../../../koneksi.php';

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$importSuccess = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel']['name']) && $_FILES['excel']['error'] === UPLOAD_ERR_OK) {
    // Validasi ekstensi file
    $allowed_extensions = ['xlsx', 'xls'];
    $file_extension = strtolower(pathinfo($_FILES['excel']['name'], PATHINFO_EXTENSION));
    if (!in_array($file_extension, $allowed_extensions)) {
        $errors[] = "File harus berupa Excel (.xlsx atau .xls).";
    } else {
        $file = $_FILES['excel']['tmp_name'];

        try {
            // Load file Excel
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            // Loop mulai dari baris ke-2 untuk mengabaikan header
            for ($i = 2; $i <= count($sheet); $i++) {
                $nama = trim($koneksi->real_escape_string($sheet[$i]['B'] ?? ''));
                $kelas_nama = trim($koneksi->real_escape_string($sheet[$i]['C'] ?? ''));

                if (!empty($nama) && !empty($kelas_nama)) {
                    $kelas_nama_clean = preg_replace('/\s+/', ' ', strtoupper($kelas_nama)); // Normalisasi

                    $queryKelas = $koneksi->prepare("SELECT id FROM kelas WHERE nama_kelas = ?");
                    $queryKelas->bind_param("s", $kelas_nama_clean);
                    $queryKelas->execute();
                    $result = $queryKelas->get_result();

                    if ($result->num_rows > 0) {
                        $kelas = $result->fetch_assoc();
                        $kelas_id = $kelas['id'];
                    } else {
                        $queryInsertKelas = $koneksi->prepare("INSERT INTO kelas (nama_kelas) VALUES (?)");
                        $queryInsertKelas->bind_param("s", $kelas_nama_clean);
                        if ($queryInsertKelas->execute()) {
                            $kelas_id = $koneksi->insert_id;
                        } else {
                            $errors[] = "Gagal menambahkan kelas '$kelas_nama_clean'.";
                            $queryInsertKelas->close();
                            continue;
                        }
                        $queryInsertKelas->close();
                    }

                    $queryInsert = $koneksi->prepare("INSERT INTO siswa (nama_siswa, kelas_id) VALUES (?, ?)");
                    $queryInsert->bind_param("si", $nama, $kelas_id);
                    if ($queryInsert->execute()) {
                        $importSuccess = true;
                    } else {
                        $errors[] = "Gagal menambah $nama.";
                    }
                    $queryInsert->close();
                    $queryKelas->close();
                } else {
                    $errors[] = "Baris $i dilewati karena data nama atau kelas kosong.";
                }
            }
        } catch (Exception $e) {
            $errors[] = "Error membaca file Excel: " . $e->getMessage();
        }
    }
} elseif (isset($_FILES['excel']['error']) && $_FILES['excel']['error'] !== UPLOAD_ERR_OK) {
    switch ($_FILES['excel']['error']) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $errors[] = "File terlalu besar.";
            break;
        case UPLOAD_ERR_NO_FILE:
            $errors[] = "Tidak ada file yang diunggah.";
            break;
        default:
            $errors[] = "Error upload file: " . $_FILES['excel']['error'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Data Siswa</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
   

    <!-- Bootstrap JS dan SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($importSuccess && empty($errors)): ?>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data siswa berhasil diimpor.',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                window.location.href = '../../siswa.php';
            });
        <?php elseif (!empty($errors)): ?>
            Swal.fire({
                title: 'Gagal!',
                html: 'Gagal mengimpor data. <br>' + '<?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../../siswa.php';
                }
            });
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Tutup koneksi
$koneksi->close();
?>