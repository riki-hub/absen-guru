<?php
include '../koneksi.php';
session_start();
if (!isset($_SESSION['nama'])) {
    header("Location: ../");
    exit();
}

// Mengambil data pengguna dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM akun_guru WHERE id='$user_id'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $croppedImageData = $_POST['gambar'];
    $id = $_POST['id'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Simpan gambar yang di-crop
    if (!empty($_FILES['gambar']['name'])) {
        $folderPath = "../upload/";
        $fileName = uniqid() . basename($_FILES['gambar']['name']);
        $targetFile = $folderPath . $fileName;

        // Pindahkan file yang diunggah ke direktori tujuan
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFile)) {
            // Jika unggahan berhasil, hapus gambar lama jika ada
            if (!empty($user['gambar'])) {
                unlink($folderPath . $user['gambar']);
            }
        } else {
            $fileName = $user['gambar']; // Jika gagal, gunakan gambar yang lama
        }
    } else {
        $fileName = $user['gambar']; // Jika tidak ada gambar yang diunggah, gunakan gambar yang lama
    }

    // Query untuk menyimpan data
    $gel = "UPDATE akun_guru SET nama='$nama', username='$username', password='$hashedPassword', gambar='$fileName' WHERE id='$id'";

    if (mysqli_query($koneksi, $gel)) {
        // Update session data
        $_SESSION['nama'] = $nama;
        $_SESSION['gambar'] = $fileName;
        header("Location:profile.php");
        exit();
    } else {
        echo "Error: " . $gel . "<br>" . mysqli_error($koneksi);
    }
}
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM akun_guru WHERE id='$user_id'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile | Guru</title>
    <link rel="shortcut icon" href="../images/favicon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-card {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .profile-card img {
            border-radius: 50%;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border: 2px solid #6a11cb;
            cursor: pointer;
        }

        .profile-card h2 {
            margin: 10px 0;
            font-size: 24px;
            color: #333;
        }

        .profile-card form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .profile-card form input,
        .profile-card form select {
            /* Menambahkan select */
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .profile-card form button {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .profile-card form button:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
        }


        .footer {
            display: flex;
            justify-content: space-around;
            position: relative;
            bottom: 0;
            width: 100%;
            background: #fff;
            padding: 10px 0;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
        }

        .footer div {
            text-align: center;
            flex: 1;
        }

        .footer div i {
            font-size: 24px;
            color: #6a11cb;
        }

        .footer div p {
            margin: 0;
            font-size: 12px;
        }

        .footer div.active i {
            color: #2575fc;
        }

        .floating-btn {
            position: absolute;
            top: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            z-index: 1;
        }
        .logout-button:hover {
      background-color: #fff;
      color : red ;
    }
    .home p {
      color: black ;
      text-decoration: none;

    }
    .home p:hover{
     color : white;
    }
    .profile p {
      color: black ;
      text-decoration: none;

    }
    .profile a {
      color: black ;
      text-decoration: none;

    }
    .home a {
      color: black ;
      text-decoration: none;

    }
    .profile p:hover {
     color : white;
    }
    </style>
</head>

<body>
    <div class="content">
        <div class="profile-card">
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <img src="../upload/<?php echo $_SESSION['gambar']; ?>" id="profile-pic" alt="Profile Picture" onclick="document.getElementById('gambar').click();" />
                <h2><?php echo $_SESSION['nama']; ?></h2>
                <input type="hidden" name="id" value="<?php echo $_SESSION['user_id']; ?>">
                <input type="file" name="gambar" id="gambar" style="display: none;" onchange="previewImage(event);" />
                <input type="text" name="nama" value="<?php echo $user['nama']; ?>" readonly />
                <input type="text" name="jabatan" value="<?php echo $user['jabatan']; ?>" required />
                <select class="form-" name="gender" required autocomplete="off" id="">
                    <option value="">Pilih...</option>
                    <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>Laki-Laki</option>
                    <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>Perempuan</option>
                </select>
                <input type="text" name="username" value="<?php echo $user['username']; ?>" required />

                <input type="password" placeholder="Password..." name="password" value="" />

                <input type="hidden" name="cropped_image_dataedit" id="cropped_image_dataedit" />
                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>

    <div class="footer">
        <div class="home">
            <a href="index.php">
                <i class="fas fa-home"></i>
                <p>Home</p>
            </a>
        </div>
        <button class="floating-btn">
            <i class="fas fa-camera"></i>
        </button>
        <div class="profile">
            <a href="profile.php">
                <i class="fas fa-user"></i>
                <p>Profile</p>
            </a>
        </div>
    </div>

    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('profile-pic');
                output.src = reader.result;
                document.getElementById('cropped_image_dataedit').value = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>

</html>