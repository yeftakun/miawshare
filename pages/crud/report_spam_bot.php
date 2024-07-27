<?php
session_start();
include '../../koneksi.php';
include '../../environment.php';

$token = TOKEN_BOT;

if (isset($_SESSION['user'])) {
    header('Location: ../../index.php?pesan=needlogin');
    exit;
}

// Dapatkan ChatID admin dari database
$query = "SELECT tele_chat_id FROM users WHERE level_id = '1'";
$result = mysqli_query($koneksi, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $chatID = $row['tele_chat_id'];
        $message = "*Laporan Masalah*\nPelapor: *" . $_SESSION['user_name'] . "*\nPerihal: *Bot telegram melakukan spam*";
        $TelegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=" . urlencode($message);
        file_get_contents($TelegramAPI);
    }
    header('Location: ../another.php?pesan=reportspamsended');
} else {
    header('Location: ../another.php?pesan=reportspamfailed');
}
?>
