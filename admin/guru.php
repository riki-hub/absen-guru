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
    <title>Data Guru | Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />


    <script src="../vendors/base/vendor.bundle.base.js"></script>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" href="../datables/datatables.min.css">
    <script src="../datables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />
    <script>
        function refresh() {
            window.location.reload();
        }
    </script>
</head>

<style>
    #crop_image {
        display: none;
        max-width: 100%;
    }



    .modal-body {
        max-height: calc(100vh - 210px);
        overflow-y: auto;
    }
</style>

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
            <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Guru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="needs-validation" action="proses/data_guru/tambah.php" method="post">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="firstName">Nama</label>
                                        <input type="text" class="form-control" name="nama" id="firstName" placeholder="" value="" required="">
                                        <div class="invalid-feedback">
                                            Valid first name is required.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName">Username</label>
                                        <input type="text" class="form-control" name="username" id="lastName" placeholder="" value="" required="">
                                        <div class="invalid-feedback">
                                            Valid last name is required.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName">Password</label>
                                        <input type="text" class="form-control" name="password" id="lastName" placeholder="" value="" required="">
                                        <div class="invalid-feedback">
                                            Valid last name is required.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName">Gender</label>
                                        <select class="form-control" name="gender" required autocomplete="off" id="">
                                            <option value="">Pilih...</option>
                                            <option value="male">Laki-Laki</option>
                                            <option value="female">Perempuan</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Valid last name is required.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastName">Jabatan</label>
                                        <input type="text" class="form-control" name="jabatan" id="lastName" placeholder="" value="" required="">
                                        <div class="invalid-feedback">
                                            Valid last name is required.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="foto_profil">Foto Profil:</label>
                                        <div class="custom-file">

                                            <input style="cursor: pointer;" type="file" class="custom-file-input" id="foto_profil" accept="image/*" onchange="previewAndCropImage(event)">
                                            <label class="custom-file-label" for="Pilih foto... ">Pilih foto... </label>
                                            <input type="hidden" id="cropped_image_data" name="cropped_image_data">

                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">

                                        <img id="crop_image" src="#" alt="Foto Profil Preview" class="mt-3">

                                    </div>
                                </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="refresh()" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                        </form>
                    </div>
                </div>
                    
            </div>
            <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cropperModalLabel">Potong Gambar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="img-container">
                                <img id="image" src="#" alt="Gambar untuk Dipotong">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="crop_button">Potong</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dino -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="font-weight-bold">Data Guru</h3><br>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalScrollable">
                            Tambah Guru
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                            <thead style="background-color:blue;color:aliceblue; ">
                                                <tr align="center">
                                                    <th> No </th>
                                                    <th> Nama Guru </th>
                                                    <th>Gender</th>
                                                    <th>Jabatan</th>
                                                    <th> Username </th>
                                                    <th> Password </th>
                                                    <th> Gambar</th>
                                                   <th style= "text-align: center;"> Action </th> 
                                                </tr>
                                            </thead>
                                            <?php
                                            include('../koneksi.php');
                                            $query = "SELECT * FROM akun_guru ORDER BY id ASC";
                                            $result = mysqli_query($koneksi, $query);
                                            if (!$result) {
                                                die("query error: " . mysqli_error($koneksi) . "-" . mysqli_error($koneksi));
                                            }
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $edit_modal_id = "editModal" . $row['id']; // ID modal yang unik
                                                $gender = $row['gender'];
                                            ?>

                                                <tr>
                                                    <td style="text-align: center;"><?php echo $no; ?></td>
                                                    <td><?php echo $row['nama']; ?></td>
                                                    <td><?php echo $row['gender']; ?></td>
                                                    <td><?php echo $row['jabatan']; ?></td>
                                                    <td><?php echo $row['username']; ?></td>
                                                    <td>Password di enkripsi</td>
                                                    <td><img src='../upload/<?php echo $row['gambar']; ?>' alt='Foto Profil' style='width: 50px; height: 50px;'></td>
                                                    <td style=" text-align: center;">
                                                        <button type="button" class="btn btn-warning mdi mdi-tooltip-edit" style="font-size: 20px;" data-toggle="modal" data-target="#<?php echo $edit_modal_id; ?>"><i class="fa-regular fa-pen-to-square"></i></button>
                                                        <a title="hapus" class="btn btn-danger  delete-btn" style="font-size: 20px;" id="delete-btn" data-id="<?php echo $row['id']; ?>"><i class="fa-solid fa-trash-can"></i></a>

                                                    </td>
                                                </tr>
                                                <!-- Modal -->
                                                <div class="modal fade" id="<?php echo $edit_modal_id; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="editModalLabel">Edit </h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form class="needs-validation" action="proses/data_guru/edit.php" method="post">
                                                                    <div class="row">
                                                                        <div class="col-md-12 mb-3">
                                                                            <label for="firstName">Nama</label>
                                                                            <input type="text" class="form-control" name="nama" id="firstName" placeholder="" value="<?php echo $row['nama']; ?>" required>
                                                                            <div class="invalid-feedback">
                                                                                Valid first name is required.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="lastName">Username</label>
                                                                            <input type="text" class="form-control" name="username" id="lastName" placeholder="" value="<?php echo $row['username']; ?>" required>
                                                                            <input type="hidden" name="id" id="op" value="<?php echo $row['id']; ?>">
                                                                            <div class="invalid-feedback">
                                                                                Valid last name is required.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="lastName">Password</label>
                                                                            <input type="text" class="form-control" name="password" id="lastName" placeholder="">
                                                                            <div class="invalid-feedback">
                                                                                Valid last name is required.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">

                                                                            <label for="lastName">Gender</label>
                                                                            <select class="form-control" name="gender" required autocomplete="off" id="">
                                                                                <option value="">Pilih...</option>
                                                                                <option value="male" <?php echo ($gender == 'male') ? 'selected' : ''; ?>>Laki-Laki</option>
                                                                                <option value="female" <?php echo ($gender == 'female') ? 'selected' : ''; ?>>Perempuan</option>
                                                                            </select>
                                                                            <div class="invalid-feedback">
                                                                                Valid last name is required.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="lastName">Jabatan</label>
                                                                            <input type="text" class="form-control" name="jabatan" id="lastName" placeholder="" value="<?php echo $row['jabatan']; ?>" required="">
                                                                            <div class="invalid-feedback">
                                                                                Valid last name is required.
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label for="foto_profil">Foto Profil:</label>
                                                                            <div class="custom-file">
                                                                                <input style="cursor: pointer;" type="file" class="custom-file-input" id="foto_profil_<?php echo $edit_modal_id; ?>" accept="image/*" onchange="previewAndCropImageedit(event, '<?php echo $edit_modal_id; ?>')">
                                                                                <label class="custom-file-label" for="img">Pilih foto...</label>
                                                                            </div>
                                                                            <input type="hidden" id="cropped_image_dataedit_<?php echo $edit_modal_id; ?>" name="cropped_image_dataedit">
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <img id="crop_imageedit_<?php echo $edit_modal_id; ?>" src="../upload/<?php echo $row['gambar']; ?>" alt="Foto Profil Preview" class="mt-3">
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

                                                <div class="modal fade" id="cropperModal_<?php echo $edit_modal_id; ?>" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="cropperModalLabel">Potong Gambar</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="img-container">
                                                                    <img id="imageedit_<?php echo $edit_modal_id; ?>" src="#" alt="Gambar untuk Dipotong">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                <button type="button" class="btn btn-primary" id="crop_buttonedit_<?php echo $edit_modal_id; ?>">Potong</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                                $no++;
                                            }
                                            ?>
                                        </table>
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
    <!-- container-scroller -->

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
                            fetch(`proses/data_guru/hapus.php?id=${id}`, {
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


    <script>
        $(document).ready(function() {
            $('#table').DataTable();
        });
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

<script>
    function updateFileName(input, modalId) {
        var fileName = input.files[0].name;
        document.getElementById('fileNameDisplay_' + modalId).innerText = fileName;
    }
</script>

<script>
    var cropper;
    var image = document.getElementById('image');
    var cropImage = document.getElementById('crop_image');
    var croppedImageDataInput = document.getElementById('cropped_image_data');

    function previewAndCropImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('image');
            output.src = reader.result;
            $('#cropperModal').modal('show');
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    $('#cropperModal').on('shown.bs.modal', function() {
        var img = image;
        var naturalWidth = img.naturalWidth;
        var naturalHeight = img.naturalHeight;
        var modalDialog = $(this).find('.modal-dialog');
        modalDialog.css('max-width', naturalWidth + 'px');

        cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 3
        });
    }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
    });

    document.getElementById('crop_button').addEventListener('click', function() {
        var canvas = cropper.getCroppedCanvas({
            width: 200,
            height: 200,
        });
        cropImage.src = canvas.toDataURL();
        cropImage.style.display = 'block';
        croppedImageDataInput.value = canvas.toDataURL();
        $('#cropperModal').modal('hide');
    });
</script>

<script>
    var cropper;
    var image, cropImage, croppedImageDataInput;

    function previewAndCropImageedit(event, modalId) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imageedit_' + modalId);
            output.src = reader.result;
            $('#cropperModal_' + modalId).modal('show');
        }
        reader.readAsDataURL(event.target.files[0]);

        $('#cropperModal_' + modalId).on('shown.bs.modal', function() {
            image = document.getElementById('imageedit_' + modalId);
            cropImage = document.getElementById('crop_imageedit_' + modalId);
            croppedImageDataInput = document.getElementById('cropped_image_dataedit_' + modalId);

            cropper = new Cropper(image, {
                aspectRatio: 1,
                viewMode: 3
            });
        }).on('hidden.bs.modal', function() {
            cropper.destroy();
            cropper = null;
        });

        document.getElementById('crop_buttonedit_' + modalId).addEventListener('click', function() {
            var canvas = cropper.getCroppedCanvas({
                width: 200,
                height: 200,
            });
            cropImage.src = canvas.toDataURL();
            cropImage.style.display = 'block';
            croppedImageDataInput.value = canvas.toDataURL();
            $('#cropperModal_' + modalId).modal('hide');
        });
    }
</script>

</html>