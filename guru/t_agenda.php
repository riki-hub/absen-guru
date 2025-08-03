<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href = 'login.php';</script>";
    exit;
}

// Tangani permintaan AJAX untuk autocompletion mata pelajaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['query'])) {
    header('Content-Type: application/json');
    $query = trim($_POST['query']);
    $pelajaranList = [];

    if (strlen($query) > 0) {
        $searchTerm = $query . '%';
        $sql = "SELECT id, mata_pelajaran FROM pelajaran WHERE mata_pelajaran LIKE ? ORDER BY mata_pelajaran";
        $stmt = $koneksi->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('s', $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $pelajaranList = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            // Escape mata_pelajaran untuk mencegah XSS
            foreach ($pelajaranList as &$pelajaran) {
                $pelajaran['mata_pelajaran'] = htmlspecialchars($pelajaran['mata_pelajaran']);
            }
        } else {
            // Log error jika prepare statement gagal
            error_log("Prepare statement failed: " . $koneksi->error);
        }
    }
    
    echo json_encode($pelajaranList);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/favicon.png" />
    <style>
        .autocomplete-suggestions {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            border-radius: 4px;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .autocomplete-suggestions li {
            padding: 8px 12px;
            cursor: pointer;
        }
        .autocomplete-suggestions li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-3">
            <a href="index.php" class="btn btn-danger">Keluar</a>
        </div>

        <form action="upload_materi.php" method="POST" id="mainForm">
            <div class="card">
                <div class="card-header">
                    <h4>Input Absensi</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="tanggal_upload" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div class="form-group mb-3 position-relative">
                        <label>Mata Pelajaran</label>
                        <input type="text" class="form-control" id="mapel" name="mapel" placeholder="Masukkan nama mata pelajaran" autocomplete="off" required>
                        <input type="hidden" id="mapel_id" name="mapel_id">
                        <ul id="mapelSuggestions" class="autocomplete-suggestions" style="display: none;"></ul>
                    </div>

                    <div class="form-group mb-3">
                        <label>Pilih Kelas</label>
                        <select class="form-control" id="kelas" name="kelas" required>
                            <option value="">Pilih...</option>
                           <?php
                                $query = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
                                $result = $koneksi->query($query);
                                while ($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama_kelas']) . '</option>';
                                }
                                ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <button type="button" id="searchBtn" class="btn btn-secondary">Search</button>
                    </div>

                    <div id="siswaTable" class="table-responsive" style="display: none;">
                        <h5>Daftar Absensi Siswa</h5>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Hadir</th>
                                    <th>Sakit</th>
                                    <th>Izin</th>
                                    <th>Alpa</th>
                                </tr>
                            </thead>
                            <tbody id="siswaTableBody"></tbody>
                        </table>
                    </div>

                    <div id="noSiswaMessage" class="alert alert-info" style="display: none;">
                        Tidak ada siswa ditemukan untuk kelas ini.
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">Kirim</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX untuk Tombol Search (Siswa)
        document.getElementById('searchBtn').addEventListener('click', function() {
            const kelasId = document.getElementById('kelas').value;
            const siswaTable = document.getElementById('siswaTable');
            const siswaTableBody = document.getElementById('siswaTableBody');
            const noSiswaMessage = document.getElementById('noSiswaMessage');

            if (!kelasId) {
                alert('Silakan pilih kelas terlebih dahulu.');
                return;
            }

            fetch('get_siswa.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'kelas_id=' + encodeURIComponent(kelasId)
            })
            .then(response => response.json())
            .then(data => {
                siswaTableBody.innerHTML = '';
                siswaTable.style.display = 'none';
                noSiswaMessage.style.display = 'none';

                if (data.length > 0) {
                    data.forEach(siswa => {
                        // Tabel Absensi
                        const absensiRow = `
                            <tr>
                                <td>${siswa.nama_siswa}</td>
                                <td><input type="radio" name="status_${siswa.id}" value="hadir" checked></td>
                                <td><input type="radio" name="status_${siswa.id}" value="sakit"></td>
                                <td><input type="radio" name="status_${siswa.id}" value="izin"></td>
                                <td><input type="radio" name="status_${siswa.id}" value="alpa"></td>
                            </tr>`;
                        siswaTableBody.innerHTML += absensiRow;
                    });
                    siswaTable.style.display = 'block';
                } else {
                    noSiswaMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data siswa. Silakan coba lagi.');
            });
        });

        // Autocomplete untuk Mata Pelajaran
        const mapelInput = document.getElementById('mapel');
        const mapelSuggestions = document.getElementById('mapelSuggestions');
        const mapelIdInput = document.getElementById('mapel_id');

        mapelInput.addEventListener('input', function() {
            const query = this.value.trim();
            if (query.length === 0) {
                mapelSuggestions.style.display = 'none';
                mapelSuggestions.innerHTML = '';
                mapelIdInput.value = '';
                return;
            }

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'query=' + encodeURIComponent(query)
            })
            .then(response => response.json())
            .then(data => {
                mapelSuggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(pelajaran => {
                        const li = document.createElement('li');
                        li.textContent = pelajaran.mata_pelajaran;
                        li.addEventListener('click', function() {
                            mapelInput.value = pelajaran.mata_pelajaran;
                            mapelIdInput.value = pelajaran.id;
                            mapelSuggestions.style.display = 'none';
                            mapelSuggestions.innerHTML = '';
                        });
                        mapelSuggestions.appendChild(li);
                    });
                    mapelSuggestions.style.display = 'block';
                } else {
                    mapelSuggestions.style.display = 'none';
                    mapelIdInput.value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mapelSuggestions.style.display = 'none';
                mapelIdInput.value = '';
            });
        });

        // Sembunyikan saran saat klik di luar
        document.addEventListener('click', function(e) {
            if (!mapelInput.contains(e.target) && !mapelSuggestions.contains(e.target)) {
                mapelSuggestions.style.display = 'none';
                mapelSuggestions.innerHTML = '';
            }
        });

        // Validasi form sebelum submit
        document.getElementById('mainForm').addEventListener('submit', function(e) {
            const kelasId = document.getElementById('kelas').value;
            const mapelId = document.getElementById('mapel_id').value;
            const siswaTableBody = document.getElementById('siswaTableBody');
            const rows = siswaTableBody.querySelectorAll('tr');

            if (!kelasId) {
                e.preventDefault();
                alert('Silakan pilih kelas terlebih dahulu.');
                return;
            }

            if (!mapelId) {
                e.preventDefault();
                alert('Silakan pilih mata pelajaran terlebih dahulu.');
                return;
            }

            if (rows.length === 0) {
                e.preventDefault();
                alert('Silakan klik tombol Search untuk memuat data siswa dan absensi.');
                return;
            }

            // Validasi bahwa setiap siswa memiliki status absensi yang dipilih
            let allStatusSelected = true;
            rows.forEach(row => {
                const siswaId = row.querySelector('input[type="radio"]').name.split('_')[1];
                const status = document.querySelector(`input[name="status_${siswaId}"]:checked`);
                if (!status) {
                    allStatusSelected = false;
                }
            });

            if (!allStatusSelected) {
                e.preventDefault();
                alert('Silakan pilih status absensi untuk setiap siswa.');
            }
        });
    </script>
</body>
</html>