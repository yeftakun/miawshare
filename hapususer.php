<?php
include 'koneksi.php';
include 'environment.php';

$token = TOKEN_BOT;
$chatID = OWNER_CHAT_ID;

// Buat query untuk menghapus baris berdasarkan kondisi yang diberikan
$query = "DELETE FROM users WHERE status = 'Nonaktif' AND delete_in <= NOW()";

mysqli_query($koneksi, $query);

// Tutup koneksi
mysqli_close($koneksi);

// $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=Cron%20Job%20is%20running%20every%20minute.";
// $ch = curl_init();

// // Set opsi cURL
// curl_setopt($ch, CURLOPT_URL, $telegramAPI);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// curl_exec($ch);

// // Tutup cURL
// curl_close($ch);
?>
