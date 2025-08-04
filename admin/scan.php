<?php
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
    <title>Scan | Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="../vendors/base/vendor.bundle.base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <script src="../js/html5-qrcode.min.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>

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

                    <div class="modal fade" id="scanResultModal" tabindex="-1" aria-labelledby="scanResultModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="scanResultModalLabel">Scan Result</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="result" id="scanResultText">
                                    <form action="proses/scan/masuk.php" method="post" id="locationForm">
                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label for="">Nama Guru</label>
                                                <input class="form-control" type="text" name="nama_guru" value="<?= $_SESSION['nama']; ?>" id="" readonly>
                                            </div>

                                            <div class="col-md-3 ">
                                                <label for="">Jam Masuk</label>
                                                <input class="form-control" type="text" name="jamcuy" id="jam" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3 ">
                                                <label for="">Tanggal</label>
                                                <input class="form-control" type="text" name="beda" id="tanggal" readonly>
                                            </div>
                                        </div>

                                        <label for="">kordinat anda</label>
                                        <input type="hidden" name="latitude" id="latitude">
                                        <input type="hidden" name="longitude" id="longitude">
                                        <div id="location"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Masuk</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <style>
                        #reader {
                            border: 1px solid #ddd;
                            max-width: 100%;
                            height: auto;
                        }



                        @media (min-width: 992px) {

                            /* Adjust size for desktops and larger screens */
                            #reader {
                                width: 500px;
                                /* Larger width for desktops */
                                height: 500px;
                            }
                        }
                    </style>

                    <div class="container mt-5">
                        <div class="row justify-content-center">
                            <div class=" text-center">
                                <p>Scanner</p>
                                <div id="reader"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
                <!-- partial:partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024
                            <a href="" target="_blank">Tim RPL</a>. All rights reserved.</span>

                    </div>
                </footer>

            </div>

        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function getCurrentDateInYMD() {

        }
    </script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            console.log(`Code matched = ${decodedText}`, decodedResult);
            const currentDate = new Date();
            const currentDay = currentDate.getDay();
            const currentHour = currentDate.getHours();
            const currentMinute = currentDate.getMinutes();
            const timestamp = currentDate.toLocaleTimeString();

            if (decodedText === "MSN002") {
                let jamAbsen = null;

                if (currentDay === 1) { // Senin
                    if ((currentHour === 6 && currentMinute >= 0) || (currentHour === 7 && currentMinute < 0)) { // 06:00 - 07:29
                        jamAbsen = 0; // jam0
                    } else if ((currentHour === 8 && currentMinute < 35) || // 08:00 - 08:34
                        (currentHour === 8 && currentMinute >= 35 && currentMinute < 60) || // 08:35 - 08:59
                        (currentHour === 9 && currentMinute < 45) || // 09:00 - 09:44
                        (currentHour === 9 && currentMinute >= 45 && currentMinute < 60) || // 09:45 - 09:59
                        (currentHour === 10 && currentMinute < 50) || // 10:00 - 10:49
                        (currentHour === 10 && currentMinute >= 50 && currentMinute < 60) || // 10:50 - 10:59
                        (currentHour === 11 && currentMinute < 25) || // 11:00 - 11:24
                        (currentHour === 11 && currentMinute >= 25 && currentMinute < 60) || // 11:25 - 11:59
                        (currentHour === 12 && currentMinute < 30) || // 12:00 - 12:29
                        (currentHour === 12 && currentMinute >= 30 && currentMinute < 60) || // 12:30 - 12:59
                        (currentHour === 13 && currentMinute < 5) || // 13:00 - 13:04
                        (currentHour === 13 && currentMinute >= 5 && currentMinute < 40) || // 13:05 - 13:39
                        (currentHour === 13 && currentMinute >= 40 && currentMinute < 60) || // 13:40 - 13:59
                        (currentHour === 14 && currentMinute < 15) || // 14:00 - 14:14
                        (currentHour === 14 && currentMinute >= 15 && currentMinute < 50)) { // 14:15 - 14:49

                        if (currentHour === 8 && currentMinute < 35) jamAbsen = 1; // 08:00 - 08:34
                        else if (currentHour === 8 && currentMinute >= 35) jamAbsen = 2; // 08:35 - 08:59
                        else if (currentHour === 9 && currentMinute < 45) jamAbsen = 3; // 09:00 - 09:44
                        else if (currentHour === 9 && currentMinute >= 45) jamAbsen = "Istirahat 1"; // 09:45 - 09:59
                        else if (currentHour === 10 && currentMinute < 50) jamAbsen = 4; // 10:00 - 10:49
                        else if (currentHour === 10 && currentMinute >= 50) jamAbsen = 5; // 10:50 - 10:59
                        else if (currentHour === 11 && currentMinute < 25) jamAbsen = 5; // 11:00 - 11:24
                        else if (currentHour === 11 && currentMinute >= 25) jamAbsen = 6; // 11:25 - 11:59
                        else if (currentHour === 12 && currentMinute < 30) jamAbsen = "Istirahat 2"; // 12:00 - 12:29
                        else if (currentHour === 12 && currentMinute >= 30) jamAbsen = 7; // 12:30 - 12:59
                        else if (currentHour === 13 && currentMinute < 5) jamAbsen = 7; // 13:00 - 13:04
                        else if (currentHour === 13 && currentMinute >= 5 && currentMinute < 40) jamAbsen = 8; // 13:05 - 13:39
                        else if (currentHour === 13 && currentMinute >= 40) jamAbsen = 9; // 13:40 - 13:59
                        else if (currentHour === 14 && currentMinute < 15) jamAbsen = 9; // 14:00 - 14:14
                        else if (currentHour === 14 && currentMinute >= 15) jamAbsen = 10; // 14:15 - 14:49
                    }
                } else if (currentDay === 5) { // Jumat
                    if ((currentHour === 6 && currentMinute >= 0) || (currentHour === 7 && currentMinute < 0)) { // 06:00 - 07:29
                        jamAbsen = 0; // jam0
                    } else if ((currentHour === 7 && currentMinute >= 45) || // 07:45 - 07:59
                        (currentHour === 8 && currentMinute < 55) || // 08:00 - 08:54
                        (currentHour === 9 && currentMinute < 30) || // 09:00 - 09:29
                        (currentHour === 9 && currentMinute >= 30 && currentMinute < 50) || // 09:30 - 09:49
                        (currentHour === 9 && currentMinute >= 50) || // 09:50 - 09:59
                        (currentHour === 10 && currentMinute < 25) || // 10:00 - 10:24
                        (currentHour === 10 && currentMinute >= 25) || // 10:25 - 10:59
                        (currentHour === 11 && currentMinute < 35)) { // 11:00 - 11:34

                        if (currentHour === 7 && currentMinute >= 45) jamAbsen = 1; // 07:45 - 07:59
                        else if (currentHour === 8 && currentMinute < 55) jamAbsen = 2; // 08:00 - 08:54
                        else if (currentHour === 9 && currentMinute < 30) jamAbsen = 3; // 09:00 - 09:29
                        else if (currentHour === 9 && currentMinute >= 30 && currentMinute < 50) jamAbsen = "Istirahat"; // 09:30 - 09:49
                        else if (currentHour === 9 && currentMinute >= 50) jamAbsen = 4; // 09:50 - 09:59
                        else if (currentHour === 10 && currentMinute < 25) jamAbsen = 4; // 10:00 - 10:24
                        else if (currentHour === 10 && currentMinute >= 25) jamAbsen = 5; // 10:25 - 10:59
                        else if (currentHour === 11 && currentMinute < 35) jamAbsen = 6; // 11:00 - 11:34
                    }
                } else { // Selasa sampai Kamis
                    if ((currentHour === 6 && currentMinute >= 0) || (currentHour === 7 && currentMinute < 0)) { // 06:00 - 07:29
                        jamAbsen = 0; // jam0
                    } else if ((currentHour === 7 && currentMinute >= 30 && currentMinute < 5) || // 07:30 - 08:05
                        (currentHour === 8 && currentMinute >= 5 && currentMinute < 40) || // 08:05 - 08:40
                        (currentHour === 8 && currentMinute >= 40 && currentMinute < 15) || // 08:40 - 09:15
                        (currentHour === 9 && currentMinute >= 15 && currentMinute < 50) || // 09:15 - 09:50
                        (currentHour === 10 && currentMinute >= 25 && currentMinute < 0) || // 10:25 - 11:00
                        (currentHour === 11 && currentMinute >= 0 && currentMinute < 35) || // 11:00 - 11:35
                        (currentHour === 11 && currentMinute >= 35 && currentMinute < 10) || // 11:35 - 12:10
                        (currentHour === 12 && currentMinute >= 45 && currentMinute < 20) || // 12:45 - 13:20
                        (currentHour === 13 && currentMinute >= 20 && currentMinute < 55) || // 13:20 - 13:55
                        (currentHour === 13 && currentMinute >= 55) || // 13:55 - 14:30
                        (currentHour === 14 && currentMinute >= 30 && currentMinute < 15)) { // 14:30 - 15:15

                        if (currentHour === 7 && currentMinute >= 30) jamAbsen = 1; // 07:30 - 08:05
                        else if (currentHour === 8 && currentMinute >= 5) jamAbsen = 2; // 08:05 - 08:40
                        else if (currentHour === 8 && currentMinute >= 40) jamAbsen = 3; // 08:40 - 09:15
                        else if (currentHour === 9 && currentMinute >= 15) jamAbsen = 4; // 09:15 - 09:50
                        else if (currentHour === 10 && currentMinute >= 25) jamAbsen = "Istirahat 1"; // 10:25 - 11:00
                        else if (currentHour === 11 && currentMinute >= 0) jamAbsen = 5; // 11:00 - 11:35
                        else if (currentHour === 11 && currentMinute >= 35) jamAbsen = 6; // 11:35 - 12:10
                        else if (currentHour === 12 && currentMinute >= 10) jamAbsen = "Istirahat 2"; // 11:35 - 12:10
                        else if (currentHour === 12 && currentMinute >= 45) jamAbsen = 7; // 12:45 - 13:20
                        else if (currentHour === 13 && currentMinute >= 20) jamAbsen = 8; // 13:20 - 13:55
                        else if (currentHour === 13 && currentMinute >= 55) jamAbsen = 9; // 13:55 - 14:30
                        else if (currentHour === 14 && currentMinute >= 30) jamAbsen = 10; // 14:30 - 15:15
                    }
                }
                if (jamAbsen !== null) {
                    const today = new Date();
                    const year = today.getFullYear();
                    const month = String(today.getMonth() + 1).padStart(2, '0'); // Month is zero-based, so +1
                    const day = String(today.getDate()).padStart(2, '0'); // Pad single digits with leading zero
                    document.getElementById('tanggal').value = `${year}-${month}-${day}`;
                    document.getElementById('jam').value = `${timestamp}`;
                    document.getElementById('scanResultModalLabel').textContent = `Jam Absensi: Jam ke-${jamAbsen}`;
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(showPosition, showError);
                    } else {
                        document.getElementById("status").textContent = "Geolocation tidak didukung oleh browser ini.";
                    }

                    function showPosition(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lng;
                        document.getElementById("location").innerHTML = `
                    <input class="form-control" type="text" name="kordinat" id="" value="${lat},${lng}" readonly>
                    <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank">Lihat di Google Maps</a>
                `;
                    }

                    function showError(error) {
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Pengguna menolak Permintaan Lokasi!",
                                    confirmButtonText: "OK",
                                    preConfirm: () => {
                                        window.location.href = "scan.php";
                                    }
                                });
                                break;
                            case error.POSITION_UNAVAILABLE:
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Informasi Lokasi Tidak Tersedia",
                                    confirmButtonText: "OK",
                                    preConfirm: () => {
                                        window.location.href = "scan.php";
                                    }
                                });
                                break;
                            case error.TIMEOUT:
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Waktu Habis",
                                    confirmButtonText: "OK",
                                    preConfirm: () => {
                                        window.location.href = "scan.php";
                                    }
                                });
                                break;
                            case error.UNKNOWN_ERROR:
                                Swal.fire({
                                    icon: "error",
                                    title: "Oops...",
                                    text: "Error yang tidak diketahui",
                                    confirmButtonText: "OK",
                                    preConfirm: () => {
                                        window.location.href = "scan.php";
                                    }
                                });
                                break;
                        }
                    }
                    $('#scanResultModal').modal('show');
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "Diluar Jam Absen!",
                        confirmButtonText: "OK",
                        preConfirm: () => {
                            window.location.href = "scan.php";
                        }
                    });
                }
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Kamu salah scan Barcode!",
                    confirmButtonText: "OK",
                    preConfirm: () => {
                        window.location.href = "scan.php";
                    }
                });
            }
        }






        function restartScanner() {
            // Logic to restart the scanner
            console.log('Restarting the scanner...');
            html5QrcodeScanner.clear();
            window.location.reload();

        }

        $(document).ready(function() {
            // Event listener for when the modal is hidden
            $('#scanResultModal').on('hidden.bs.modal', function() {
                restartScanner();
            });
        });

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: 250
        });

        // Memulai pemindaian.
        html5QrcodeScanner.render(onScanSuccess);

        // Kirim form secara otomatis setelah modal ditutup
    </script>
    <!-- plugins:js -->

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