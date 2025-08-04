<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'admin') {
  header("Location: ../");
  $_SESSION['gambar'];
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <title>Dashboard | Admin</title>
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
    <div class="container-fluid page-body-wrapper">
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
          <li class="nav-item">
            <a class="nav-link" href="data_bulanan.php">Data Hadir Bulanan</a>
          </li>
        </ul>
      </div>
    </li>

      <!-- Menu Agenda dengan dropdown animasi -->
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#agenda" aria-expanded="false" aria-controls="agenda">
          <i class="ti-agenda menu-icon"></i>
          <span class="menu-title">Data Agenda</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="agenda">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item">
              <a class="nav-link" href="agenda.php">Agenda Harian</a>
            </li>
            <!-- Tambahkan submenu lain di sini jika dibutuhkan -->
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
          <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h4 class="font-weight-bold mb-0">Dashboard</h4>
                </div>
              </div>
            </div>
          </div>
          <?php
          include '../koneksi.php';
          $p = 0;
          $query  = "SELECT count(id) AS d_a FROM akun_guru";
          $sql    = mysqli_query($koneksi, $query);
          if (mysqli_num_rows($sql) > 0) {
            $data = mysqli_fetch_assoc($sql);
            $p  = $data['d_a'];
          }
          // FB
          $b = 0;
          $query  = "SELECT count(id) AS d_b FROM admin ";
          $sql    = mysqli_query($koneksi, $query);
          if (mysqli_num_rows($sql) > 0) {
            $data = mysqli_fetch_assoc($sql);
            $b  = $data['d_b'];
          }

          // FC
          $m = 0;
          $query  = "SELECT count(id) AS d_c FROM izin";
          $sql    = mysqli_query($koneksi, $query);
          if (mysqli_num_rows($sql) > 0) {
            $data = mysqli_fetch_assoc($sql);
            $m  = $data['d_c'];
          }

          // MTC
          $today = date('Y-m-d');
          $k = 0;
          $query  = "SELECT count(id) AS jml FROM absen_msk WHERE tanggal = '$today'";
          $sql    = mysqli_query($koneksi, $query);
          if (mysqli_num_rows($sql) > 0) {
            $data = mysqli_fetch_assoc($sql);
            $k  = $data['jml'];
          }

          ?>

          <div class="row">
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">Guru</p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                      <?= number_format($p) ?>
                    </h3>
                    <i class="fa-solid fa-chalkboard-user icon-md  text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">
                    admin
                  </p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                      <?= number_format($b) ?>
                    </h3>
                    <i class="fa-solid fa-user-tie icon-md  text-muted mb-0 mb-md-3 mb-xl-0"></i>
                  </div>

                </div>
              </div>
            </div>
            <div class="col-md-3 grid-margin stretch-card">
              <div class="card">
                <div class="card-body">
                  <p class="card-title text-md-center text-xl-left">
                    Masuk
                  </p>
                  <div class="d-flex flex-wrap justify-content-between justify-content-md-center justify-content-xl-between align-items-center">
                    <h3 class="mb-0 mb-md-2 mb-xl-0 order-md-1 order-xl-0">
                      <?= number_format($k) ?>
                    </h3>
                    <i class="fa-solid fa-tags icon-md  text-muted mb-0 mb-md-3 mb-xl-0"></i>
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
  <script src="../vendors/base/vendor.bundle.base.js"></script>
  <script src="../vendors/chart.js/Chart.min.js"></script>
  <script src="../js/off-canvas.js"></script>
  <script src="../js/hoverable-collapse.js"></script>
  <script src="../js/template.js"></script>
  <script src="../js/todolist.js"></script>
  <script src="../js/dashboard.js"></script>
</body>

</html>