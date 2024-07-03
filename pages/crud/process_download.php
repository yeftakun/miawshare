<?php
session_start();
include '../../koneksi.php';

// Cek apakah session download_file bernilai true
if (!isset($_SESSION['download_file']) || $_SESSION['download_file'] !== true) {
    header('Location: ../error/deniedpage.php');
    exit;
} else {
    $userId = $_SESSION['user_id'];
    $query = "SELECT post_img_path FROM posts WHERE user_id = '$userId'";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die("Query error: " . mysqli_error($koneksi));
    }

    $zipFile = 'downloaded_files.zip';
    $zip = new ZipArchive();

    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        while ($row = mysqli_fetch_assoc($result)) {
            $filePath = '../../storage/posting/' . $row['post_img_path']; // Sesuaikan dengan path file Anda

            if (file_exists($filePath)) {
                $zip->addFile($filePath, basename($filePath)); // Menambahkan file ke dalam ZIP
            } else {
                echo "File tidak ditemukan: $filePath<br>"; // Debug jika file tidak ditemukan
            }
        }

        $zip->close();

        // Set header untuk mengatur respons HTTP
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipFile);
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);

        // Hapus file zip setelah didownload
        unlink($zipFile);

        // Set session download_file ke false setelah selesai
        $_SESSION['download_file'] = false;

        exit;
    } else {
        // Jika gagal membuat file zip
        echo 'Gagal membuat file zip.';
    }
}
?>
