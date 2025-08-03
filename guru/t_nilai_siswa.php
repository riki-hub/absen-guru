<?php
session_start();
include '../koneksi.php';

// Pastikan user telah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href = 'login.php';</script>";
    exit;
}

// Tangani permintaan AJAX untuk autocompletion mata pelajaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mapel_query'])) {
    header('Content-Type: application/json');
    $query = trim($_POST['mapel_query']);
    $mapelList = [];

    if (strlen($query) > 0) {
        $searchTerm = $query . '%';
        $sql = "SELECT id, mata_pelajaran FROM pelajaran WHERE mata_pelajaran LIKE ? ORDER BY mata_pelajaran";
        $stmt = $koneksi->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('s', $searchTerm);
            $stmt->execute();
            $result = $stmt->get_result();
            $mapelList = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            foreach ($mapelList as &$mapel) {
                $mapel['mata_pelajaran'] = htmlspecialchars($mapel['mata_pelajaran']);
            }
        } else {
            error_log("Gagal menyiapkan kueri autocompletion: " . $koneksi->error);
            echo json_encode(['error' => 'Gagal memuat data mata pelajaran']);
            exit;
        }
    }
    
    echo json_encode($mapelList);
    exit;
}

// Proses simpan atau perbarui nilai via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_nilai') {
    header('Content-Type: application/json');
    $nilaiData = json_decode($_POST['nilai_data'], true);
    $mapelId = intval($_POST['mapel_id']);
    $kelasId = intval($_POST['kelas_id']);
    $tanggal = $_POST['tanggal'];
    $success = true;
    $error_message = '';

    if (!$nilaiData || !is_array($nilaiData)) {
        echo json_encode(['success' => false, 'message' => 'Data nilai tidak valid']);
        exit;
    }

    foreach ($nilaiData as $data) {
        $siswaId = intval($data['siswa_id']);
        $id = isset($data['id']) ? intval($data['id']) : 0;
        $nilai_1 = $data['nilai_1'] !== '' ? intval($data['nilai_1']) : null;
        $nilai_2 = $data['nilai_2'] !== '' ? intval($data['nilai_2']) : null;
        $nilai_3 = $data['nilai_3'] !== '' ? intval($data['nilai_3']) : null;
        $nilai_4 = $data['nilai_4'] !== '' ? intval($data['nilai_4']) : null;
        $nilai_5 = $data['nilai_5'] !== '' ? intval($data['nilai_5']) : null;

        // Cek apakah nilai sudah ada untuk siswa, mapel, dan kelas
        $sqlCheck = "SELECT id, tanggal FROM nilai_siswa WHERE id_siswa = ? AND id_mapel = ? AND id_kelas = ?";
        $stmtCheck = $koneksi->prepare($sqlCheck);
        if ($stmtCheck) {
            $stmtCheck->bind_param('iii', $siswaId, $mapelId, $kelasId);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();
            $existingRecord = $resultCheck->fetch_assoc();
            $stmtCheck->close();
        } else {
            $success = false;
            $error_message = 'Gagal menyiapkan kueri cek nilai: ' . $koneksi->error;
            break;
        }

        if ($existingRecord) {
            // Perbarui nilai yang sudah ada
            $sql = "UPDATE nilai_siswa SET nilai_1 = ?, nilai_2 = ?, nilai_3 = ?, nilai_4 = ?, nilai_5 = ?, tanggal = ? WHERE id = ?";
            $stmt = $koneksi->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('iiiiisi', $nilai_1, $nilai_2, $nilai_3, $nilai_4, $nilai_5, $tanggal, $existingRecord['id']);
                if (!$stmt->execute()) {
                    $success = false;
                    $error_message = 'Gagal memperbarui nilai: ' . $stmt->error;
                    $stmt->close();
                    break;
                }
                $stmt->close();
            } else {
                $success = false;
                $error_message = 'Gagal menyiapkan kueri update: ' . $koneksi->error;
                break;
            }
        } else {
            // Simpan nilai baru
            $sql = "INSERT INTO nilai_siswa (id_siswa, id_mapel, id_kelas, tanggal, nilai_1, nilai_2, nilai_3, nilai_4, nilai_5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $koneksi->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('iiisiiiii', $siswaId, $mapelId, $kelasId, $tanggal, $nilai_1, $nilai_2, $nilai_3, $nilai_4, $nilai_5);
                if (!$stmt->execute()) {
                    $success = false;
                    $error_message = 'Gagal menyimpan nilai: ' . $stmt->error;
                    $stmt->close();
                    break;
                }
                $stmt->close();
            } else {
                $success = false;
                $error_message = 'Gagal menyiapkan kueri insert: ' . $koneksi->error;
                break;
            }
        }
    }

    echo json_encode(['success' => $success, 'message' => $success ? 'Nilai berhasil disimpan!' : $error_message]);
    exit;
}

