<?php
// Mengambil informasi semua nama file di "select post_img_path from posts;"
include '../../koneksi.php';

$queryGetPostImages = "SELECT post_img_path FROM posts";
$resultGetPostImages = mysqli_query($koneksi, $queryGetPostImages);

// Array untuk menyimpan nama file yang terkait dengan postingan
$postImages = [];

while ($row = mysqli_fetch_assoc($resultGetPostImages)) {
    $postImages[] = $row['post_img_path'];
}

// Mengambil semua nama file di direktori "../storage/posting/"
$dir = '../../storage/posting/';
$files = array_diff(scandir($dir), array('.', '..')); // Menghapus "." dan ".." dari daftar file

// Menghapus file yang tidak terkait dengan postingan
foreach ($files as $file) {
    if (!in_array($file, $postImages)) {
        unlink($dir . $file); // Menghapus file dari direktori
    }
}

// Setelah selesai menghapus, arahkan kembali ke admin_panel.php
header("Location:../admin_panel.php?pesan=trash_file_deleted");
exit();
?>
