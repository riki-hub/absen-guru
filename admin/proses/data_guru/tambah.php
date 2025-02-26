<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah</title>
</head>

<body>

</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</html>
<?php
include '../../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $jabatan = $_POST['jabatan'];
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
    } else {
        // Jika tidak ada gambar yang dipilih, gunakan avatar default berdasarkan gender
        if ($gender == 'male') {
            $fileName = 'avatar1.jpg';
        } else {
            $fileName = 'avatar2.jpg';
        }
    }

    // Query untuk menyimpan data
    $sql = "INSERT INTO akun_guru (nama, username, gender, jabatan, password, gambar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssssss", $nama, $username, $gender, $jabatan, $hashedPassword, $fileName);

    if ($stmt->execute()) {
        echo "<script> Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    showConfirmButton: false,
    timer: 1500
  }).then(() => {
    window.location.href = '../../guru.php';
  });
</script>";
    } else {
        echo "Terjadi kesalahan: " . $stmt->error;
    }

    // Tutup statement dan koneksi
    $stmt->close();
    $koneksi->close();
}
