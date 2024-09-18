<?php
include 'koneksi.php';
// ambil data config dari database
$sql = "SELECT * FROM config";
$data = mysqli_fetch_assoc(mysqli_query($koneksi, $sql));
// Definisikan env dari nilai config
define('TOKEN_BOT', $data['token_bot']);
define('MAX_IMAGE_SIZE', (int)$data['img_size']);
define('MAX_PROFILE_IMAGE_SIZE', (int)$data['profile_size']);
define('OWNER_CHAT_ID', $data['owner_chat_id']);
define('LIMIT_BERANDA', (int)$data['limit_beranda']);
define('NSFW_DETECT', (int)$data['nsfw_detect']);
define('HOST_MAIN', $data['host_main']);
define('HOST_API', $data['host_api']);

// echo semua data dan cek tipe datanya
// echo 'TOKEN_BOT: ' . TOKEN_BOT . ' (' . gettype(TOKEN_BOT) . ')' . PHP_EOL . '<br>';
// echo 'MAX_IMAGE_SIZE: ' . MAX_IMAGE_SIZE . ' (' . gettype(MAX_IMAGE_SIZE) . ')' . PHP_EOL . '<br>';
// echo 'MAX_PROFILE_IMAGE_SIZE: ' . MAX_PROFILE_IMAGE_SIZE . ' (' . gettype(MAX_PROFILE_IMAGE_SIZE) . ')' . PHP_EOL . '<br>';
// echo 'OWNER_CHAT_ID: ' . OWNER_CHAT_ID . ' (' . gettype(OWNER_CHAT_ID) . ')' . PHP_EOL . '<br>';
// echo 'LIMIT_BERANDA: ' . LIMIT_BERANDA . ' (' . gettype(LIMIT_BERANDA) . ')' . PHP_EOL . '<br>';
// echo 'NSFW_DETECT: ' . NSFW_DETECT . ' (' . gettype(NSFW_DETECT) . ')' . PHP_EOL . '<br>';
// echo 'HOST_MAIN: ' . HOST_MAIN . ' (' . gettype(HOST_MAIN) . ')' . PHP_EOL . '<br>';
// echo 'HOST_API: ' . HOST_API . ' (' . gettype(HOST_API) . ')' . PHP_EOL . '<br>';
?>