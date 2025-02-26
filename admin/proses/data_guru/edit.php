<?php
include '../../../koneksi.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $gender = $_POST['gender'];
    $jabatan = $_POST['jabatan'];
    $croppedImageData = $_POST['cropped_image_dataedit'];

    $id = $_POST['id'];

    // Hash password hanya jika ada perubahan password
    if (!empty($password)) {
        $hashedPassword = $password;
    }

    // Simpan gambar yang di-crop atau gunakan gambar yang sudah ada
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
        // Jika tidak ada gambar yang dipilih, ambil gambar yang sudah ada atau gunakan avatar default
        $sql = "SELECT gambar FROM akun_guru WHERE id='$id'";
        $result = mysqli_query($koneksi, $sql);
        $row = mysqli_fetch_assoc($result);
        $fileName = $row['gambar'];

        if (empty($fileName)) {
            // Gunakan avatar default jika tidak ada gambar yang disimpan sebelumnya
            if ($gender == 'male') {
                $fileName = 'avatar1.jpg';
            } else {
                $fileName = 'avatar2.jpg';
            }
        }
    }

    // Query untuk mengupdate data
    if (!empty($password)) {
        $sql = "UPDATE akun_guru SET nama='$nama', gender='$gender', jabatan='$jabatan', username='$username', password='$hashedPassword', gambar='$fileName' WHERE id='$id'";
    } else {
        $sql = "UPDATE akun_guru SET nama='$nama', gender='$gender', jabatan='$jabatan', username='$username', gambar='$fileName' WHERE id='$id'";
    }

    if (mysqli_query($koneksi, $sql)) {
        header("location:../../guru.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($koneksi);
    }

    // Tutup koneksi
    $koneksi->close();
}
