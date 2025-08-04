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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../datables/datatables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />

    <style>
        #crop_image {
            display: none;
            max-width: 100%;
        }
        .modal-body {
            max-height: calc(100vh - 210px);
            overflow-y: auto;
        }
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
               .input-group-text {
    background-color: #007bff;
    color: white;
    border: none;
}
#searchInput {
    border-radius: 0.25rem 0 0 0.25rem;
}
    </style>
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
                                var months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                var myDays = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
                                var date = new Date();
                                var day = date.getDate();
                                var month = date.getMonth();
                                var thisDay = date.getDay(), thisDay = myDays[thisDay];
                                var yy = date.getYear();
                                var year = (yy < 1000) ? yy + 1900 : yy;
                                document.write("<h4>" + thisDay + ", " + day + " " + months[month] + " " + year + "</h4>");
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
                            <a class="dropdown-item" href="profil.php">
                                <i class="ti-settings text-primary"></i>
                                Settings
                            </a>
                            <a class="dropdown-item" href="logot.php">
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

            <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Guru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
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
                                        <label for="foto_profil">Foto Profil:</label>
                                        <input type="file" class="form-control-file" id="foto_profil" accept="image/*" required onchange="previewAndCropImage(event)">
                                        <input type="hidden" id="cropped_image_data" name="cropped_image_data">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <img id="crop_image" src="#" alt="Foto Profil Preview" class="mt-3">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="cropperModal" tabindex="-1" role="dialog" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="cropperModalLabel">Potong Gambar</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
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
            <!-- Main Panel -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header"></div>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">Data Materi</h6>
                            <div class="d-flex">
                                <!-- Search Input -->
        <div class="input-group mr-2" style="width: 200px;">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama Guru..." aria-label="Search">
            <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fas fa-search"></i>
                </span>
            </div>
        </div>
                                <a style="margin-top: 5px; margin-left: 5px;" href="proses/materi/cetak.php" target="_blank" class="btn btn-primary btn-icon-split btn-sm">
                                    <span class="icon text-white-55">
                                        <i class="fas fa-print"></i>
                                    </span>
                                    <span class="text">Cetak Data</span>
                                </a>
                            </div>
                        </div>

                        <!-- Tabel Data -->
                        <div class="row">
                            <div class="col-lg-12 grid-margin">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                            <thead style="background-color: blue; color: aliceblue;">
                                                <tr>
                                                    <th style="text-align: center;">No</th>
                                                    <th>Tanggal</th>
                                                    <th>Nama Guru</th>
                                                    <th>Mata Pelajaran</th>
                                                    <th style="text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                include '../koneksi.php';
                                                $cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
                                                $sql = "SELECT * FROM materi";
                                                if (!empty($cari)) {
                                                    $sql .= " WHERE nama_guru LIKE '%$cari%' OR mapel LIKE '%$cari%' OR materi LIKE '%$cari%'";
                                                }
                                                $sql .= " ORDER BY tanggal_upload DESC";
                                                $result = $koneksi->query($sql);
                                                $no = 1;
                                                if ($result->num_rows > 0):
                                                    while ($row = $result->fetch_assoc()):
                                                ?>
                                                    <tr>
                                                        <td style="text-align: center;"><?= $no++; ?></td>
                                                        <td><?= htmlspecialchars($row['tanggal_upload']); ?></td>
                                                        <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                                                        <td><?= htmlspecialchars($row['mapel']); ?></td>
                                                        <td style="text-align: center;">
                                                            <a href="proses/materi/hapusmateri.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data?')" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Edit -->
                                                    <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="proses/materi/edit_materi.php" method="post">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Edit Materi</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nama Guru</label>
                                                                            <input type="text" name="nama_guru" class="form-control" value="<?= htmlspecialchars($row['nama_guru']); ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Mata Pelajaran</label>
                                                                            <input type="text" name="mapel" class="form-control" value="<?= htmlspecialchars($row['mapel']); ?>" required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Tanggal</label>
                                                                            <input type="date" name="tanggal_upload" class="form-control" value="<?= htmlspecialchars($row['tanggal_upload']); ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endwhile; ?>
                                                <?php else: ?>
                                                    <tr><td colspan="6" class="text-center">Data tidak ditemukan.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
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

    <!-- Tambahkan fungsi crop image -->
    <script>
        let cropper;

        function previewAndCropImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const image = document.getElementById('image');
                    image.src = e.target.result;
                    $('#cropperModal').modal('show');

                    if (cropper) {
                        cropper.destroy();
                    }
                    cropper = new Cropper(image, {
                        aspectRatio: 1,
                        viewMode: 1,
                    });
                };
                reader.readAsDataURL(file);
            }
        }

        document.getElementById('crop_button').addEventListener('click', function() {
            const canvas = cropper.getCroppedCanvas();
            if (canvas) {
                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('cropped_image_data').value = e.target.result;
                        document.getElementById('crop_image').src = e.target.result;
                        document.getElementById('crop_image').style.display = 'block';
                    };
                    reader.readAsDataURL(blob);
                    $('#cropperModal').modal('hide');
                });
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete').forEach(button => {
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
                            fetch(`proses/materi/hapus.php`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json'
                                }
                            }).then(response => {
                                if (response.ok) {
                                    swalWithBootstrapButtons.fire({
                                        title: "Terhapus!",
                                        text: "Sukses menghapus data!",
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
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data materi ini akan dihapus!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Hapus',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('proses/materi/hapus.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    id: id
                                }),
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Success:', data);
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Data materi berhasil dihapus.',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi error saat menghapus data.',
                                    icon: 'error'
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function() {
        const filter = searchInput.value.toLowerCase();
        const table = document.querySelector('.table');
        const rows = table.getElementsByTagName('tr');

        // Loop through all table rows, starting from the second row (skip header)
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            const nameCell = cells[2]; // Nama Guru is in the third column (index 2)
            const mapelCell = cells[3]; // Mata Pelajaran is in the fourth column (index 3)
            let match = false;

            // Check if either Nama Guru or Mata Pelajaran matches the filter
            if (nameCell || mapelCell) {
                const nameText = nameCell ? (nameCell.textContent || nameCell.innerText).toLowerCase() : '';
                const mapelText = mapelCell ? (mapelCell.textContent || mapelCell.innerText).toLowerCase() : '';
                if (nameText.indexOf(filter) > -1 || mapelText.indexOf(filter) > -1) {
                    match = true;
                }
            }

            // Show or hide the row based on the match
            rows[i].style.display = match ? '' : 'none';
        }
    });
});
</script>

    <script>
        $(document).ready(function() {
            $('#table').DataTable({
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "columns": [
                    { "data": null, "defaultContent": "" }, // No
                    { "data": "tanggal_upload" },
                    { "data": "nama_guru" },
                    { "data": "mapel" },
                    { "data": null, "defaultContent": "", "orderable": false } // Action
                ],
                "columnDefs": [
                    {
                        "targets": 0, // No column
                        "render": function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    }
                ],
                "language": {
                    "emptyTable": "Data tidak ditemukan.",
                    "info": "Menampilkan _START_ hingga _END_ dari _TOTAL_ entri",
                    "infoEmpty": "Menampilkan 0 hingga 0 dari 0 entri",
                    "infoFiltered": "(difilter dari _MAX_ total entri)",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "search": "Cari:",
                    "zeroRecords": "Tidak ada data yang cocok",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });

            // Inisialisasi collapse untuk sidebar
            $('.sidebar .nav-item > a[data-toggle="collapse"]').on('click', function() {
                var $this = $(this);
                var target = $this.attr('href');
                $(target).collapse('toggle');
            });
        });
    </script>

    <!-- plugins:js -->
    <script src="../vendors/base/vendor.bundle.base.js"></script>
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
