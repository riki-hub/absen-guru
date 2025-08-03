<?php
require "../koneksi.php";
session_start();


if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'admin') {
    header("Location: ../");
    exit();
}
date_default_timezone_set('Asia/Jakarta');

// Mendapatkan tanggal hari ini
 // Mengambil tanggal dari form atau default ke bulan lalu
 $tanggal_hari_ini = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Query untuk mencari nama guru yang belum absen hari ini
$sql = "
    SELECT ag.nama 
    FROM akun_guru ag
    LEFT JOIN absen_msk am ON ag.nama = am.nama AND am.tanggal = ?
    WHERE am.nama IS NULL
";

// Menjalankan query
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $tanggal_hari_ini);
$stmt->execute();
$result = $stmt->get_result();

// Menyiapkan array hasil pencarian
$guru_names = [];
while ($row = $result->fetch_assoc()) {
    $guru_names[] = $row['nama'];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="../datables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Tambahkan di dalam tag <head> -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- endinject -->
    <!-- endinject -->
    <!-- plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="../css/style.css" />
    <!-- endinject -->
    <link rel="shortcut icon" href="../images/favicon.png" />
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
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">Tambah Guru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="needs-validation" action="proses/data_guru/absen.php" method="post" novalidate>
                    <div class="form-row">
                    <div class="col mb-3">
                <label for="nama">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" placeholder="Ketikan Nama Guru" oninput="searchGuru()" required autocomplete="off">
                <ul id="suggestions" class="suggestions-list"></ul>
                <div class="invalid-feedback">
                    Nama guru harus diisi.
                </div>
            </div>
                    </div>
                    <div class="form-row">
                        <div class="col mb-3">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="tanggal" required>
                            <div class="invalid-feedback">
                                Tanggal harus diisi.
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

            
            <!-- Dino -->
            <div class="main-panel">
                
                <div class="content-wrapper">
                <?php if($_SESSION['username'] == 'admin') : ?>
                    
                    <?php else :?>
                    <div class="content-wrapper">
                        <div class="page-header">
                            <h3 class="font-weight-bold">Data Absen</h3><br>
                            <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#exampleModalScrollable">
                                Tambah Absen
                            </button>

                            <!-- Filter Tanggal -->
                            <form method="get" action="">
                                <div class="form-group row">
                                
                                    <div class="col-sm-4">
                                        <?php
                                           
                                        ?>
                                        <input type="date" class="form-control" name="tanggal" id="filterTanggal" 
                                            value="<?php echo isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d'); ?>" 
                                            >
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>
                            
                        </div>
                    <?php endif;?>
                    <div class="page-header">
                        

                    </div>
                    <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Hadir</h6>
        
        <div class="d-flex">
        <a id="printLink" style="margin-top: 5px; margin-left: 5px;" href="#" target="_blank" class="btn btn-primary btn-icon-split btn-sm">
        <span class="icon text-white-55">
            <i class="fas fa-print"></i>
        </span>
        <span class="text">Cetak Data</span>
    </a>
        </div>
        
        
    </div>

    
<script>
   document.addEventListener('DOMContentLoaded', function() {
    var link = document.getElementById('printLink');
    
    // Dapatkan tanggal yang dipilih dari parameter URL
    var urlParams = new URLSearchParams(window.location.search);
    var tanggal = urlParams.get('tanggal'); // Ambil nilai tanggal dari parameter URL
    
    // Jika tanggal tidak ada, gunakan tanggal hari ini sebagai default
    if (!tanggal) {
        var today = new Date();
        tanggal = today.toISOString().split('T')[0]; // Format yyyy-mm-dd
    }

    // Ubah tanggal menjadi objek Date
    var selectedDate = new Date(tanggal);
    
    // Cek apakah hari tersebut adalah Jumat (5 = Jumat)
    var isFriday = selectedDate.getDay() === 5;

    // Tentukan URL berdasarkan apakah tanggal tersebut adalah hari Jumat atau bukan
    if (isFriday) {
        // Jika hari Jumat, arahkan ke hadir2.php
        link.href = 'proses/cetak/hadir2.php?tanggal=' + tanggal;
    } else {
        // Jika bukan hari Jumat, arahkan ke hadir.php
        link.href = 'proses/cetak/hadir.php?tanggal=' + tanggal;
    }
});

</script>

    <div class="card-body">
        <!-- Table Data Hadir -->
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead style="background-color:blue;color:aliceblue;">
                    <tr align="center">
                        <th style="background-color:blue;color:aliceblue;">No</th>
                        <th style="background-color:blue;color:aliceblue;">Nama Guru</th>
                        <th style="background-color:blue;color:aliceblue;">Tanggal</th>
                        <?php
                            // Menentukan jumlah kolom jam sesuai hari ini
                            $hariIni = date('N');
                            $jumlahJam = 11;
                            $kolomDitampilkan = ($hariIni == 5) ? 7 : $jumlahJam;

                            // Menampilkan kolom jam
                            for ($i = 1; $i < $kolomDitampilkan; $i++) {
                                echo '<th style="background-color:blue;color:aliceblue;">Jam ' . $i . '</th>';
                            }
                        ?>
                        
                        <th style="background-color:blue;color:aliceblue;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include('../koneksi.php');

                        // Mengambil tanggal dari form atau default ke bulan lalu
                        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

                        // Query untuk menampilkan data berdasarkan tanggal
                        $query = "SELECT * FROM absen_msk WHERE tanggal = '$tanggal'";
                        $result = mysqli_query($koneksi, $query);
                        if (!$result) {
                            die("query error: " . mysqli_error($koneksi));
                        }

                        $no = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $id = $row['id'];
                    ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $no; ?></td>
                            <td><?php echo $row['nama']; ?></td>
                            <td><?php echo $row['tanggal'] == '0000-00-00' ? 'Belum absen' : date('d-m-Y', strtotime($row['tanggal'])); ?></td>
                            <?php
                                // Mencetak data jam sesuai kolom yang ditampilkan
                                for ($i = 1; $i < $kolomDitampilkan; $i++) {
                                    $jamKey = 'jam' . $i;
                                    echo '<td style="text-align: center;">' . ($row[$jamKey]) . '</td>';
                                }
                            ?>
                          
                          <td style="text-align: center;"> <a class="btn btn-primary" href="tambahan.php?id=<?php echo $row['id']; ?>"><i class="fa fa-search" aria-hidden="true"></i></a>
                                                <?php if ($_SESSION['username'] != 'admin') : ?>
                                                    <a class="btn btn-warning" href="edit-jam.php?id=<?php echo $row['id']; ?>"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                                    <a class="btn btn-danger" href="proses/data_guru/hapus_absen.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
    <i class="fa fa-trash" aria-hidden="true"></i> 
</a>

                                                    
                                                <?php endif; ?>
                                                
                                            
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



                    <!-- modal edit jam -->
                   




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
                            fetch(`proses/hadir/hapus.php`, {
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
<script>
// Array untuk menampung data nama guru
const guruNames = <?php echo json_encode($guru_names); ?>;

// Fungsi pencarian dinamis
function searchGuru() {
    const input = document.getElementById("nama").value;
    const suggestions = document.getElementById("suggestions");

    // Clear previous suggestions
    suggestions.innerHTML = '';

    if (input.length > 0) {
        // Filter nama guru berdasarkan input
        const filteredNames = guruNames.filter(guru => guru.toLowerCase().includes(input.toLowerCase()));

        // Tampilkan nama guru yang cocok
        filteredNames.forEach(name => {
            const li = document.createElement("li");
            li.textContent = name;
            li.addEventListener("click", function() {
                document.getElementById("nama").value = name;
                suggestions.innerHTML = ''; // Hapus saran setelah dipilih
            });
            suggestions.appendChild(li);
        });
    }
}
</script>

<style>
    .suggestions-list {
        list-style-type: none;
        padding: 0;
        margin-top: 5px;
        border: 1px solid #ccc;
        max-height: 150px;
        overflow-y: auto;
    }

    .suggestions-list li {
        padding: 5px;
        cursor: pointer;
    }

    .suggestions-list li:hover {
        background-color: #f0f0f0;
    }
</style>
</html>