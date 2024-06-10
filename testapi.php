<?php
$telegramAPI = "https://api.telegram.org/bot6906279097:AAHN_mpUN7GqUX_jqT8upKdjzsbQmNB3b-U/sendMessage?parse_mode=markdown&chat_id=1627790263&text=Cron%20Job%20is%20running%20every%20minute.";
$ch = curl_init();

// Set opsi cURL
curl_setopt($ch, CURLOPT_URL, $telegramAPI);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_exec($ch);

// Tutup cURL
curl_close($ch);
?>