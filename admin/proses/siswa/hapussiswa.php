<?php
session_start();
include('../../../koneksi.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Start transaction
    mysqli_begin_transaction($koneksi);
    
    try {
        // Delete from nilai_siswa table first (child table)
        $query_nilai = "DELETE FROM nilai_siswa WHERE id_siswa = ?";
        $stmt_nilai = mysqli_prepare($koneksi, $query_nilai);
        mysqli_stmt_bind_param($stmt_nilai, "i", $id);
        mysqli_stmt_execute($stmt_nilai);
        mysqli_stmt_close($stmt_nilai);
        
        // Delete from siswa table (parent table)
        $query_siswa = "DELETE FROM siswa WHERE id = ?";
        $stmt_siswa = mysqli_prepare($koneksi, $query_siswa);
        mysqli_stmt_bind_param($stmt_siswa, "i", $id);
        
        if (mysqli_stmt_execute($stmt_siswa)) {
            // Commit transaction
            mysqli_commit($koneksi);
            header("Location: ../../siswa.php");
        } else {
            throw new Exception("Gagal menghapus data siswa: " . mysqli_error($koneksi));
        }
        
        mysqli_stmt_close($stmt_siswa);
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($koneksi);
        echo "<script>
            alert('" . $e->getMessage() . "');
            window.location='../../siswa.php';
        </script>";
    }
    
    mysqli_close($koneksi);
}
?>