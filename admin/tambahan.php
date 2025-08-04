<?php
// memanggil file koneksi.php untuk membuat koneksi

include '../koneksi.php';

// mengecek apakah di url ada nilai GET id
if (isset($_GET['id'])) {
    // ambil nilai id dari url dan disimpan dalam variabel $id
    $id = ($_GET["id"]);

    // menampilkan data dari database yang mempunyai id=$id dari tabel absen_msk
    $query = "SELECT absen_msk.*, akun_guru.* 
              FROM absen_msk 
              INNER JOIN akun_guru ON absen_msk.nama = akun_guru.nama 
              WHERE absen_msk.id='$id'";
    $result = mysqli_query($koneksi, $query);

    // jika data gagal diambil maka akan tampil error berikut
    if (!$result) {
        die("Query Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
    }

    // mengambil data dari database
    $data = mysqli_fetch_assoc($result);

    // apabila data tidak ada pada database maka akan dijalankan perintah ini
    if (!$data) {
        echo "<script>alert('Data tidak ditemukan pada database');window.location='index.php';</script>";
    }
} else {
    // apabila tidak ada data GET id pada akan di redirect ke index.php
    echo "<script>alert('Masukkan data id.');window.location='index.php';</script>";
}


session_start();
if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'admin') {
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
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="../vendors/base/vendor.bundle.base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <!-- endinject -->

    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>
    <style>
        .navbar-nav .nav-search h4 {
            margin: 0;
            font-size: 1rem;
            /* Adjust font size as needed */
            color: #6c757d;
            /* Adjust color as needed */
        }

        @media (max-width: 992px) {
            .nav-item.nav-search {
                display: block;
                text-align: center;
                width: 100%;
            }
        }
    </style>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="index.php"><img src="../images/logo.svg" class="mr-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="index.php"><img src="../images/logo-mini.png" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="ti-view-list"></span>
                </button>
                <ul class="navbar-nav mr-lg-2">
                    <li class="nav-item nav-search d-none d-lg-block">
                        <span>
                            <script type="text/javascript">
                                // <!-
                                var months = [
                                    "Januari",
                                    "Februari",
                                    "Maret",
                                    "April",
                                    "Mei",
                                    "Juni",
                                    "Juli",
                                    "Agustus",
                                    "September",
                                    "Oktober",
                                    "November",
                                    "Desember",
                                ];
                                var myDays = [
                                    "Minggu",
                                    "Senin",
                                    "Selasa",
                                    "Rabu",
                                    "Kamis",
                                    "Jumat",
                                    "Sabtu",
                                ];
                                var date = new Date();
                                var day = date.getDate();
                                var month = date.getMonth();
                                var thisDay = date.getDay(),
                                    thisDay = myDays[thisDay];
                                var yy = date.getYear();
                                var year = yy < 1000 ? yy + 1900 : yy;
                                document.write(
                                    "<h4>" +
                                    thisDay +
                                    ", " +
                                    day +
                                    " " +
                                    months[month] +
                                    " " +
                                    year +
                                    "</h4>"
                                );
                                //-->
                            </script>
                        </span>
                    </li>
                </ul>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            <img src="../upload/<?= $_SESSION['gambar']; ?>" alt="profile" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                            <a class="dropdown-item">
                                <i class="ti-settings text-primary"></i>
                                Settings
                            </a>
                            <a class="dropdown-item">
                                <i class="ti-power-off text-primary"></i>
                                Logout
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                    <span class="ti-view-list"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="ti-home menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                     <?php if ($_SESSION['username'] != 'admin') : ?>
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
          <i class="ti-dashboard menu-icon"></i>
          <span class="menu-title">Data Master</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="ui-basic">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link" href="pengguna.php">Data Pengguna</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="guru.php">Data Guru</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="kelas.php">Data Kelas</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="siswa.php">Data Siswa</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="mapel.php">Data Mata Pelajaran</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="location.php">Location</a>
            </li>
          </ul>
        </div>
      </li>
    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#hadir" aria-expanded="false" aria-controls="hadir">
                            <i class="ti-layout-grid2 menu-icon"></i>
                            <span class="menu-title">Data Kehadiran</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="hadir">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="izin.php">Data Izin</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="data.php">Data Hadir</a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="logot.php">
                            <i class="fa-solid fa-arrow-right-from-bracket menu-icon"></i>
                            <span class="menu-title">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="container mt-5">
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

                                                // mengecek apakah di url ada nilai GET id
                                                if (isset($_GET['id'])) {
                                                    // ambil nilai id dari url dan disimpan dalam variabel $id
                                                    $id = ($_GET["id"]);

                                                    // menampilkan data dari database yang mempunyai id=$id dari tabel absen_msk
                                                    $query = "SELECT absen_msk.*, akun_guru.* 
              FROM absen_msk 
              INNER JOIN akun_guru ON absen_msk.nama = akun_guru.nama 
              WHERE absen_msk.id='$id'";
                                                    $result = mysqli_query($koneksi, $query);

                                                    // jika data gagal diambil maka akan tampil error berikut
                                                    if (!$result) {
                                                        die("Query Error: " . mysqli_errno($koneksi) . " - " . mysqli_error($koneksi));
                                                    }

                                                    // mengambil data dari database
                                                    $p = mysqli_fetch_assoc($result);

                                                    // apabila p$p tidak ada pada database maka akan dijalankan perintah ini
                                                    if (!$p) {
                                                        echo "<script>alert('Data tidak ditemukan pada database');window.location='index.php';</script>";
                                                    }
                                                } else {
                                                    // apabila tidak ada p$p GET id pada akan di redirect ke index.php
                                                    echo "<script>alert('Masukkan p$p id.');window.location='index.php';</script>";
                                                }
                                                for ($i = 1; $i <= 10; $i++) {
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
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024
                            <a href="https://www.templatewatch.com/" target="_blank">Tim RPL</a>. All rights reserved.</span>

                    </div>
                </footer>
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