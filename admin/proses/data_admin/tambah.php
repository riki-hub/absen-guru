<?php
include '../../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $croppedImageData = $_POST['cropped_image_data'];

    // Hash password
    $hashedPassword = $password;

    // Simpan gambar yang di-crop
    if (!empty($croppedImageData)) {
        $folderPath = "../../../upload/";
        $image_parts = explode(";base64,", $croppedImageData);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = uniqid() . '.png';
        $file = $folderPath . $fileName;

        file_put_contents($file, $image_base64);
    }

    // Koneksi ke database


    // Query untuk menyimpan data
    $sql = "INSERT INTO admin (nama, username, password, gambar) VALUES (?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssss", $nama, $username, $hashedPassword, $fileName);

    if ($stmt->execute()) {
        echo "Data berhasil disimpan.";
        header("Location:../../pengguna.php");
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $koneksi->close();
}
