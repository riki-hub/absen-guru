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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard | Admin</title>
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <script src="../vendors/base/vendor.bundle.base.js"></script>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>
    <style>
        .navbar-nav .nav-search h4 {
            margin: 0;
            font-size: 1rem;
            color: #6c757d;
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
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header">
                        <h3 class="font-weight-bold">Data Mata Pelajaran</h3><br>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahModal">
                            Tambah Mata Pelajaran
                        </button>
                    </div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Data Mata Pelajaran</h6>
                            <div class="d-flex"></div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                    <thead style="background-color:blue;color:aliceblue;">
                                        <tr align="center">
                                            <th style="background-color:blue;color:aliceblue;">No</th>
                                            <th style="background-color:blue;color:aliceblue;">Mata Pelajaran</th>
                                            <th style="background-color:blue;color:aliceblue;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        include('../koneksi.php');
                                        $query = "SELECT * FROM pelajaran";
                                        $result = mysqli_query($koneksi, $query);
                                        if (!$result) {
                                            die("Query error: " . mysqli_error($koneksi));
                                        }
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <tr>
                                                <td style="text-align: center;"><?php echo $no; ?></td>
                                                <td><?php echo $row['mata_pelajaran']; ?></td>
                                                <td style="text-align: center;">
                                                    <button class="btn btn-warning" style="font-size: 20px;" data-toggle="modal" data-target="#editModal" data-id="<?php echo $row['id']; ?>" data-nama="<?php echo htmlspecialchars($row['mata_pelajaran']); ?>">
                                                        <i class="fa-regular fa-pen-to-square"></i>
                                                    </button>
                                                    <a title="hapus" class="btn btn-danger delete-btn" style="font-size: 20px;" data-id="<?php echo $row['id']; ?>"><i class="fa-solid fa-trash-can"></i></a>
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
                <!-- Modal Tambah -->
                <div class="modal fade" id="tambahModal" tabindex="-1" role="dialog" aria-labelledby="tambahModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahModalTitle">Tambah Mata Pelajaran</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" action="proses/mapel/tambah.php" method="post" id="tambahForm">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="mataPelajaranTambah">Mata Pelajaran</label>
                                            <input type="text" class="form-control" name="mata_pelajaran" id="mataPelajaranTambah" placeholder="" value="" required>
                                            <div class="invalid-feedback">
                                                Nama mata pelajaran wajib diisi.
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
                </div>
                <!-- Modal Edit -->
                <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalTitle">Edit Mata Pelajaran</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="needs-validation" action="proses/mapel/edit.php" method="post" id="editForm" novalidate>
                                    <input type="hidden" name="id" id="editId">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label for="mataPelajaranEdit">Mata Pelajaran</label>
                                            <input type="text" class="form-control" name="mata_pelajaran" id="mataPelajaranEdit" required>
                                            <div class="invalid-feedback">
                                                Nama mata pelajaran wajib diisi.
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
                </div>
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2024
                            <a href="https://www.templatewatch.com/" target="_blank">Tim RPL</a>. All rights reserved.</span>
                    </div>
                </footer>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "pageLength": 5,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [5, 10, 15, 20],
            });

            // Validasi form tambah
            $('#tambahForm').on('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Harap isi semua kolom yang diperlukan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                $(this).addClass('was-validated');
            });

            // Validasi form edit
            $('#editForm').on('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    Swal.fire({
                        title: 'Error!',
                        text: 'Harap isi semua kolom yang diperlukan.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                $(this).addClass('was-validated');
            });

            // Isi modal edit dengan data menggunakan event delegation
            $(document).on('click', '.btn-warning', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');
                $('#editId').val(id);
                $('#mataPelajaranEdit').val(nama);
                $('#editForm').removeClass('was-validated');
            });

            // Hapus data
            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                const id = $(this).data('id');
                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success",
                        cancelButton: "btn btn-danger"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "Anda yakin?",
                    text: "Anda akan menghapus data mata pelajaran ini!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Iya, hapus!",
                    cancelButtonText: "Tidak, batalkan!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`proses/mapel/hapus.php?id=${id}`, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (response.ok) {
                                swalWithBootstrapButtons.fire({
                                    title: "Terhapus!",
                                    text: "Sukses menghapus data mata pelajaran!.",
                                    icon: "success"
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                throw new Error('Respon internet tidak bagus!.');
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
    </script>
    <script src="../vendors/chart.js/Chart.min.js"></script>
    <script src="../js/off-canvas.js"></script>
    <script src="../js/hoverable-collapse.js"></script>
    <script src="../js/template.js"></script>
    <script src="../js/todolist.js"></script>
    <script src="../js/dashboard.js"></script>
</body>

</html>