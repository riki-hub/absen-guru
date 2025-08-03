<?php
include '../../../koneksi.php';

$id = $_POST['id'];
$nama = $_POST['nama'];


// Ambil data jam kehadiran
$jamValues = [];
for ($i = 1; $i <= 10; $i++) {
    $jamKey = "jam$i";
    $jamValues[$jamKey] = mysqli_real_escape_string($koneksi, $_POST[$jamKey] ?? '');
}

// Cari jam terakhir terisi
$lastFilledJam = '';
for ($i = 10; $i >= 1; $i--) {
    if (!empty($_POST["jam$i"])) {
        $lastFilledJam = $_POST["jam$i"];
        break;
    }
}
$sentence = !empty($lastFilledJam) ? "Terakhir Absen Di Kelas, $lastFilledJam" : "Belum diisi";

// Handle upload foto
$fotoBaru = null;
$fotoLama = null;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "../../../absen/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true); // bikin folder kalau belum ada
    }

    $fileTmpPath = $_FILES['photo']['tmp_name'];
    $fileName = basename($_FILES['photo']['name']);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowedTypes)) {
        $newFileName = uniqid('foto_', true) . '.' . $fileExt;
        $targetFilePath = $targetDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $targetFilePath)) {
            $fotoBaru = $newFileName;

            // Ambil nama file lama buat dihapus
            $getOld = mysqli_query($koneksi, "SELECT foto_absen FROM absen_msk WHERE id = '$id'");
            $old = mysqli_fetch_assoc($getOld);
            if ($old && !empty($old['foto_absen'])) {
                $fotoLama = $old['foto_absen'];
                $oldPath = $targetDir . $fotoLama;
                if (file_exists($oldPath)) {
                    unlink($oldPath); // hapus foto lama
                }
            }
        } else {
            echo "<script>alert('Gagal menyimpan file foto.');window.location='index.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Format file tidak valid. Hanya JPG, JPEG, PNG, GIF.');window.location='index.php';</script>";
        exit;
    }
}

// Bangun query update
$query = "UPDATE absen_msk SET ";
foreach ($jamValues as $jamKey => $jamVal) {
    $query .= "$jamKey = '$jamVal', ";
}

if ($fotoBaru !== null) {
    $query .= "foto_absen = '" . mysqli_real_escape_string($koneksi, $fotoBaru) . "', ";
}

$query .= "activity = '" . mysqli_real_escape_string($koneksi, $sentence) . "' ";
$query .= "WHERE id = '$id'";

// Eksekusi query
if (mysqli_query($koneksi, $query)) {
    echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>edit jam guru</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Data berhasil di edit',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../edit-jam.php?id=$id';
                });
            </script>
        </body>
        </html>";
} else {
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>edit jam guru</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
       <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Data berhasil di edit',
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '../../edit-jam.php?id=$id';
                });
            </script>
    </body>
    </html>";
}


?>
