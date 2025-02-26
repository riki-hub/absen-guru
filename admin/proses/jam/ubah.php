<?php
include '../../../koneksi.php';
$id = $_POST['id']; // Mengambil id dari form
    $nama = $_POST['nama']; // Nama guru (readonly)
    $photo = $_POST['photo']; // Foto (apabila ada)

    // Ambil jam kehadiran dari form
    $jamValues = [];
    for ($i = 1; $i <= 10; $i++) {
        if (isset($_POST["jam$i"])) {
            $jamValues["jam$i"] = $_POST["jam$i"];
        }
    }

    // Jika foto diupload, proses upload file dan ambil path foto
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        // Tentukan path folder tempat foto disimpan
        $targetDir = "uploads/"; // Pastikan folder ini ada dan dapat diakses
        $targetFile = $targetDir . basename($_FILES['photo']['name']);
        
        // Cek apakah file adalah gambar
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'png', 'jpeg', 'gif'];
        
        if (in_array($imageFileType, $allowedTypes)) {
            // Upload file
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photo = $targetFile; // Update dengan path foto yang sudah diupload
            } else {
                echo "<script>alert('Gagal mengupload foto.');window.location='index.php';</script>";
                exit;
            }
        } else {
            echo "<script>alert('File bukan gambar yang valid.');window.location='index.php';</script>";
            exit;
        }
    }

    // Update query untuk menyimpan data yang diubah
    $query = "UPDATE absen_msk SET 
                jam1 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam1'] ?? '') . "',
                jam2 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam2'] ?? '') . "',
                jam3 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam3'] ?? '') . "',
                jam4 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam4'] ?? '') . "',
                jam5 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam5'] ?? '') . "',
                jam6 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam6'] ?? '') . "',
                jam7 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam7'] ?? '') . "',
                jam8 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam8'] ?? '') . "',
                jam9 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam9'] ?? '') . "',
                jam10 = '" . mysqli_real_escape_string($koneksi, $jamValues['jam10'] ?? '') . "',
                foto_absen = '" . mysqli_real_escape_string($koneksi, $photo) . "' 
              WHERE id = '$id'";

    // Eksekusi query
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data berhasil diperbarui!');window.history.back()</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan saat memperbarui data.');window.location='index.php';</script>";
    }

?>
