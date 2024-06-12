<?php
include '../koneksi.php';

// Buat query untuk menghapus baris berdasarkan kondisi yang diberikan
$query = "DELETE FROM users WHERE status = 'Nonaktif' AND delete_in <= NOW()";

mysqli_query($koneksi, $query);

// Tutup koneksi
mysqli_close($koneksi);
?> 
