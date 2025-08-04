<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'admin') {
    header("Location: ../");
    exit();
}

include '../koneksi.php';

// Tangkap keyword pencarian dan filter kelas jika ada
$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';
$kelas_filter = isset($_GET['kelas_filter']) ? mysqli_real_escape_string($koneksi, $_GET['kelas_filter']) : '';

// Query data siswa dengan JOIN ke tabel kelas
$sql = "SELECT siswa.*, kelas.nama_kelas 
        FROM siswa 
        JOIN kelas ON siswa.kelas_id = kelas.id";
$where_conditions = [];
if (!empty($cari)) {
    $where_conditions[] = "(siswa.nama_siswa LIKE '%$cari%' OR kelas.nama_kelas LIKE '%$cari%')";
}
if (!empty($kelas_filter)) {
    $where_conditions[] = "siswa.kelas_id = '$kelas_filter'";
}
if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}
$sql .= " ORDER BY kelas_id ASC";

$result = $koneksi->query($sql);
$no = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Data Siswa | Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../vendors/ti-icons/css/themify-icons.css" />
    <link rel="stylesheet" href="../vendors/base/vendor.bundle.base.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <!-- endinject -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../datables/datatables.min.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />

    <style>
        #crop_image, #crop_imageedit_ {
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

        .table thead th {
            background-color: blue !important;
            color: aliceblue !important;
            padding: 10px !important;
        }

        .table th, .table td {
            vertical-align: middle !important;
            padding: 8px !important;
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
                                var year = yy < 1000 ? yy + 1900 : yy;
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
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <div class="page-header d-flex justify-content-between align-items-center">
                        <h3 class="font-weight-bold">Data Siswa</h3>
                        <div class="d-flex justify-content-end align-items-center gap-2">
                            <!-- Filter Kelas -->
                            <form action="" method="get" class="d-flex align-items-center">
                                <select name="kelas_filter" class="form-control me-2" style="width: 200px;" onchange="this.form.submit()">
                                    <option value="">Semua Kelas</option>
                                    <?php
                                    $kelas_query = $koneksi->query("SELECT * FROM kelas");
                                    while ($kelas = $kelas_query->fetch_assoc()):
                                    ?>
                                        <option value="<?= $kelas['id']; ?>" <?= $kelas['id'] == $kelas_filter ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($kelas['nama_kelas']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <?php if (!empty($cari)): ?>
                                    <input type="hidden" name="cari" value="<?= htmlspecialchars($cari); ?>">
                                <?php endif; ?>
                            </form>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahSiswa">
                                <i class="bi bi-person-plus-fill me-1"></i> Tambah Siswa
                            </button>
                        </div>
                    </div>

                    <!-- Tabel Data -->
                    <div class="row">
                        <div class="col-lg-12 grid-margin">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="table" width="100%" cellspacing="0">
                                            <thead style="background-color:blue;color:aliceblue;">
                                                <tr align="center">
                                                    <th style="text-align: center;">No</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Kelas</th>
                                                    <th style="text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ($result->num_rows > 0): ?>
                                                    <?php while ($row = $result->fetch_assoc()): ?>
                                                        <tr>
                                                            <td style="text-align: center;"><?= $no++; ?></td>
                                                            <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                                                            <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
                                                            <td style="text-align: center;">
                                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <a href="proses/siswa/hapussiswa.php?id=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data?')" class="btn btn-danger btn-sm">
                                                                    <i class="fas fa-trash-alt"></i>
                                                                </a>
                                                            </td>
                                                        </tr>

                                                        <!-- Modal Edit -->
                                                        <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id']; ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <form action="proses/siswa/edit.siswa.php" method="post">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Edit Siswa</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Nama Siswa</label>
                                                                                <input type="text" name="nama_siswa" class="form-control" value="<?= htmlspecialchars($row['nama_siswa']); ?>" required>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Kelas</label>
                                                                                <select name="kelas_id" class="form-control" required>
                                                                                    <?php
                                                                                    $kelas_query = $koneksi->query("SELECT * FROM kelas");
                                                                                    while ($kelas = $kelas_query->fetch_assoc()):
                                                                                    ?>
                                                                                        <option value="<?= $kelas['id']; ?>" <?= $kelas['id'] == $row['kelas_id'] ? 'selected' : ''; ?>>
                                                                                            <?= htmlspecialchars($kelas['nama_kelas']); ?>
                                                                                        </option>
                                                                                    <?php endwhile; ?>
                                                                                </select>
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
                                                    <tr><td colspan="4" class="text-center">Data tidak ditemukan.</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Tambah Siswa (dengan opsi impor) -->
                    <div class="modal fade" id="modalTambahSiswa" tabindex="-1" aria-labelledby="modalTambahSiswaLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="modalTambahSiswaLabel">Tambah Siswa</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual" type="button" role="tab" aria-controls="manual" aria-selected="true">Manual</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="import-tab" data-bs-toggle="tab" data-bs-target="#import" type="button" role="tab" aria-controls="import" aria-selected="false">Import Excel</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="myTabContent">
                                        <!-- Tab Manual -->
                                        <div class="tab-pane fade show active" id="manual" role="tabpanel" aria-labelledby="manual-tab">
                                            <form action="proses/siswa/tambahsiswa.php" method="post">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Siswa</label>
                                                    <input type="text" name="nama_siswa" class="form-control" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Kelas</label>
                                                    <select name="kelas_id" class="form-control" required>
                                                        <?php
                                                        $kelas_query = $koneksi->query("SELECT * FROM kelas");
                                                        while ($kelas = $kelas_query->fetch_assoc()):
                                                        ?>
                                                            <option value="<?= $kelas['id']; ?>"><?= htmlspecialchars($kelas['nama_kelas']); ?></option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- Tab Import -->
                                        <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                                            <form action="proses/siswa/import.php" method="POST" enctype="multipart/form-data">
                                                <div class="mb-3">
                                                    <label for="excel" class="form-label">Pilih File Excel:</label>
                                                    <input class="form-control" type="file" name="excel" id="excel" accept=".xls,.xlsx" required>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Upload</button>
                                                </div>
                                            </form>
                                            <form action="proses/siswa/download.php" method="post" class="mt-2">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="bi bi-download me-1"></i> Download Template
                                                </button>
                                            </form>
                                        </div>
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
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2025
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
    <script src="../vendors/base/vendor.bundle.base.js"></script>
    <script src="../datables/datatables.min.js"></script>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#table').DataTable();
        });

        // Autocomplete Pencarian
        $(document).ready(function() {
            $('#cari').on('input', function() {
                var keyword = $(this).val();
                if (keyword.length > 0) {
                    $.ajax({
                        url: 'cari_siswa.php',
                        method: 'GET',
                        data: { 
                            cari: keyword,
                            kelas_filter: '<?= $kelas_filter; ?>' // Include class filter in search
                        },
                        success: function(data) {
                            $('#hasil-pencarian').html(data).show();
                        }
                    });
                } else {
                    $('#hasil-pencarian').empty().hide();
                }
            });

            $(document).on('click', '.item-siswa', function() {
                var nama_siswa = $(this).text();
                $('#cari').val(nama_siswa);
                $('#hasil-pencarian').empty().hide();
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('#cari, #hasil-pencarian').length) {
                    $('#hasil-pencarian').empty().hide();
                }
            });
        });

        // Konfirmasi Hapus
        document.querySelectorAll('a[href*="hapussiswa.php"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Yakin ingin menghapus data?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
    <!-- End custom js for this page-->
</body>
</html>

<?php
$koneksi->close();
?>