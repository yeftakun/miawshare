<?php
// Include koneksi ke database
include 'koneksi.php';

// Pesan kesalahan
$errorMsg = '';

// Include file environment.php
include 'environment.php';
// Menggunakan nilai dari variabel TOKEN_BOT
$token = TOKEN_BOT;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $userName = $_POST['user_name'];
    $name = $_POST['name'];
    $bio = $_POST['bio'];
    $password = $_POST['password'];
    $teleChatID = $_POST['tele_chat_id'];

    // Inisialisasi direktori dan nama file default
    $uploadDir = 'storage/profile/';
    $defaultImage = 'default.png';

    // Cek apakah user_name sudah ada dalam database
    $checkUsernameQuery = "SELECT COUNT(user_name) AS total FROM users WHERE user_name = '$userName'";
    $checkUsernameResult = mysqli_query($koneksi, $checkUsernameQuery);
    $row = mysqli_fetch_assoc($checkUsernameResult);
    $totalUsername = $row['total'];
    
    if ($totalUsername > 0) {
        $errorMsg = "Username sudah digunakan. Silakan pilih username lain.";
    } else {

        // Cek apakah tele_chat_id sudah ada dalam database
        $checkTeleChatIDQuery = "SELECT COUNT(tele_chat_id) AS total FROM users WHERE tele_chat_id = '$teleChatID'";
        $checkTeleChatIDResult = mysqli_query($koneksi, $checkTeleChatIDQuery);
        $rowTeleChatID = mysqli_fetch_assoc($checkTeleChatIDResult);
        $totalTeleChatID = $rowTeleChatID['total'];

        if ($totalTeleChatID > 0) {
            header("location:daftar.php?pesan=chatidused");
            exit();
        } else {

            // Generate kode OTP
            $generatedCode = mt_rand(100000, 999999);

            // Simpan kode OTP ke database
            $insertOTPQuery = "INSERT INTO otp (user_name, otp_code, to_use) VALUES ('$userName', '$generatedCode', 'register')";
            // eksekusi setelah data user berhasil diinput

            // API Telegram untuk mengirim pesan
            $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$teleChatID&text=Kode%20OTP%20Regis:%20`$generatedCode`";

            // Kirim pesan ke Telegram
            file_get_contents($telegramAPI);

            // Fungsi untuk mendapatkan ekstensi file
            function getFileExtension($filename) {
                return pathinfo($filename, PATHINFO_EXTENSION);
            }

            // Fungsi untuk mendapatkan nama file tanpa ekstensi
            function getFileNameWithoutExtension($filename) {
                return pathinfo($filename, PATHINFO_FILENAME);
            }

            // Validasi ukuran file
            if ($_FILES['image']['size'] > 0 && $_FILES['image']['size'] > 512 * 1024) {
                header("location:daftar.php?pesan=filetoolarge");
                exit();
            } else {
                // Validasi ekstensi file
                if ($_FILES['image']['size'] > 0) {
                    $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
                    $imageExtension = getFileExtension($_FILES['image']['name']);
                    if (!in_array($imageExtension, $allowedExtensions)) {
                        header("location:daftar.php?pesan=unsupportedfile");
                        exit();
                    }
                }

                // Proses upload gambar jika ada
                if (empty($errorMsg) && $_FILES['image']['size'] > 0) {
                    $fileName = $_FILES['image']['name'];
                    $filePath = $uploadDir . $fileName;
                    $i = 1;
                    while (file_exists($filePath)) {
                        $newFileName = getFileNameWithoutExtension($fileName) . '-' . $i . '.' . $imageExtension;
                        $filePath = $uploadDir . $newFileName;
                        $i++;
                    }
                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
                        $errorMsg = "Gagal mengunggah file gambar.";
                    }
                    // Set $filePath hanya dengan nama file
                    $filePath = basename($filePath);
                } else {
                    // Gunakan gambar default jika tidak ada gambar yang diunggah
                    $filePath = $defaultImage;
                }

                // Query untuk memasukkan data ke database
                $insertQuery = "INSERT INTO users (user_name, name, user_profile_path, user_bio, level_id, password, status, tele_chat_id) VALUES ('$userName', '$name', '$filePath', '$bio', 2, '$password', 'Nonaktif', '$teleChatID')";
                // Jalankan query
                if (empty($errorMsg) && mysqli_query($koneksi, $insertQuery)) {
                    mysqli_query($koneksi, $insertOTPQuery); // insert OTP code
                    // Redirect ke halaman sukses dengan alert jika berhasil
                    header("Location: verif-otp.php?pesan=registered");
                    exit();
                } elseif (empty($errorMsg)) {
                    $errorMsg = "Gagal memasukkan data ke database: " . mysqli_error($koneksi);
                }
            }
        }

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar</title>
    <link rel="stylesheet" href="styles/style.css">
    <link rel="icon" type="image/png" href="assets/logo/logo.png">
    <link rel="stylesheet" href="styles/alert.css">
</head>
<body>
    <?php
    if(isset($_GET['pesan'])){
        if($_GET['pesan']=="filetoolarge"){
            echo "<div class='alert'>File gambar tidak boleh lebih dari 512KB</div>";
        }
    }
    if(isset($_GET['pesan'])){
        if($_GET['pesan']=="unsupportedfile"){
            echo "<div class='alert'>Format file tidak didukung. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.</div>";
        }
    }
    if(isset($_GET['pesan'])){
        if($_GET['pesan']=="chatidused"){
            echo "<div class='alert'>ChatID Telegram sudah digunakan. Silakan gunakan ChatID yang lain.</div>";
        }
    }
    ?>
    <div class="header">
        <h1>Daftar</h1>
        <p><a href="index.php">Kembali</a></p>
    </div>
    <div class="form">
        <form method="POST" enctype="multipart/form-data" class="form">
            <!-- Tampilkan pesan kesalahan jika ada -->
            <?php if (!empty($errorMsg)) { ?>
                <div class="alert"><?php echo $errorMsg; ?></div>
            <?php } ?>
            <label for="user_name">Username:</label>
            <input type="text" id="user_name" name="user_name" required>

            <label for="name">Nama:</label>
            <input type="text" id="name" name="name" required>

            <!-- <label for="image" class="uploadimg">Upload Profile Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <img id="image-preview" src="#" alt="image-preview" style="display: none;">  -->
            <!-- Added image preview with display: none -->

            <label for="bio">Bio</label>
            <textarea id="bio" name="bio" placeholder="About you" rows="5" required></textarea>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="tele_chat_id">ChatID Telegram:</label>
            <input type="number" id="tele_chat_id" name="tele_chat_id" required>
            <p><a href="https://t.me/chatIDrobot" target="_blank">Dapatkan ChatID</a></p>
            <p>Sebelum submit, lakukan init (tulis apapun) terlebih dahulu <a href="https://t.me/spamtestingbot" target="_blank">di sini</a></p>


            <button class="button" type="submit">Daftar</button>
        </form>
    </div>

    <script src="script/preview-img.js"></script>
    <script src="script/alert-time.js"></script>
</body>
</html>
