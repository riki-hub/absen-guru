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
            error_log("Prepare statement failed: " . $koneksi->error);
        }
    }
    
    echo json_encode($pelajaranList);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Hadir</title>
    <link rel="shortcut icon" href="../images/favicon.png" />
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }
        .modal.show {
            opacity: 1;
        }
        .modal-content {
            background-color: white;
            padding: 16px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: scale(0.7) translateY(-100px);
            transition: transform 0.4s ease-in-out, translateY 0.4s ease-in-out;
        }
        .modal.show .modal-content {
            transform: scale(1) translateY(0);
        }
        .modal-header {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }
        .modal-nama-siswa {
            font-size: 0.875rem;
            color: #4b5563;
            margin-top: 4px;
        }
        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .modal-body label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            color: #374151;
        }
        .modal-body input[type="checkbox"] {
            width: 16px;
            height: 16px;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 6px;
            margin-top: 12px;
        }
        #dataHadir {
            display: none;
            width: 100%;
            overflow-x: auto;
        }
        #dataHadir.show {
            display: block;
        }
        #dataHadir table {
            width: 100%;
            min-width: 600px;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        #tableHeader th {
            background: linear-gradient(45deg, #1e3a8a, #3b82f6);
            color: white;
            padding: 10px;
            border: 1px solid #d1d5db;
            transition: background 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            font-weight: 600;
            font-size: 0.875rem;
        }
        #tableHeader th:hover {
            background: linear-gradient(45deg, #1e40af, #60a5fa);
        }
        #tableBody td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: center;
            font-size: 0.75rem;
            color: #374151;
        }
        #tableBody tr {
            transition: background-color 0.2s ease;
        }
        #tableBody tr:hover {
            background-color: #f3f4f6;
        }
        #tableBody td.text-left {
            text-align: left;
        }
        .logout-btn, .export-btn, .edit-btn {
            transition: all 0.3s ease;
        }
        .logout-btn:hover {
            transform: scale(1.1);
            background-color: #ef4444;
            color: white;
        }
        .logout-btn:active {
            transform: scale(0.95);
        }
        .export-btn:hover {
            transform: scale(1.1);
            background-color: #10b981;
            color: white;
        }
        .export-btn:active {
            transform: scale(0.95);
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            min-width: 28px;
        }
        .badge:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .badge-hadir {
            background-color: #28a745;
            color: white;
        }
        .badge-sakit {
            background-color: #ffc107;
            color: #1f2937;
        }
        .badge-izin {
            background-color: #007bff;
            color: white;
        }
        .badge-alpa {
            background-color: #dc3545;
            color: white;
        }
        .badge-hadir:where(:not(:has(*))) {
            background-color: #d4e9d6;
            color: #155724;
        }
        .badge-sakit:where(:not(:has(*))) {
            background-color: #ffebb3;
            color: #664d03;
        }
        .badge-izin:where(:not(:has(*))) {
            background-color: #cce5ff;
            color: #004085;
        }
        .badge-alpa:where(:not(:has(*))) {
            background-color: #f8d7da;
            color: #721c24;
        }
        .edit-btn {
            background-color: #22d3ee;
            color: white;
            padding: 5px 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #0891b2;
            position: relative;
            overflow: hidden;
        }
        .edit-btn::before {
            content: '✏️';
            font-size: 0.75rem;
        }
        .edit-btn:hover {
            transform: scale(1.15);
            background-color: #06b6d4;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-color: #0e7490;
        }
        .edit-btn:active {
            transform: scale(0.95);
            background-color: #155e75;
        }
        .edit-btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
        }
        .edit-btn:hover::after {
            width: 150px;
            height: 150px;
        }
        .autocomplete-suggestions {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            max-height: 150px;
            overflow-y: auto;
            width: 100%;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .autocomplete-suggestions li {
            padding: 6px 10px;
            cursor: pointer;
            font-size: 0.875rem;
        }
        .autocomplete-suggestions li:hover {
            background-color: #f3f4f6;
        }
        @media (max-width: 640px) {
            .container {
                padding: 12px;
            }
            .grid-cols-1.md\:grid-cols-4 {
                grid-template-columns: 1fr;
            }
            .flex.items-end.gap-2 {
                flex-direction: column;
                gap: 8px;
                align-items: stretch;
            }
            .modal-content {
                width: 95%;
                padding: 12px;
            }
            .modal-title {
                font-size: 1.125rem;
            }
            .modal-nama-siswa {
                font-size: 0.75rem;
            }
            .modal-body label {
                font-size: 0.75rem;
            }
            .modal-body input[type="checkbox"] {
                width: 14px;
                height: 14px;
            }
            .modal-footer button {
                padding: 6px 12px;
                font-size: 0.75rem;
            }
            #tableHeader th {
                font-size: 0.75rem;
                padding: 8px;
            }
            #tableBody td {
                font-size: 0.7rem;
                padding: 6px;
            }
            .edit-btn {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
            .edit-btn::before {
                font-size: 0.7rem;
            }
            .badge {
                padding: 3px 8px;
                font-size: 0.7rem;
                min-width: 24px;
            }
            nav .container {
                flex-direction: column;
                gap: 8px;
                align-items: flex-start;
            }
            .logout-btn {
                padding: 6px 12px;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
<nav class="text-white p-4">
    <div class="flex justify-end items-center w-full">
        <a href="index.php" 
           class="logout-btn px-4 py-2 bg-red-500 rounded-lg font-semibold text-white hover:shadow-md" 
           onclick="confirmLogout(event)">Keluar
        </a>
    </div>
</nav>

    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold mb-4">Data Hadir</h2>
        <form method="POST" id="absensiForm" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                 <div class="relative">
                    <label for="mapel" class="block text-sm font-medium text-gray-700">Mata Pelajaran:</label>
                    <input type="text" id="mapel" name="mapel" placeholder="Masukkan nama mata pelajaran" class="mt-1 p-2 border rounded w-full" autocomplete="off">
                    <input type="hidden" id="mapel_id" name="mapel_id">
                    <ul id="mapelSuggestions" class="autocomplete-suggestions" style="display: none;"></ul>
                </div>
                 <div>
                    <label for="kelas_id" class="block text-sm font-medium text-gray-700">Nama Kelas:</label>
                    <select name="kelas_id" id="kelas_id" class="mt-1 p-2 border rounded w-full" required>
                        <option value="">Pilih Kelas</option>
                        <?php
                            include('../koneksi.php');
                            $queryKelas = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
                            $resultKelas = mysqli_query($koneksi, $queryKelas);
                            $selectedKelasId = isset($_POST['kelas_id']) ? $_POST['kelas_id'] : (isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '');
                            while ($rowKelas = mysqli_fetch_assoc($resultKelas)) {
                                $selected = ($selectedKelasId == $rowKelas['id']) ? 'selected' : '';
                                echo "<option value='{$rowKelas['id']}' $selected>{$rowKelas['nama_kelas']}</option>";
                            }
                            mysqli_close($koneksi);
                            ?>
                    </select>
                </div>
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700">Tanggal Awal:</label>
                    <input type="date" name="startDate" id="startDate" value="<?php echo isset($_POST['startDate']) ? htmlspecialchars($_POST['startDate']) : (isset($_GET['startDate']) ? htmlspecialchars($_GET['startDate']) : date('Y-m-d')); ?>" class="mt-1 p-2 border rounded w-full">
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700">Tanggal Akhir:</label>
                    <input type="date" name="endDate" id="endDate" value="<?php echo isset($_POST['endDate']) ? htmlspecialchars($_POST['endDate']) : (isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d')); ?>" class="mt-1 p-2 border rounded w-full">
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" id="filterBtn" class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600">Filter</button>
                    <button type="button" id="exportBtn" class="p-2 bg-green-500 text-white rounded export-btn">Download</button>
                </div>
            </div>
        </form>

        <div id="dataHadir">
            <table class="table table-bordered w-full bg-white shadow rounded">
                <thead id="tableHeader">
                    <tr align="center">
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Hadir</th>
                        <th>Sakit</th>
                        <th>Izin</th>
                        <th>Alpa</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data akan dimuat di sini via AJAX -->
                </tbody>
            </table>
        </div>

        <div class="modal" id="editModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Kehadiran</h3>
                    <span class="modal-nama-siswa" id="editNamaSiswa"></span>
                </div>

                <form id="editForm">
                    <input type="hidden" name="siswa_id" id="editSiswaId">
                    <input type="hidden" name="startDate" id="editStartDate">
                    <input type="hidden" name="endDate" id="editEndDate">
                    <input type="hidden" name="kelas_id" id="editKelasId">
                    <input type="hidden" name="mapel_id" id="editMapelId">

                    <div class="modal-body space-y-4">
                        <div>
                            <label for="editTanggal" class="block text-sm font-medium text-gray-700">Pilih Tanggal:</label>
                            <input type="date" name="tanggal" id="editTanggal" class="mt-1 p-2 border rounded w-full" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block">
                                <input type="checkbox" name="hadir" id="editHadir"> Hadir
                            </label>
                            <label class="block">
                                <input type="checkbox" name="sakit" id="editSakit"> Sakit
                            </label>
                            <label class="block">
                                <input type="checkbox" name="izin" id="editIzin"> Izin
                            </label>
                            <label class="block">
                                <input type="checkbox" name="alpa" id="editAlpa"> Alpa
                            </label>
                        </div>
                    </div>

                    <div class="modal-footer mt-4 flex justify-end space-x-2">
                        <button type="button" class="close-btn bg-gray-300 text-gray-700 p-2 rounded hover:bg-gray-400">Batal</button>
                        <button type="submit" id="saveBtn" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600" disabled>Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('absensiForm');
            const filterBtn = document.getElementById('filterBtn');
            const exportBtn = document.getElementById('exportBtn');
            const dataHadir = document.getElementById('dataHadir');
            const tableBody = document.getElementById('tableBody');
            const modal = document.getElementById('editModal');
            const editForm = document.getElementById('editForm');
            const closeBtnSecondary = modal.querySelector('.close-btn');
            const checkboxes = modal.querySelectorAll('input[type="checkbox"]');
            const tanggalSelect = document.getElementById('editTanggal');
            const saveBtn = document.getElementById('saveBtn');
            const mapelInput = document.getElementById('mapel');
            const mapelSuggestions = document.getElementById('mapelSuggestions');
            const mapelIdInput = document.getElementById('mapel_id');
            let currentDates = [];

            if (!filterBtn) {
                console.error('Tombol filterBtn tidak ditemukan!');
                return;
            }
            if (!exportBtn) {
                console.error('Tombol exportBtn tidak ditemukan!');
                return;
            }

            // Autocomplete untuk Mata Pelajaran
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

            // Membatasi hanya satu checkbox yang dapat dicentang
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        checkboxes.forEach(cb => {
                            if (cb !== this) {
                                cb.checked = false;
                            }
                        });
                    }
                    saveBtn.disabled = !tanggalSelect.value || !checkboxesChecked();
                });
            });

            // Fungsi untuk memeriksa apakah ada checkbox yang dicentang
            function checkboxesChecked() {
                return Array.from(checkboxes).some(cb => cb.checked);
            }

            // Fungsi untuk menghasilkan daftar tanggal dalam rentang
            function getDateRange(startDate, endDate) {
                const dates = [];
                let currentDate = new Date(startDate);
                const end = new Date(endDate);

                while (currentDate <= end) {
                    const day = String(currentDate.getDate()).padStart(2, '0');
                    const month = String(currentDate.getMonth() + 1).padStart(2, '0');
                    const year = currentDate.getFullYear();
                    dates.push(`${year}-${month}-${day}`);
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                return dates;
            }

            // Fungsi untuk memuat data tabel berdasarkan filter
            function loadTable(startDate, endDate, kelasId, mapelId = '') {
                if (!startDate || !endDate || !kelasId) {
                    console.warn('startDate, endDate, atau kelasId tidak tersedia untuk memuat tabel.');
                    return;
                }

                if (new Date(endDate) < new Date(startDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Tanggal Akhir tidak boleh sebelum Tanggal Awal!'
                    });
                    return;
                }

                currentDates = getDateRange(startDate, endDate);
                dataHadir.classList.remove('table-scroll');

                dataHadir.classList.remove('show');
                tableBody.innerHTML = `<tr><td colspan="7" align="center">Memuat data...</td></tr>`;

                fetch('tabel_siswa.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}&kelas_id=${encodeURIComponent(kelasId)}&mapel_id=${encodeURIComponent(mapelId)}&isSingleDate=${startDate === endDate}&isMultiMonth=${new Date(startDate).getMonth() !== new Date(endDate).getMonth() || new Date(startDate).getFullYear() !== new Date(endDate).getFullYear()}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    console.log('Respons dari tabel_siswa.php:', data);
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(data, 'text/html');
                    const tbody = doc.querySelector('tbody');

                    if (tbody && tbody.innerHTML.trim() !== '' && !data.includes('Tidak ada data') && !data.includes('Terjadi kesalahan')) {
                        console.log('Jumlah baris ditemukan:', tbody.children.length);
                        tableBody.innerHTML = tbody.innerHTML;
                        dataHadir.classList.add('show');
                        setupEditButtons();
                    } else {
                        console.warn('Tidak ada tbody yang valid atau konten kosong');
                        tableBody.innerHTML = data.includes('<tr') ? data : `<tr><td colspan="7" align="center">Tidak ada data untuk ditampilkan.</td></tr>`;
                        dataHadir.classList.add('show');
                        setupEditButtons();
                    }
                })
                .catch(error => {
                    console.error('Error saat memuat tabel:', error);
                    tableBody.innerHTML = `<tr><td colspan="7" align="center">Terjadi kesalahan saat memuat data: ${error.message}</td></tr>`;
                    dataHadir.classList.add('show');
                });
            }

            filterBtn.addEventListener('click', function() {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const kelasId = document.getElementById('kelas_id').value;
                const mapelId = document.getElementById('mapel_id').value;

                if (!kelasId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Pilih kelas terlebih dahulu!'
                    });
                    return;
                }

                if (!startDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Pilih Tanggal Awal terlebih dahulu!'
                    });
                    return;
                }

                if (!endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Pilih Tanggal Akhir terlebih dahulu!'
                    });
                    return;
                }

                console.log('Filter diklik - startDate:', startDate, 'endDate:', endDate, 'kelasId:', kelasId, 'mapelId:', mapelId);
                loadTable(startDate, endDate, kelasId, mapelId);
            });

            exportBtn.addEventListener('click', function() {
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const kelasId = document.getElementById('kelas_id').value;
                const mapelId = document.getElementById('mapel_id').value;

                if (!kelasId || !startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Pilih kelas, Tanggal Awal, dan Tanggal Akhir terlebih dahulu!'
                    });
                    return;
                }

                if (new Date(endDate) < new Date(startDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Tanggal Akhir tidak boleh sebelum Tanggal Awal!'
                    });
                    return;
                }

                console.log('Tombol Download diklik - startDate:', startDate, 'endDate:', endDate, 'kelasId:', kelasId, 'mapelId:', mapelId);
                Swal.fire({
                    title: 'Pilih Format Ekspor',
                    text: 'Silakan pilih format file untuk ekspor data.',
                    icon: 'question',
                    showCancelButton: true,
                    cancelButtonText: 'Batal',
                    showDenyButton: true,
                    confirmButtonText: 'PDF',
                    denyButtonText: 'Excel',
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#10b981'
                }).then((result) => {
                    if (result.isConfirmed) {
                        console.log('Ekspor ke PDF');
                        window.location.href = `proses/download/tcpdf.php?startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}&kelas_id=${encodeURIComponent(kelasId)}&mapel_id=${encodeURIComponent(mapelId)}`;
                    } else if (result.isDenied) {
                        console.log('Ekspor ke Excel');
                        window.location.href = `proses/download/excel.php?startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}&kelas_id=${encodeURIComponent(kelasId)}&mapel_id=${encodeURIComponent(mapelId)}`;
                    }
                });
            });

            function setupEditButtons() {
                const editButtons = document.querySelectorAll('.edit-btn');
                console.log('Jumlah tombol edit ditemukan:', editButtons.length);

                editButtons.forEach(button => {
                    button.removeEventListener('click', handleEditClick);
                    button.addEventListener('click', handleEditClick);
                });
            }

            function handleEditClick(e) {
                e.preventDefault();
                console.log('Tombol edit diklik:', this);
                console.log('Atribut data:', {
                    siswaId: this.dataset.siswaId,
                    namaSiswa: this.dataset.namaSiswa,
                    attendance: this.dataset.attendance
                });

                const siswaId = this.dataset.siswaId;
                const namaSiswa = this.dataset.namaSiswa || 'Nama tidak tersedia';
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const kelasId = document.getElementById('kelas_id').value;
                const mapelId = document.getElementById('mapel_id').value;

                // Validate inputs
                if (!siswaId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Data siswa tidak valid.'
                    });
                    return;
                }
                if (!startDate || !endDate) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tanggal Awal dan Tanggal Akhir harus diisi.'
                    });
                    return;
                }
                if (new Date(endDate) < new Date(startDate)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tanggal Akhir tidak boleh sebelum Tanggal Awal.'
                    });
                    return;
                }

                modal.querySelector('#editSiswaId').value = siswaId;
                modal.querySelector('#editNamaSiswa').textContent = namaSiswa;
                modal.querySelector('#editStartDate').value = startDate;
                modal.querySelector('#editEndDate').value = endDate;
                modal.querySelector('#editKelasId').value = kelasId;
                modal.querySelector('#editMapelId').value = mapelId;

                checkboxes.forEach(cb => cb.checked = false);
                saveBtn.disabled = true;

                // Populate date dropdown
                tanggalSelect.innerHTML = '<option value="">Pilih Tanggal</option>';
                currentDates = getDateRange(startDate, endDate);
                if (currentDates.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Tidak ada tanggal dalam rentang yang dipilih.'
                    });
                    return;
                }

                currentDates.forEach(date => {
                    const displayDate = date.split('-').reverse().join('-');
                    tanggalSelect.innerHTML += `<option value="${date}">${displayDate}</option>`;
                });

                let attendanceData = {};
                try {
                    attendanceData = JSON.parse(this.dataset.attendance || '{}');
                } catch (e) {
                    console.error('Error parsing attendance data:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Data kehadiran tidak valid.'
                    });
                    return;
                }

                // Remove existing event listeners
                const existingListeners = tanggalSelect.__eventListeners || [];
                existingListeners.forEach(listener => {
                    tanggalSelect.removeEventListener('change', listener);
                });

                const handleDateChange = function() {
                    const selectedDate = this.value;
                    checkboxes.forEach(cb => cb.checked = false);
                    if (selectedDate && attendanceData[selectedDate]) {
                        const { hadir, sakit, izin, alpa } = attendanceData[selectedDate];
                        modal.querySelector('#editHadir').checked = hadir === 1;
                        modal.querySelector('#editSakit').checked = sakit === 1;
                        modal.querySelector('#editIzin').checked = izin === 1;
                        modal.querySelector('#editAlpa').checked = alpa === 1;
                        console.log('Tanggal dipilih:', selectedDate, 'Attendance:', attendanceData[selectedDate]);
                    } else {
                        console.log('Tanggal dipilih:', selectedDate, 'Attendance: Tidak ada data');
                    }
                    saveBtn.disabled = !selectedDate || !checkboxesChecked();
                };

                tanggalSelect.__eventListeners = [handleDateChange];
                tanggalSelect.addEventListener('change', handleDateChange);

                // Pre-select the first date
                if (currentDates.length > 0) {
                    tanggalSelect.value = currentDates[0];
                    handleDateChange.call(tanggalSelect);
                }

                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.classList.add('show');
                }, 10);
                console.log('Modal seharusnya tampil sekarang dengan animasi');
            }

            // Menangani pengiriman form edit melalui AJAX
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(editForm);
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const kelasId = document.getElementById('kelas_id').value;
                const mapelId = document.getElementById('mapel_id').value;
                const siswaId = document.getElementById('editSiswaId').value;

                fetch('update_absensi.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
                        });
                    }
                    return response.json().catch(() => {
                        return response.text().then(text => {
                            throw new Error(`Invalid JSON response: ${text}`);
                        });
                    });
                })
                .then(data => {
                    console.log('Respons dari update_absensi.php:', data);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: data.message || 'Data kehadiran berhasil diperbarui!'
                        });
                        closeModal();
                        loadTable(startDate, endDate, kelasId, mapelId);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Gagal memperbarui data kehadiran.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error saat mengupdate data:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengupdate data: ' + error.message
                    });
                });
            });

            function closeModal() {
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    const tanggalSelect = document.getElementById('editTanggal');
                    const existingListeners = tanggalSelect.__eventListeners || [];
                    existingListeners.forEach(listener => {
                        tanggalSelect.removeEventListener('change', listener);
                    });
                    tanggalSelect.__eventListeners = [];
                }, 400);
                console.log('Modal ditutup');
            }

            if (closeBtnSecondary) {
                closeBtnSecondary.addEventListener('click', closeModal);
            }

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeModal();
                }
            });

            function confirmLogout(event) {
                event.preventDefault();
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda akan keluar dari sistem!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Logout!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'logout.php';
                    }
                });
            }

            const urlParams = new URLSearchParams(window.location.search);
            const startDateFromUrl = urlParams.get('startDate');
            const endDateFromUrl = urlParams.get('endDate');
            const kelasIdFromUrl = urlParams.get('kelas_id');
            const mapelIdFromUrl = urlParams.get('mapel_id');
            console.log('URL Params - startDate:', startDateFromUrl, 'endDate:', endDateFromUrl, 'kelas_id:', kelasIdFromUrl, 'mapel_id:', mapelIdFromUrl);
            if (startDateFromUrl && endDateFromUrl && kelasIdFromUrl) {
                document.getElementById('startDate').value = startDateFromUrl;
                document.getElementById('endDate').value = endDateFromUrl;
                document.getElementById('kelas_id').value = kelasIdFromUrl;
                if (mapelIdFromUrl) {
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'query=' + encodeURIComponent(mapelIdFromUrl)
                    })
                    .then(response => response.json())
                    .then(data => {
                        const pelajaran = data.find(p => p.id === mapelIdFromUrl);
                        if (pelajaran) {
                            mapelInput.value = pelajaran.mata_pelajaran;
                            mapelIdInput.value = pelajaran.id;
                        }
                        loadTable(startDateFromUrl, endDateFromUrl, kelasIdFromUrl, mapelIdFromUrl);
                    })
                    .catch(error => {
                        console.error('Error fetching mapel:', error);
                        loadTable(startDateFromUrl, endDateFromUrl, kelasIdFromUrl);
                    });
                } else {
                    loadTable(startDateFromUrl, endDateFromUrl, kelasIdFromUrl);
                }
            }
        });
    </script>
</body>
</html>