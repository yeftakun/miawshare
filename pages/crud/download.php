<?php
session_start();
include '../../koneksi.php';

// cek apakah pengguna dengan level_id = 2 sudah login
// if (!isset($_SESSION['user_id']) || $_SESSION['level_id'] !== 2) {
if (false) {
    header('Location: ../login.php');
    exit;
}else{
    // Mendapatkan daftar file dari database
    $userId = $_SESSION['user_id'];
    $query = "SELECT post_img_path FROM posts WHERE user_id = '$userId'";
    $result = mysqli_query($koneksi, $query);

    if (!$result) {
        die("Query error: " . mysqli_error($conn));
    }

    $files = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $files[] = $row['post_img_path'];
    }

    $totalSize = 0;
    foreach ($files as $file) {
        // $filePath = '../../storage/posting' . $file; // Sesuaikan dengan path file Anda
        $filePath = '../../storage/posting/' . $file; // Sesuaikan dengan path file Anda
        if (file_exists($filePath)) {
            $totalSize += filesize($filePath);
        }
    }

    function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    // buat session download
    $_SESSION['download_file'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Download Files</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
        }
        .container {
            max-width: 600px;
            margin: auto;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            margin: 10px 0;
        }
        .status {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Download Files</h1>
        <p>Total ukuran file: <?php echo formatSizeUnits($totalSize); ?></p>
        <button class="button" onclick="cancelDownload()">Gak Jadi</button>
        <button class="button" onclick="startDownload()">Unduh</button>
        <div id="status" class="status"></div>
    </div>

    <script>
        function startDownload() {
            document.getElementById('status').innerHTML = 'Memporses file ke ZIP...';
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'process_download.php', true);
            xhr.responseType = 'blob';

            xhr.onload = function () {
                if (xhr.status === 200) {
                    var blob = xhr.response;
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'files.zip';
                    document.getElementById('status').innerHTML = 'Menyelesaikan...';
                    link.click();
                    document.getElementById('status').innerHTML = 'File siap diunduh.';
                } else {
                    document.getElementById('status').innerHTML = 'Terjadi kesalahan saat memproses unduhan.';
                }
            };

            xhr.send();
        }
        function cancelDownload() {
            // Redirect user to ../profile directory
            window.location.href = '../profile.php';
        }
    </script>
</body>
</html>
