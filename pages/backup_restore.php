<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MiawShare - Backup & Restore</title>
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <link rel="stylesheet" href="../styles/alert.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        body a {
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
            font-size: 20px; /* Ukuran teks */
        }
        .container {
            margin-top: 70px; /* Adjust based on header height */
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
        .drop-area {
            border: 2px dashed #4CAF50;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            color: #4CAF50;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 20px;
        }
        .drop-area.hover {
            background-color: #e0f0e0;
        }
        .file-name {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }
        .drop-area input[type="file"] {
            display: none;
        }
    </style>
</head>
<body>
    <div class="fixed-header">
        <div class="left-menu">
            <a href="another.php">Kembali ke Lainnya</a>
        </div>
        <div class="right-menu">
            <!-- Placeholder for any right-menu content -->
        </div>
    </div>
    <?php
    if(isset($_GET['pesan'])){
        if($_GET['pesan'] == 'restore-success'){
            echo "<div class='done'>Success restore file</div>";
        }else if($_GET['pesan'] == 'restore-failed'){
            echo "<div class='error'>Failed restore file: " . htmlspecialchars($_GET['error']) . "</div>";
        }
    }
    ?>
    <div class="container">
        <div class="content">
            <h1>Backup & Restore</h1>
            <a class="button" href="another.php">Kembali</a>
            <h2>Backup</h2>
            <p>Backup gambar dan data ke file zip.</p>
            <button class="button" onclick="startBackup()">Backup</button>
            <div id="status" class="status"></div>
            <h2>Restore</h2>
            <form action="crud/restore.php" method="post" enctype="multipart/form-data">
                <div id="drop-area" class="drop-area">
                    Drag & drop file here or <span id="browse-text">browse</span>
                    <input type="file" name="backup_file" id="fileInput" required>
                    <div id="file-name" class="file-name"></div>
                </div>
                <!-- <input type="file" name="backup_file" id="hidden-file-input" required style="display:none;"> -->
                <button type="submit" class="button">Restore</button>
            </form>
        </div>
    </div>

    <script>
        function startBackup() {
            document.getElementById('status').innerHTML = 'Tunggu sebentar...';
            var iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = 'crud/backup.php';
            document.body.appendChild(iframe);

            var checkDownload = setInterval(function() {
                var xhr = new XMLHttpRequest();
                xhr.open('HEAD', 'crud/backup.php', true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        clearInterval(checkDownload);
                        document.getElementById('status').innerHTML = 'Selesai';
                    }
                };
                xhr.send();
            }, 1000);
        }

        function showWaitMessage() {
            document.getElementById('status').innerHTML = 'Tunggu sebentar...';
        }

        // Drag and drop functionality
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('fileInput');
        const fileNameDisplay = document.getElementById('file-name');
        const browseText = document.getElementById('browse-text');

        dropArea.addEventListener('click', () => fileInput.click());

        dropArea.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropArea.classList.add('hover');
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.classList.remove('hover');
        });

        dropArea.addEventListener('drop', (event) => {
            event.preventDefault();
            dropArea.classList.remove('hover');
            if (event.dataTransfer.files.length) {
                fileInput.files = event.dataTransfer.files;
                displayFileName(event.dataTransfer.files[0]);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) {
                displayFileName(fileInput.files[0]);
            }
        });

        function displayFileName(file) {
            fileNameDisplay.textContent = `File selected: ${file.name}`;
        }
    </script>
</body>
</html>
