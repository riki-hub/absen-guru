<?php
// memanggil file koneksi.php untuk membuat koneksi

include '../koneksi.php';

// mengecek apakah di url ada nilai GET id
if (isset($_GET['id'])) {
    // Ambil nilai id dari URL dan simpan dalam variabel $id
    $id = ($_GET["id"]);

    // Query untuk menampilkan data dari database yang mempunyai id=$id dan tanggal sesuai dengan tanggal sekarang
    $query = "SELECT absen_msk.*, akun_guru.* 
              FROM akun_guru 
              INNER JOIN absen_msk ON akun_guru.nama = absen_msk.nama 
              WHERE akun_guru.id='$id' AND absen_msk.tanggal = CURDATE()";
    $result = mysqli_query($koneksi, $query);

    // Jika data gagal diambil maka akan tampil error berikut
    if (!$result) {
        die("Query Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
    }

    // Mengambil data dari database
    $data = mysqli_fetch_assoc($result);

    // Apabila data tidak ada pada database maka akan dijalankan perintah ini
}



session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'guru') {
    header("Location: ../");

    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>detail absen </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- plugins:css -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <!-- endinject -->

    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>
<script>
    window.setTimeout(function() {
        window.location.reload();
    }, 10000);
</script>

<!-- partial:partials/_navbar.html -->

<!-- partial -->

<!-- partial:partials/_sidebar.html -->

<!-- partial -->
<div class="">
    <div class="content-wrapper">
        <div class="">
            <div class="row">
                <!-- Form Section -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>Detail Absen</h4>
                        </div>
                        <div class="card-body">
                            <form>
                                <div class="mb-3 row">
                                    <label for="name" class="col-sm-3 col-form-label">Nama Guru</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control" id="name" value="<?php echo $data['nama']; ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jabatan" class="col-sm-3 col-form-label">Jabatan</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control" id="jabatan" value="<?php echo $data['jabatan']; ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="gender" class="col-sm-3 col-form-label">Gender</label>
                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control" id="gender" value="<?php echo $data['gender']; ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-3 col-form-label">Koordinat</label>

                                    <div class="col-sm-9">
                                        <input type="text" readonly class="form-control" id="alamat" value="<?php echo $data['kordinat']; ?>">
                                        <br>
                                        <a href="https://www.google.com/maps?q=<?php echo $data['kordinat']; ?>" target="_blank">Lihat di Google Maps</a>
                                    </div>
                                    <div class="col-sm-9">
                                        <label for="">Terakhir Absen</label>
                                        <input type="text" class="form-control" name="" id="" value="<?php echo $data['activity']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 checklist-container row">
                                    <label class="form-label">Checklist Jam Kehadiran</label>
                                    <style>
                                        .checklist-container {
                                            margin-left: 20px;
                                            /* Atur jarak ke kanan sesuai kebutuhan */
                                        }
                                    </style>
                                    <?php
                                    include '../koneksi.php';

                                    // Mengecek apakah di URL ada nilai GET id
                                    if (isset($_GET['id'])) {
                                        // Ambil nilai id dari URL dan simpan dalam variabel $id
                                        $id = ($_GET["id"]);

                                        // Menampilkan data dari database yang mempunyai id=$id dari tabel absen_msk
                                        $query = "SELECT absen_msk.*, akun_guru.* 
              FROM akun_guru 
              INNER JOIN absen_msk ON akun_guru.nama = absen_msk.nama 
              WHERE akun_guru.id='$id' AND absen_msk.tanggal = CURDATE()";
                                        $result = mysqli_query($koneksi, $query);

                                        // Jika data gagal diambil maka akan tampil error berikut
                                        if (!$result) {
                                            die("Query Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
                                        }

                                        // Mengambil data dari database
                                        $p = mysqli_fetch_assoc($result);

                                        // Apabila p$p tidak ada pada database maka akan dijalankan perintah ini
                                        if (!$p) {
                                            echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Anda belum melakukan absen',
            showConfirmButton: false,
            timer: 1500
        }).then(() => {
            window.location.href = 'index.php';
        });
    </script>";
                                        }
                                    } else {
                                        // Apabila tidak ada p$p GET id pada akan di redirect ke index.php
                                        echo "<script>alert('Masukkan p$p id.');window.location='index.php';</script>";
                                    }

                                    // Mengatur batas jam berdasarkan hari
                                    $hari = date('N'); // Hari dalam angka (1 = Senin, 7 = Minggu)
                                    $batasJam = ($hari == 5) ? 6 : 10; // 5 = Jumat, batas jam untuk Jumat adalah 6, untuk hari biasa adalah 10

                                    for ($i = 1; $i <= $batasJam; $i++) {
                                        $jamKey = "jam$i";
                                        $isChecked = !empty($data[$jamKey]) ? "checked" : "";
                                        echo '<div class="col-sm-3 mb-2">';
                                        echo '<div class="form-check">';
                                        echo "<input class='form-check-input' type='checkbox' id='jam$i' disabled $isChecked>";
                                        echo "<label class='form-check-label' for='jam$i'>Jam $i</label>";
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                    ?>


                                </div>
                                <div class="d-flex justify-content-between">
                                    <span></span>
                                    <button onclick="history.back()" type="button" class="btn btn-secondary">Kembali</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <!-- Barcode Section -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header text-center">
                            <h4>Foto Terakhir Absen</h4>
                        </div>
                        <div class="card-body text-center">
                            <img src="../absen/<?php echo $data['foto_absen'] ?>" class="img-fluid img-thumbnail" alt="Foto">

                        </div>
                    </div>
                </div>

            </div>
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->

            <!-- partial -->
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>

<!-- plugins:js -->
<script src="../vendors/base/vendor.bundle.base.js"></script>
<!-- endinject -->
<!-- Plugin js for this page-->
<script src="../vendors/chart.js/Chart.min.js"></script>
<!-- End plugin js for this page-->
<!-- inject:js -->
<script src="../js/off-canvas.js"></script>
<script src="../js/hoverable-collapse.js"></script>
<script src="../js/template.js"></script>
<script src="../js/todolist.js"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="../js/dashboard.js"></script>
<!-- End custom js for this page-->

</body>

</html>