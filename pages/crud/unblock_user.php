<?php
session_start();
include '../../koneksi.php';

// hanya admin yang dapat mengakses halaman ini
if($_SESSION['level_id'] != 1) {
    header("location:../error/deniedpage.php");
}

// Periksa apakah terdapat parameter user_name yang diterima melalui URL
if(isset($_GET['user_name'])) {
    // Ambil nilai user_name dari URL
    $user_name = $_GET['user_name'];

    // Buat query UPDATE status user menjadi 'Suspened' berdasarkan user_name
    $query = "UPDATE users SET status = 'Aktif' WHERE user_name = '$user_name'";
    
    // Eksekusi query UPDATE
    if(mysqli_query($koneksi, $query)) {

        // Jika penghapusan berhasil, kembali ke halaman pengguna yang di block
        header("Location: ../profile.php?user_name=$user_name");
        // header("Location: ../profile.php?user_user_name=$user_name&pesan=success_block");
    } else {
        // Jika terjadi kesalahan saat menghapus data, beri respon dengan status error
        http_response_code(500);
        echo "Gagal memblokir user: " . mysqli_error($koneksi);
    }
} else {
    // Jika parameter user_name tidak ditemukan, beri respon dengan status error
    http_response_code(400);
    echo "Parameter user_name diperlukan";
}
?>