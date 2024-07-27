<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MiawShare - Lainnya</title>
    <link rel="stylesheet" href="../styles/alert.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        body a{
            text-decoration: none;
            color: blue;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .content {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        h2 {
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 10px;
        }
        .report-spam-bot {
            padding: 10px 20px;
            background-color: #ff0000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .report-spam-bot:hover {
            background-color: #cc0000;
        }
        .fixed-header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .fixed-header .left-menu a,
        .fixed-header .right-menu a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            margin-right: 30px;
            size: 20px; /* Ukuran teks */
        }
        .container {
            margin-top: 70px; /* Adjust based on header height */
        }
    </style>
</head>
<body>
    <div class="fixed-header">
        <div class="left-menu">
            <a href="profile.php">Kembali ke profile</a>
        </div>
        <div class="right-menu">
            
        </div>
    </div>
    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == 'reportspamsended') {
            echo "<div class='done'>Laporan bot spam berhasil terkirim</div>";
        } else if ($_GET['pesan'] == 'reportspamfailed') {
            echo "<div class='alert'>Laporan bot spam gagal terkirim</div>";
        }
    }
    ?>
    <div class="container">
        <h1>MiawShare - Lainnya</h1>
        <div class="content">
            <h2>Backup gambar</h2>
            <p>Anda dapat mengunduh gambar yang telah diunggah ke MiawShare dengan mengunjungi halaman <a href="crud/download.php">backup</a>.</p>
        </div>
        <div class="content">
            <h2>Laporkan Masalah - Spam Bot</h2>
            <p>Jika Anda menerima pesan spam dari bot kami, Anda dapat melaporkannya dengan menekan tombol "Laporkan Bot Spam" dan blokir sementara bot di Telegram.</p>
            <p>Pengguna dapat membuka blokir setiap 24 jam untuk mengecek bot sudah diperbaiki.</p>
            <button class="report-spam-bot" onclick="reportSpamBot()">Laporkan Bot Spam</button>
        </div>
        <div class="content">
            <h2>Hubungi Kami</h2>
            <p>Jika Anda memiliki pertanyaan atau masalah, Anda dapat menghubungi kami melalui email <a href="mailto:miawsharebeta@gmail.com"> miawsharebeta@gmail.com</a></p>
        </div>
        <div class="content">
            <h2>Backup</h2>
            <p>Backup gambar dan data anda ke file zip dengan mengunjungi halaman <a href="backup_restore.php">Backup/Restore</a>.</p>
        </div>
    </div>

    <script>
        function reportSpamBot() {
            if (confirm('Apakah Anda yakin ingin melaporkan bot spam?')) {
                window.location.href = 'crud/report_spam_bot.php';
            }
        }
    </script>
    <script src="../script/alert-time.js"></script>
</body>
</html>
