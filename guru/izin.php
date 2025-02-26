<?php
// memanggil file koneksi.php untuk membuat koneksi
include '../koneksi.php';

// Mulai session
session_start();

// Cek apakah user terautentikasi dan memiliki role 'guru'
if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'guru') {
    header("Location: ../");
    exit();
}

// Mengecek apakah di URL ada nilai GET id
if (isset($_GET['id'])) {
    // Ambil nilai id dari URL dan simpan dalam variabel $id
    $id = $_GET['id'];

    // Query untuk menampilkan data dari database yang mempunyai id=$id
    $query = "SELECT * FROM akun_guru WHERE id='$id'";
    $result = mysqli_query($koneksi, $query);

    // Jika data gagal diambil maka akan tampil error berikut
    if (!$result) {
        die("Query Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
    }

    // Mengambil data dari database
    $data = mysqli_fetch_assoc($result);

    // Apabila data tidak ada pada database maka akan dijalankan perintah ini
    if (!$data) {
        die("Data tidak ditemukan!");
    }
} else {
    die("ID tidak ditemukan di URL!");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Izin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- plugins:css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>

    <div class="row">
        <!-- Form Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Izin</h4>
                </div>
                <div class="card-body">
                    <form action="proses/izin/izin.php" method="post" enctype="multipart/form-data">
                        <div class="col-md-6 mb-3">
                            <label for="">Nama Guru</label>
                            <input class="form-control" readonly type="text" name="nama_guru" value="<?php echo htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8'); ?>" id="guru">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Tanggal</label>
                            <input type="text" id="tanggal_hari_ini" readonly class="form-control" name="tanggal">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Keterangan</label>
                            <input type="text" id="" class="form-control" name="keterangan">
                        </div>
                        <div class="col-md-6 mb-3">
                            <a class="btn btn-primary" onclick="startCamera()">Mulai Kamera</a>
                            <video id="video" width="320" height="240" autoplay style="display:none;"></video>
                            <canvas id="canvas" width="280" height="240" style="display:none;"></canvas>
                            <a class="btn btn-primary" id="snap" style="display:none;" onclick="takePicture()">Ambil Foto</a>
                            <input type="hidden" name="photo" id="photo">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Kirim</button>

                        <button onclick="history.back()" type="button" class="btn btn-secondary">Kembali</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="col-md-8">
    <div class="row">
    <?php
    include "../koneksi.php";
    $id = $_SESSION['nama'];
    $tanggal = date("Y-m-d");
    $query = "SELECT * from izin where nama= '$id' and tanggal= '$tanggal'";
    $result = mysqli_query($koneksi, $query);

    // If data retrieval fails, display an error
    if (!$result) {
        die("Query Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
    }

    // Fetch data from the database
    $p = mysqli_fetch_assoc($result);
    ?>
    <div class="card">
        <div class="card-header">
            <h4>Status Izin</h4>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <!-- Image/Photo Section -->
                <div class="col-md-3 mb-3">
                    <img src="../izin/<?= $p['gambar'];?>" alt="Foto Guru" class="img-fluid" style="width: 250px; height: 250px;">
                </div>

                <!-- Information Section -->
                <div class="col-md-9 mb-3">
                    <div class="col-md-6 mb-3">
                        <label for="">Nama Guru</label>
                        <input class="form-control" readonly type="text" name="nama_guru" value="<?php echo htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8'); ?>" id="guru">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Keterangan</label>
                        <input class="form-control" readonly type="text" name="nama_guru" value="<?php echo htmlspecialchars($p['keterangan'], ENT_QUOTES, 'UTF-8'); ?>" id="guru">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="">Status</label>
                        <br>
                        <?php
                        if ($p['status'] == 'Di izinkan') {
                            echo '<div class="badge badge-success" style="width: 120px;"> <b>Di izinkan</b> </div>';
                        } else if ($p['status'] == 'belum di cek') {
                            echo '<div class="badge badge-warning" style="width: 120px;"> <b>belum di acc</b> </div>';
                        } else if ($p['status'] == 'Tidak di izinkan') {
                            echo '<div class="badge badge-danger" style="width: 120px;"> <b>Tidak di izinkan</b> </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
<!-- plugins:js -->
<script src="../vendors/base/vendor.bundle.base.js"></script>
<script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const snapButton = document.getElementById('snap');
    const photoInput = document.getElementById('photo');

    function startCamera() {
        navigator.mediaDevices.getUserMedia({
                video: true
            })
            .then((stream) => {
                video.srcObject = stream;
                video.style.display = 'block';
                snapButton.style.display = 'inline';
            })
            .catch((err) => {
                console.error("Error accessing camera: " + err);
            });
    }

    function takePicture() {
        context.drawImage(video, 0, 0, 320, 240);
        const dataURL = canvas.toDataURL('image/png');
        photoInput.value = dataURL; // Menyimpan data gambar sebagai base64
        video.style.display = 'none';
        canvas.style.display = 'block';
        stopCamera();
    }

    function stopCamera() {
        let stream = video.srcObject;
        let tracks = stream.getTracks();
        tracks.forEach(track => track.stop());
        video.srcObject = null;
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                document.getElementById("latitude").value = position.coords.latitude;
                document.getElementById("longitude").value = position.coords.longitude;
            });
        } else {
            alert("Geolocation tidak didukung oleh browser ini.");
        }
    }

    // Get location automatically when page loads
    window.onload = getLocation;
</script>
<script>
    // Mengatur nilai input tanggal dengan tanggal hari ini
    document.addEventListener('DOMContentLoaded', function() {
        var today = new Date().toISOString().split('T')[0]; // Mendapatkan tanggal hari ini dalam format YYYY-MM-DD
        document.getElementById('tanggal_hari_ini').value = today; // Mengisi input dengan tanggal hari ini
    });
</script>



</html>