// Ambil data kelas untuk dropdown
$kelasOptions = $koneksi->query("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas");
if (!$kelasOptions) {
    error_log("Gagal mengambil data kelas: " . $koneksi->error);
}

// Proses pencarian data siswa dan nilai
$existingGrades = [];
$newStudents = [];
$searchPerformed = false;
$errorMessage = '';
$mapelName = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mapel']) && isset($_POST['kelas_id']) && isset($_POST['tanggal'])) {
    $searchPerformed = true;
    $mapel = trim($_POST['mapel']);
    $mapelId = isset($_POST['mapel_id']) ? intval($_POST['mapel_id']) : 0;
    $kelasId = intval($_POST['kelas_id']);
    $tanggal = $_POST['tanggal'];
    
    error_log("Pencarian POST diterima: mapel=$mapel, mapel_id=$mapelId, kelas_id=$kelasId, tanggal=$tanggal");

    if (!empty($mapel) && $kelasId > 0 && !empty($tanggal)) {
        // Cek apakah mata pelajaran sudah ada di database
        if ($mapelId == 0) {
            // Mata pelajaran diinput secara manual, cek apakah sudah ada
            $sqlCheckMapel = "SELECT id FROM pelajaran WHERE mata_pelajaran = ?";
            $stmtCheckMapel = $koneksi->prepare($sqlCheckMapel);
            if ($stmtCheckMapel) {
                $stmtCheckMapel->bind_param('s', $mapel);
                $stmtCheckMapel->execute();
                $resultCheckMapel = $stmtCheckMapel->get_result();
                if ($resultCheckMapel->num_rows > 0) {
                    $row = $resultCheckMapel->fetch_assoc();
                    $mapelId = $row['id'];
                } else {
                    // Tambahkan mata pelajaran baru
                    $sqlInsertMapel = "INSERT INTO pelajaran (mata_pelajaran) VALUES (?)";
                    $stmtInsertMapel = $koneksi->prepare($sqlInsertMapel);
                    if ($stmtInsertMapel) {
                        $stmtInsertMapel->bind_param('s', $mapel);
                        if ($stmtInsertMapel->execute()) {
                            $mapelId = $koneksi->insert_id;
                            error_log("Mata pelajaran baru ditambahkan: $mapel, ID: $mapelId");
                        } else {
                            $errorMessage = 'Gagal menambahkan mata pelajaran baru: ' . $stmtInsertMapel->error;
                            error_log($errorMessage);
                        }
                        $stmtInsertMapel->close();
                    } else {
                        $errorMessage = 'Gagal menyiapkan kueri insert mata pelajaran: ' . $koneksi->error;
                        error_log($errorMessage);
                    }
                }
                $stmtCheckMapel->close();
            } else {
                $errorMessage = 'Gagal menyiapkan kueri cek mata pelajaran: ' . $koneksi->error;
                error_log($errorMessage);
            }
        }

        // Simpan nama mata pelajaran untuk ditampilkan
        $mapelName = $mapel;

        if ($mapelId > 0) {
            // Ambil semua siswa di kelas yang dipilih
            $sqlSiswa = "SELECT id, nama_siswa FROM siswa WHERE kelas_id = ? ORDER BY nama_siswa";
            $stmtSiswa = $koneksi->prepare($sqlSiswa);
            if ($stmtSiswa) {
                $stmtSiswa->bind_param('i', $kelasId);
                $stmtSiswa->execute();
                $siswaResult = $stmtSiswa->get_result();
                $allStudents = $siswaResult->fetch_all(MYSQLI_ASSOC);
                $stmtSiswa->close();
            } else {
                $errorMessage = 'Gagal mengambil data siswa: ' . $koneksi->error;
                error_log($errorMessage);
            }

            // Ambil semua nilai untuk mata pelajaran dan kelas ini, tanpa memfilter tanggal
            $sqlExisting = "SELECT ns.id, ns.id_siswa, ns.tanggal, ns.nilai_1, ns.nilai_2, ns.nilai_3, ns.nilai_4, ns.nilai_5, s.nama_siswa 
                            FROM nilai_siswa ns 
                            JOIN siswa s ON ns.id_siswa = s.id 
                            WHERE ns.id_mapel = ? AND ns.id_kelas = ?
                            ORDER BY s.nama_siswa, ns.tanggal DESC";
            $stmtExisting = $koneksi->prepare($sqlExisting);
            if ($stmtExisting) {
                $stmtExisting->bind_param('ii', $mapelId, $kelasId);
                $stmtExisting->execute();
                $resultExisting = $stmtExisting->get_result();
                $existingGrades = $resultExisting->fetch_all(MYSQLI_ASSOC);
                $stmtExisting->close();
            } else {
                $errorMessage = 'Gagal mengambil data nilai: ' . $koneksi->error;
                error_log($errorMessage);
            }

            // Filter siswa yang belum memiliki nilai untuk mata pelajaran ini
            $existingSiswaIds = array_column($existingGrades, 'id_siswa');
            $newStudents = array_filter($allStudents, function($siswa) use ($existingSiswaIds) {
                return !in_array($siswa['id'], $existingSiswaIds);
            });
        } else if (empty($errorMessage)) {
            $errorMessage = 'Gagal mendapatkan ID mata pelajaran.';
            error_log($errorMessage);
        }
    } else {
        $errorMessage = 'Data input tidak valid. Pastikan mata pelajaran, kelas, dan tanggal diisi dengan benar.';
        error_log($errorMessage);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input dan Edit Nilai Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
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
        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }
        .modal-dialog {
            max-width: 90%;
        }
        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table input[type="number"] {
            width: 80px;
        }
        @media (max-width: 768px) {
            .container {
                padding: 12px;
            }
            .modal-dialog {
                max-width: 95%;
                margin: 10px auto;
            }
            .modal-body {
                padding: 10px;
                max-height: 80vh;
            }
            .modal-header, .modal-footer {
                padding: 8px 12px;
            }
            .modal-title {
                font-size: 1.1rem;
            }
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            .table {
                min-width: 600px;
            }
            .table th, .table td {
                font-size: 0.85rem;
                padding: 6px;
            }
            .table input[type="number"] {
                width: 60px;
                font-size: 0.85rem;
                padding: 4px;
            }
            .btn {
                font-size: 0.85rem;
                padding: 6px 12px;
            }
            .form-control, .form-select {
                font-size: 0.85rem;
                padding: 6px;
            }
            .form-label {
                font-size: 0.9rem;
            }
            .row > div {
                margin-bottom: 10px;
            }
            .modal-footer .btn {
                margin: 5px;
            }
        }
        @media (max-width: 576px) {
            .table th, .table td {
                font-size: 0.75rem;
                padding: 4px;
            }
            .table input[type="number"] {
                width: 50px;
                font-size: 0.75rem;
                padding: 3px;
            }
            .btn {
                font-size: 0.75rem;
                padding: 5px 10px;
            }
            .modal-title {
                font-size: 1rem;
            }
            .modal-footer {
                flex-wrap: wrap;
                justify-content: center;
            }
            .modal-footer .btn {
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-3">
            <a href="index.php" class="btn btn-danger">Keluar</a>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Input dan Edit Nilai Siswa</h4>
            </div>
            <div class="card-body">
                <form method="POST" id="searchForm" class="mb-4">
                    <div class="row">
                        <div class="col-md-4 mb-3 position-relative">
                            <label for="mapel" class="form-label">Mata Pelajaran</label>
                            <input type="text" class="form-control" id="mapel" name="mapel" placeholder="Masukkan nama mata pelajaran" autocomplete="off" value="<?php echo isset($_POST['mapel']) ? htmlspecialchars($_POST['mapel']) : ''; ?>">
                            <input type="hidden" id="mapel_id" name="mapel_id" value="<?php echo isset($_POST['mapel_id']) ? htmlspecialchars($_POST['mapel_id']) : ''; ?>">
                            <ul id="mapelSuggestions" class="autocomplete-suggestions" style="display: none;"></ul>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select class="form-select" id="kelas_id" name="kelas_id" required>
                                <option value="">Pilih Kelas...</option>
                                <?php 
                                if ($kelasOptions) {
                                    $kelasOptions->data_seek(0);
                                    while ($row = $kelasOptions->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>" <?php echo isset($_POST['kelas_id']) && $_POST['kelas_id'] == $row['id'] ? 'selected' : ''; ?>">
                                            <?php echo htmlspecialchars($row['nama_kelas']); ?>
                                        </option>
                                    <?php endwhile;
                                } else { ?>
                                    <option value="">Tidak ada kelas tersedia</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?php echo isset($_POST['tanggal']) ? htmlspecialchars($_POST['tanggal']) : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="searchBtn">Cari</button>
                </form>

                <?php if ($searchPerformed && $errorMessage): ?>
                    <div class="error-message"><?php echo htmlspecialchars($errorMessage); ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tabel dan Tombol Download -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-labelledby="nilaiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nilaiModalLabel">Data Nilai Siswa - <?php echo htmlspecialchars($mapelName); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if ($searchPerformed && empty($errorMessage)): ?>
                        <?php if (!empty($existingGrades)): ?>
                            <div class="table-responsive mt-4">
                                <h5>Siswa dengan Nilai Terekam</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>Nilai 1</th>
                                            <th>Nilai 2</th>
                                            <th>Nilai 3</th>
                                            <th>Nilai 4</th>
                                            <th>Nilai 5</th>
                                        </tr>
                                    </thead>
                                    <tbody id="existingTableBody">
                                        <?php 
                                        $processedSiswa = [];
                                        foreach ($existingGrades as $row):
                                            if (!in_array($row['id_siswa'], $processedSiswa)):
                                                $processedSiswa[] = $row['id_siswa'];
                                        ?>
                                                <tr>
                                                    <td>
                                                        <?php echo htmlspecialchars($row['nama_siswa']); ?>
                                                        <input type="hidden" name="id_<?php echo $row['id']; ?>" value="<?php echo $row['id']; ?>">
                                                        <input type="hidden" name="siswa_id_<?php echo $row['id']; ?>" value="<?php echo $row['id_siswa']; ?>">
                                                    </td>
                                                    <td><input type="number" name="nilai1_<?php echo $row['id']; ?>" class="form-control" min="0" max="100" value="<?php echo $row['nilai_1'] !== null ? htmlspecialchars($row['nilai_1']) : ''; ?>"></td>
                                                    <td><input type="number" name="nilai2_<?php echo $row['id']; ?>" class="form-control" min="0" max="100" value="<?php echo $row['nilai_2'] !== null ? htmlspecialchars($row['nilai_2']) : ''; ?>"></td>
                                                    <td><input type="number" name="nilai3_<?php echo $row['id']; ?>" class="form-control" min="0" max="100" value="<?php echo $row['nilai_3'] !== null ? htmlspecialchars($row['nilai_3']) : ''; ?>"></td>
                                                    <td><input type="number" name="nilai4_<?php echo $row['id']; ?>" class="form-control" min="0" max="100" value="<?php echo $row['nilai_4'] !== null ? htmlspecialchars($row['nilai_4']) : ''; ?>"></td>
                                                    <td><input type="number" name="nilai5_<?php echo $row['id']; ?>" class="form-control" min="0" max="100" value="<?php echo $row['nilai_5'] !== null ? htmlspecialchars($row['nilai_5']) : ''; ?>"></td>
                                                </tr>
                                        <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($newStudents)): ?>
                            <div class="table-responsive mt-4">
                                <h5>Siswa Belum Memiliki Nilai</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nama Siswa</th>
                                            <th>Nilai 1</th>
                                            <th>Nilai 2</th>
                                            <th>Nilai 3</th>
                                            <th>Nilai 4</th>
                                            <th>Nilai 5</th>
                                        </tr>
                                    </thead>
                                    <tbody id="newTableBody">
                                        <?php foreach ($newStudents as $siswa): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($siswa['nama_siswa']); ?>
                                                    <input type="hidden" name="siswa_id_<?php echo $siswa['id']; ?>" value="<?php echo $siswa['id']; ?>">
                                                </td>
                                                <td><input type="number" name="nilai1_<?php echo $siswa['id']; ?>" class="form-control" min="0" max="100"></td>
                                                <td><input type="number" name="nilai2_<?php echo $siswa['id']; ?>" class="form-control" min="0" max="100"></td>
                                                <td><input type="number" name="nilai3_<?php echo $siswa['id']; ?>" class="form-control" min="0" max="100"></td>
                                                <td><input type="number" name="nilai4_<?php echo $siswa['id']; ?>" class="form-control" min="0" max="100"></td>
                                                <td><input type="number" name="nilai5_<?php echo $siswa['id']; ?>" class="form-control" min="0" max="100"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if (empty($existingGrades) && empty($newStudents) && empty($errorMessage)): ?>
                            <div class="alert alert-info mt-4">
                                Tidak ada siswa ditemukan untuk kelas ini atau kombinasi mata pelajaran dan kelas yang dipilih.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" id="downloadBtn" class="btn btn-success">Download</button>
                    <button type="button" id="saveNilaiBtn" class="btn btn-primary">Simpan Perubahan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM telah dimuat');

            const mapelInput = document.getElementById('mapel');
            const mapelSuggestions = document.getElementById('mapelSuggestions');
            const mapelIdInput = document.getElementById('mapel_id');
            const searchForm = document.getElementById('searchForm');
            const kelasIdInput = document.getElementById('kelas_id');
            const tanggalInput = document.getElementById('tanggal');
            const searchBtn = document.getElementById('searchBtn');
            const nilaiModal = new bootstrap.Modal(document.getElementById('nilaiModal'));

            // Periksa elemen penting
            if (!mapelInput || !mapelSuggestions || !mapelIdInput || !searchForm || !kelasIdInput || !tanggalInput || !searchBtn) {
                console.error('Elemen formulir tidak ditemukan:', {
                    mapelInput: !!mapelInput,
                    mapelSuggestions: !!mapelSuggestions,
                    mapelIdInput: !!mapelIdInput,
                    searchForm: !!searchForm,
                    kelasIdInput: !!kelasIdInput,
                    tanggalInput: !!tanggalInput,
                    searchBtn: !!searchBtn
                });
                Swal.fire('Error', 'Elemen formulir tidak ditemukan. Silakan periksa konsol browser.', 'error');
                return;
            }

            // Autocomplete untuk Mata Pelajaran
            mapelInput.addEventListener('input', function() {
                const query = this.value.trim();
                console.log('Input mata pelajaran berubah:', query);
                if (query.length === 0) {
                    mapelSuggestions.style.display = 'none';
                    mapelSuggestions.innerHTML = '';
                    mapelIdInput.value = '';
                    return;
                }

                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'mapel_query=' + encodeURIComponent(query)
                })
                .then(response => {
                    console.log('Status respons autocompletion:', response.status);
                    if (!response.ok) {
                        throw new Error('Gagal memuat saran: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    mapelSuggestions.innerHTML = '';
                    if (data.error) {
                        console.error('Kesalahan server:', data.error);
                        Swal.fire('Error', 'Gagal memuat saran mata pelajaran: ' + data.error, 'error');
                        return;
                    }
                    if (data.length > 0) {
                        data.forEach(mapel => {
                            const li = document.createElement('li');
                            li.textContent = mapel.mata_pelajaran;
                            li.addEventListener('click', function() {
                                mapelInput.value = mapel.mata_pelajaran;
                                mapelIdInput.value = mapel.id;
                                mapelSuggestions.style.display = 'none';
                                mapelSuggestions.innerHTML = '';
                                console.log('Mata pelajaran dipilih:', mapel.mata_pelajaran, mapel.id);
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
                    console.error('Kesalahan autocompletion:', error);
                    Swal.fire('Error', 'Gagal memuat saran mata pelajaran: ' + error.message, 'error');
                });
            });

            // Sembunyikan saran saat klik di luar
            document.addEventListener('click', function(e) {
                if (!mapelInput.contains(e.target) && !mapelSuggestions.contains(e.target)) {
                    mapelSuggestions.style.display = 'none';
                    mapelSuggestions.innerHTML = '';
                }
            });

            // Tangani pengiriman form
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                console.log('Form submission dipicu');

                const mapel = mapelInput.value.trim();
                const mapelId = mapelIdInput.value;
                const kelasId = kelasIdInput.value;
                const tanggal = tanggalInput.value;

                console.log('Data formulir:', { mapel, mapelId, kelasId, tanggal });

                if (!mapel || !kelasId || !tanggal) {
                    console.log('Validasi gagal: Input tidak lengkap');
                    Swal.fire('Peringatan', 'Silakan lengkapi semua pilihan (mata pelajaran, kelas, dan tanggal).', 'warning');
                    return;
                }

                // Kirimkan data secara manual
                const formData = `mapel=${encodeURIComponent(mapel)}&mapel_id=${encodeURIComponent(mapelId)}&kelas_id=${encodeURIComponent(kelasId)}&tanggal=${encodeURIComponent(tanggal)}`;
                console.log('Mengirim data:', formData);
                fetch(window.location.href, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData
                })
                .then(response => {
                    console.log('Status respons pencarian:', response.status);
                    if (!response.ok) {
                        throw new Error('Gagal memuat data: ' + response.statusText);
                    }
                    return response.text();
                })
                .then(html => {
                    console.log('Respons HTML diterima');
                    // Parse HTML untuk mendapatkan isi modal
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const modalBody = doc.querySelector('.modal-body')?.innerHTML || '';
                    const modalTitle = doc.querySelector('.modal-title')?.innerHTML || 'Data Nilai Siswa';
                    const errorMessage = doc.querySelector('.error-message')?.innerHTML || '';

                    // Perbarui isi modal
                    const modalBodyElement = document.querySelector('.modal-body');
                    const modalTitleElement = document.querySelector('.modal-title');
                    if (modalBodyElement && modalTitleElement) {
                        modalBodyElement.innerHTML = modalBody;
                        modalTitleElement.innerHTML = modalTitle;
                    } else {
                        console.error('Elemen modal-body atau modal-title tidak ditemukan');
                        Swal.fire('Error', 'Elemen modal tidak ditemukan di halaman.', 'error');
                        return;
                    }

                    // Perbarui tombol di footer jika ada data
                    const saveBtn = document.querySelector('#saveNilaiBtn');
                    const downloadBtn = document.querySelector('#downloadBtn');
                    if (modalBody.includes('table') && saveBtn && downloadBtn) {
                        saveBtn.style.display = 'inline-block';
                        downloadBtn.style.display = 'inline-block';
                    } else {
                        if (saveBtn) saveBtn.style.display = 'none';
                        if (downloadBtn) downloadBtn.style.display = 'none';
                    }

                    // Tampilkan modal jika ada data tabel atau pesan info
                    if (errorMessage) {
                        console.log('Pesan error ditemukan:', errorMessage);
                        Swal.fire('Error', errorMessage, 'error');
                    } else if (modalBody.includes('table') || modalBody.includes('alert-info')) {
                        console.log('Menampilkan modal karena ada data tabel atau pesan info');
                        nilaiModal.show();
                    } else {
                        console.log('Tidak ada data tabel atau pesan info untuk ditampilkan');
                        Swal.fire('Info', 'Tidak ada data untuk ditampilkan.', 'info');
                    }
                })
                .catch(error => {
                    console.error('Kesalahan pencarian:', error);
                    Swal.fire('Error', 'Gagal memuat data siswa: ' + error.message, 'error');
                });
            });

            // Pastikan tombol Cari dapat diklik
            searchBtn.addEventListener('click', function(e) {
                console.log('Tombol Cari diklik');
                e.preventDefault();
                console.log('Memicu pengiriman formulir');
                searchForm.dispatchEvent(new Event('submit'));
            });

            // Simpan perubahan nilai
            document.getElementById('saveNilaiBtn')?.addEventListener('click', function() {
                console.log('Tombol simpan diklik');
                const mapelId = mapelIdInput.value;
                const kelasId = kelasIdInput.value;
                const tanggal = tanggalInput.value;
                const mapel = mapelInput.value.trim();
                const existingTableBody = document.getElementById('existingTableBody');
                const newTableBody = document.getElementById('newTableBody');
                const nilaiData = [];

                console.log('Data simpan:', { mapelId, kelasId, tanggal, mapel });

                // Validasi mapel_id sebelum menyimpan
                if (!mapelId && mapel) {
                    // Jika mapel_id kosong, coba cari id dari nama mata pelajaran
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'mapel_query=' + encodeURIComponent(mapel)
                    })
                    .then(response => response.json())
                    .then(data => {
                        const mapelMatch = data.find(m => m.mata_pelajaran.toLowerCase() === mapel.toLowerCase());
                        if (mapelMatch) {
                            mapelIdInput.value = mapelMatch.id;
                            proceedWithSave(mapelMatch.id);
                        } else {
                            // Kirim ke server untuk menambahkan mata pelajaran baru
                            fetch(window.location.href, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `mapel=${encodeURIComponent(mapel)}&kelas_id=${encodeURIComponent(kelasId)}&tanggal=${encodeURIComponent(tanggal)}`
                            })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const modalBody = doc.querySelector('.modal-body')?.innerHTML || '';
                                const modalTitle = doc.querySelector('.modal-title')?.innerHTML || 'Data Nilai Siswa';
                                const errorMessage = doc.querySelector('.error-message')?.innerHTML || '';

                                if (errorMessage) {
                                    Swal.fire('Error', errorMessage, 'error');
                                    return;
                                }

                                // Perbarui mapel_id dari respons server
                                const newMapelId = doc.querySelector('#mapel_id')?.value;
                                if (newMapelId) {
                                    mapelIdInput.value = newMapelId;
                                    proceedWithSave(newMapelId);
                                } else {
                                    Swal.fire('Error', 'Gagal mendapatkan ID mata pelajaran baru.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Kesalahan menambahkan mata pelajaran:', error);
                                Swal.fire('Error', 'Gagal menambahkan mata pelajaran: ' + error.message, 'error');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Kesalahan validasi mapel:', error);
                        Swal.fire('Error', 'Gagal memvalidasi mata pelajaran: ' + error.message, 'error');
                    });
                } else {
                    proceedWithSave(mapelId);
                }

                function proceedWithSave(mapelId) {
                    // Ambil data dari tabel siswa dengan nilai terekam
                    if (existingTableBody) {
                        const existingRows = existingTableBody.querySelectorAll('tr');
                        existingRows.forEach(row => {
                            const id = row.querySelector('input[type="hidden"][name^="id_"]').value;
                            const siswaId = row.querySelector('input[type="hidden"][name^="siswa_id_"]').value;
                            const nilai1 = row.querySelector(`input[name="nilai1_${id}"]`).value;
                            const nilai2 = row.querySelector(`input[name="nilai2_${id}"]`).value;
                            const nilai3 = row.querySelector(`input[name="nilai3_${id}"]`).value;
                            const nilai4 = row.querySelector(`input[name="nilai4_${id}"]`).value;
                            const nilai5 = row.querySelector(`input[name="nilai5_${id}"]`).value;

                            nilaiData.push({
                                id: id,
                                siswa_id: siswaId,
                                nilai_1: nilai1,
                                nilai_2: nilai2,
                                nilai_3: nilai3,
                                nilai_4: nilai4,
                                nilai_5: nilai5
                            });
                        });
                    }

                    // Ambil data dari tabel siswa tanpa nilai
                    if (newTableBody) {
                        const newRows = newTableBody.querySelectorAll('tr');
                        newRows.forEach(row => {
                            const siswaId = row.querySelector('input[type="hidden"]').value;
                            const nilai1 = row.querySelector(`input[name="nilai1_${siswaId}"]`).value;
                            const nilai2 = row.querySelector(`input[name="nilai2_${siswaId}"]`).value;
                            const nilai3 = row.querySelector(`input[name="nilai3_${siswaId}"]`).value;
                            const nilai4 = row.querySelector(`input[name="nilai4_${siswaId}"]`).value;
                            const nilai5 = row.querySelector(`input[name="nilai5_${siswaId}"]`).value;

                            nilaiData.push({
                                siswa_id: siswaId,
                                nilai_1: nilai1,
                                nilai_2: nilai2,
                                nilai_3: nilai3,
                                nilai_4: nilai4,
                                nilai_5: nilai5
                            });
                        });
                    }

                    console.log('Mengirim data nilai:', nilaiData);

                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=save_nilai&mapel_id=${encodeURIComponent(mapelId)}&kelas_id=${encodeURIComponent(kelasId)}&tanggal=${encodeURIComponent(tanggal)}&nilai_data=${encodeURIComponent(JSON.stringify(nilaiData))}`
                    })
                    .then(response => {
                        console.log('Status respons simpan:', response.status);
                        if (!response.ok) {
                            throw new Error('Gagal menyimpan nilai: ' + response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Respons simpan:', data);
                        if (data.success) {
                            Swal.fire('Sukses', 'Nilai berhasil disimpan!', 'success').then(() => {
                                searchForm.submit();
                            });
                        } else {
                            Swal.fire('Error', 'Gagal menyimpan nilai: ' + (data.message || 'Terjadi kesalahan'), 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Kesalahan simpan:', error);
                        Swal.fire('Error', 'Gagal menyimpan nilai: ' + error.message, 'error');
                    });
                }
            });

            // Fungsionalitas download
            document.getElementById('downloadBtn')?.addEventListener('click', function() {
                console.log('Tombol download diklik');
                const mapelId = mapelIdInput.value;
                const kelasId = kelasIdInput.value;
                const tanggal = tanggalInput.value;
                const mapel = mapelInput.value.trim();

                if (!mapel || !kelasId || !tanggal) {
                    Swal.fire('Peringatan', 'Data formulir tidak lengkap untuk ekspor.', 'warning');
                    return;
                }

                // Validasi mapel_id untuk download
                if (!mapelId && mapel) {
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'mapel_query=' + encodeURIComponent(mapel)
                    })
                    .then(response => response.json())
                    .then(data => {
                        const mapelMatch = data.find(m => m.mata_pelajaran.toLowerCase() === mapel.toLowerCase());
                        if (mapelMatch) {
                            mapelIdInput.value = mapelMatch.id;
                            window.location.href = `proses/download2/excel.php?tanggal=${encodeURIComponent(tanggal)}&kelas_id=${encodeURIComponent(kelasId)}&mapel_id=${encodeURIComponent(mapelMatch.id)}`;
                        } else {
                            // Kirim ke server untuk menambahkan mata pelajaran baru
                            fetch(window.location.href, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                body: `mapel=${encodeURIComponent(mapel)}&kelas_id=${encodeURIComponent(kelasId)}&tanggal=${encodeURIComponent(tanggal)}`
                            })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newMapelId = doc.querySelector('#mapel_id')?.value;
                                if (newMapelId) {
                                    mapelIdInput.value = newMapelId;
                                    window.location.href = `proses/download2/excel.php?tanggal=${encodeURIComponent(tanggal)}&kelas_id=${encodeURIComponent(kelasId)}&mapel_id=${encodeURIComponent(newMapelId)}`;
                                } else {
                                    Swal.fire('Error', 'Gagal mendapatkan ID mata pelajaran untuk download.', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Kesalahan menambahkan mata pelajaran untuk download:', error);
                                Swal.fire('Error', 'Gagal memvalidasi mata pelajaran: ' + error.message, 'error');
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Kesalahan validasi mapel untuk download:', error);
                        Swal.fire('Error', 'Gagal memvalidasi mata pelajaran: ' + error.message, 'error');
                    });
                } else {
                    console.log('Menginisiasi ekspor Excel:', { mapelId, kelasId, tanggal });
                    window.location.href = `proses/download2/excel.php?tanggal=${encodeURIComponent(tanggal)}&kelas_id=${encodeURIComponent(kelasId)}&mapel_id=${encodeURIComponent(mapelId)}`;
                }
            });

            // Tampilkan modal jika pencarian telah dilakukan
            <?php if ($searchPerformed && empty($errorMessage) && (!empty($existingGrades) || !empty($newStudents))): ?>
                nilaiModal.show();
            <?php endif; ?>
        });
    </script>
</body>
</html>