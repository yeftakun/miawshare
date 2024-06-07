<?php
session_start();
include '../../koneksi.php';

// Memeriksa apakah pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("location:../../index.php?pesan=needlogin");
    exit(); // Stop further execution
}
 
// Memeriksa apakah parameter post_id tersedia
if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];

    // Query untuk mengambil data gambar yang akan dihapus
    $query = "SELECT post_img_path FROM posts WHERE post_id = '$postId'";
    $result = mysqli_query($koneksi, $query);
    $row = mysqli_fetch_assoc($result);

    // Pastikan gambar ditemukan
    if ($row) {
        // Hapus gambar dari direktori
        $imagePath = "../../storage/posting/" . $row['post_img_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Query untuk menghapus postingan dari database
        $queryDeletePost = "DELETE FROM posts WHERE post_id = '$postId'";
        $resultDeletePost = mysqli_query($koneksi, $queryDeletePost);

        // Redirect kembali ke halaman beranda setelah penghapusan berhasil
        if ($resultDeletePost) {
            header("location:../beranda.php?pesan=deleted");
            exit();
        } else {
            echo "Gagal menghapus postingan.";
        }
    } else {
        echo "Gambar tidak ditemukan.";
    }
} else {
    echo "Parameter post_id tidak tersedia.";
}
?>
