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
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />


<script src="../vendors/base/vendor.bundle.base.js"></script>

   


<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

<!-- Tambahkan di dalam tag <head> -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                            </ul>
                        </div>
                    </li>

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

                <div class="page-header">
                        <h3 class="font-weight-bold">Data Kelas</h3><br>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalScrollable">
                            Tambah Kelas
                        </button>
                    </div>



                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Data Kelas</h6>
                            <div class="d-flex">


                            </div>
                        </div>

                        <div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered" id="table" width="100%" cellspacing="0">
            <thead style="background-color:blue;color:aliceblue;">
                <tr align="center">
                    <th style="background-color:blue;color:aliceblue;">No</th>
                    <th style="background-color:blue;color:aliceblue;">Kelas</th>
                    <th style="background-color:blue;color:aliceblue;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include('../koneksi.php');
                $query = "SELECT * FROM kelas";
                $result = mysqli_query($koneksi, $query);
                if (!$result) {
                    die("Query error: " . mysqli_error($koneksi));
                }
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no; ?></td>
                        <td><?php echo $row['nama_kelas']; ?></td>
                        <td style="text-align: center;">
                            <a href="barcode.php?namakelas=<?php echo $row['nama_kelas']; ?>" class="btn btn-primary mdi mdi-tooltip-edit" style="font-size: 20px;"><i class="fa-solid fa-qrcode"></i></a>
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning mdi mdi-tooltip-edit" style="font-size: 20px;"><i class="fa-regular fa-pen-to-square"></i></a>
                            <a title="hapus" class="btn btn-danger  delete-btn" style="font-size: 20px;" id="delete-btn" data-id="<?php echo $row['id']; ?>"><i class="fa-solid fa-trash-can"></i></a>
                        </td>
                    </tr>
                <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>




                    </div>


                </div>
                <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Kelas</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" action="proses/kelas/tambah.php" method="post">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="firstName">nama Kelas</label>
                                            <input type="text" class="form-control" name="nama_kelas" id="firstName" placeholder="" value="" required="">
                                            <div class="invalid-feedback">
                                                Valid first name is required.
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            </form>
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
    <!-- container-scroller -->

   
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Memuat DataTables -->
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- Inisialisasi DataTable -->
<script>
$(document).ready(function() {
    $('#table').DataTable({
        "pageLength": 5,  // Menampilkan 10 baris per halaman default
        "searching": true, // Menambahkan fitur pencarian
        "lengthChange": true, // Mengaktifkan opsi untuk mengubah jumlah baris per halaman
        "lengthMenu": [5, 10, 15, 20], // Pilihan jumlah baris yang bisa dipilih: 5, 10, 15
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: "btn btn-success",
                            cancelButton: "btn btn-danger"
                        },
                        buttonsStyling: false
                    });

                    swalWithBootstrapButtons.fire({
                        title: "Anda yakin?",
                        text: "Anda akan menghapus data ini!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Iya, hapus!",
                        cancelButtonText: "Tidak, batalkan!",
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Perform AJAX request to delete the item
                            fetch(`proses/proses_hapus.php?id=${id}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            }).then(response => {
                                if (response.ok) {
                                    swalWithBootstrapButtons.fire({
                                        title: "Terhapus!",
                                        text: "Sukses menghapus data!.",
                                        icon: "success"
                                    }).then(() => {
                                        // Optional: reload the page or remove the item from the DOM
                                        location.reload();
                                    });
                                } else {
                                    throw new Error('respon internet tidak bagus!.');
                                }
                            }).catch(error => {
                                swalWithBootstrapButtons.fire({
                                    title: "Error!",
                                    text: "Terjadi error saat menghapus data.",
                                    icon: "error"
                                });
                            });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            swalWithBootstrapButtons.fire({
                                title: "Dibatalkan",
                                text: "Berhasil dibatalkan",
                                icon: "error"
                            });
                        }
                    });
                });
            });
        });
    </script>
