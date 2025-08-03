<?php
session_start();

if (!isset($_SESSION['nama']) || $_SESSION['role'] != 'admin') {
    header("Location: ../");
    exit();
}

if (isset($_GET['file_path'])) {
    $file_path = urldecode($_GET['file_path']);
    $full_file_path = "../guru/" . $file_path;
    if (!empty($file_path) && file_exists($full_file_path)) {
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $file_name = basename($full_file_path);
        $file_size = filesize($full_file_path) / 1024; // Ukuran dalam KB
        $upload_time = date("Y-m-d H:i:s", filemtime($full_file_path)); // Waktu unggah
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>View File Materi</title>
            <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
            <style>
                .file-container-preview {
                    width: 100%;
                    height: 80vh;
                    margin-top: 20px;
                    overflow: auto;
                    border: 1px solid #e9ecef;
                    border-radius: 10px;
                    background-color: #f8f9fa;
                }
                .file-container-preview img, .file-container-preview iframe {
                    width: 100%;
                    height: 100%;
                    border: none;
                    border-radius: 8px;
                }
                .file-container-download {
                    width: 100%;
                    height: 80vh;
                    margin-top: 20px;
                    overflow: auto;
                    border: 1px solid #e9ecef;
                    border-radius: 10px;
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                    animation: fadeIn 0.5s ease-in-out;
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .unsupported-dashboard {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    height: 100%;
                    padding: 30px;
                }
                .unsupported-dashboard .file-icon {
                    font-size: 60px;
                    color: #007bff;
                    margin-bottom: 20px;
                    transition: transform 0.3s ease;
                }
                .unsupported-dashboard:hover .file-icon {
                    transform: scale(1.1);
                }
                .unsupported-dashboard h4 {
                    color: #dc3545;
                    font-size: 1.5rem;
                    margin-bottom: 20px;
                    text-transform: uppercase;
                }
                .file-details {
                    text-align: center;
                    background: #ffffff;
                    padding: 20px;
                    border-radius: 10px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
                    margin-bottom: 20px;
                }
                .file-details p {
                    margin: 8px 0;
                    color: #495057;
                }
                .file-details p strong {
                    color: #007bff;
                }
                .action-buttons {
                    display: flex;
                    justify-content: center;
                    gap: 10px; /* Jarak antara tombol */
                }
                .action-buttons .btn {
                    padding: 10px 20px;
                    font-weight: bold;
                    transition: all 0.3s ease;
                }
                .action-buttons .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }
                .btn-primary {
                    background-color: #007bff;
                    border-color: #007bff;
                }
                .btn-secondary-preview {
                    background-color: #28a745; /* Hijau untuk file yang bisa ditampilkan */
                    border-color: #28a745;
                    color: #fff;
                }
                .btn-secondary-download {
                    background-color: #6c757d; /* Abu-abu untuk file yang harus diunduh */
                    border-color: #6c757d;
                    color: #fff;
                }
                .btn-secondary-preview:hover, .btn-secondary-download:hover {
                    opacity: 0.9;
                }
                .header-container {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }
            </style>
        </head>
        <body>
            <div class="container mt-3">
                <div class="header-container">
                    <h3>Pratinjau File Materi</h3>
                    <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'])): ?>
                        <a href="javascript:history.back()" class="btn btn-secondary-download">
                            <i class="fas fa-sign-out-alt"></i> Keluar
                        </a>
                    <?php endif; ?>
                </div>
                <div class="file-container-<?php echo in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt']) ? 'preview' : 'download'; ?>">
                    <?php
                    $supported_preview = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'txt'];
                    if (in_array($file_extension, $supported_preview)) {
                        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='" . htmlspecialchars($full_file_path) . "' alt='File Materi'>";
                        } elseif ($file_extension === 'pdf') {
                            echo "<iframe src='" . htmlspecialchars($full_file_path) . "' frameborder='0'></iframe>";
                        } elseif ($file_extension === 'txt') {
                            $content = file_get_contents($full_file_path);
                            echo "<pre>" . htmlspecialchars($content) . "</pre>";
                        }
                    } else {
                        echo "<div class='unsupported-dashboard'>";
                        echo "<div class='file-icon'><i class='fas fa-file'></i></div>";
                        echo "<h4>File Tidak Dapat Ditampilkan Langsung</h4>";
                        echo "<div class='file-details'>";
                        echo "<p><strong>Nama File:</strong> " . htmlspecialchars($file_name) . "</p>";
                        echo "<p><strong>Jenis File:</strong> " . htmlspecialchars($file_extension) . "</p>";
                        echo "<p><strong>Ukuran:</strong> " . number_format($file_size, 2) . " KB</p>";
                        echo "<p><strong>Terakhir Diunggah:</strong> " . htmlspecialchars($upload_time) . "</p>";
                        echo "</div>";
                        echo "<div class='action-buttons'>";
                        echo "<a href='" . htmlspecialchars($full_file_path) . "' download class='btn btn-primary'>";
                        echo "<i class='fas fa-download'></i> Unduh Sekarang</a>";
                        echo "<a href='javascript:history.back()' class='btn btn-secondary-download'>";
                        echo "<i class='fas fa-times'></i> Keluar</a>";
                        echo "</div>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const iframe = document.querySelector('iframe');
                    if (iframe) {
                        iframe.onerror = function() {
                            iframe.style.display = 'none';
                            const container = document.querySelector('.file-container-preview');
                            container.innerHTML = '<div class="unsupported-dashboard"><div class="file-icon"><i class="fas fa-file"></i></div><h4>File Tidak Dapat Ditampilkan</h4><p>Gagal memuat file. <a href="<?php echo htmlspecialchars($full_file_path); ?>" download class="btn btn-primary"><i class="fas fa-download"></i> Unduh Sekarang</a></p></div>';
                        };
                    }
                });
            </script>
        </body>
        </html>
        <?php
    } else {
        echo "<h3>File tidak ditemukan!</h3>";
        exit();
    }
} else {
    echo "<h3>Parameter file_path tidak ditemukan!</h3>";
    exit();
}
?>