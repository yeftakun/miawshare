<?php
// Include koneksi ke database
include '../../koneksi.php';

// Periksa apakah terdapat parameter page dan id yang diterima melalui URL
if(isset($_GET['page']) && isset($_GET['id'])) {
    // Ambil nilai page dan id dari URL
    $page = $_GET['page'];
    $id = $_GET['id'];

    // Tentukan tabel mana yang akan dihapus berdasarkan nilai page
    switch ($page) {
        case 'level':
            $table = 'level';
            $id_column = 'level_id';
            break;
        case 'otp':
            $table = 'otp';
            $id_column = 'id';
            break;
        case 'posts':
            $table = 'posts';
            $id_column = 'post_id';
            break;
        case 'users':
            $table = 'users';
            $id_column = 'user_id';
            break;
        default:
            // Jika nilai page tidak sesuai, beri respon dengan status error
            http_response_code(400);
            echo "Invalid page parameter";
            exit();
    }

    // Buat query DELETE berdasarkan tabel dan id
    $query = "DELETE FROM $table WHERE $id_column = $id";

    // Eksekusi query DELETE
    if(mysqli_query($koneksi, $query)) {
        // Jika penghapusan berhasil, beri respon dengan status sukses
        // http_response_code(200);
        // echo "Data berhasil dihapus";
        if ($page == 'posts'){
            header("Location: delete_trash_file.php?pesan=success_delete");
        }else{
            header("Location: ../admin_panel.php?pesan=success_delete");
        }
    } else {
        // Jika terjadi kesalahan saat menghapus data, beri respon dengan status error
        http_response_code(500);
        echo "Gagal menghapus data: " . mysqli_error($koneksi);
    }
} else {
    // Jika parameter page dan id tidak ditemukan, beri respon dengan status error
    http_response_code(400);
    echo "Parameter page dan id diperlukan";
}
?>
