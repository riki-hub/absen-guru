<?php
include '../../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $croppedImageData = $_POST['cropped_image_dataedit'];
    $id = $_POST['id'];
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

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


    // Query untuk menyimpan data
    $gel = "UPDATE admin SET nama='$nama',username='$username',password='$hashedPassword',gambar='$fileName' WHERE id='$id'";


    if (mysqli_query($koneksi, $gel)) {
        header("location:../../pengguna.php");
    } else {
        echo "Error: " . $gel . "<br>" . mysqli_error($koneksi);
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $koneksi->close();
}
