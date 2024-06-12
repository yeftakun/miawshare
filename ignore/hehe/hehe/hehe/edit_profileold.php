<?php
session_start();
include '../../koneksi.php';
include '../../environment.php';

// Memeriksa apakah pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("location:../../index.php?pesan=needlogin");
    exit(); // Stop further execution
}

$token = TOKEN_BOT;
$max_img_size = MAX_IMAGE_SIZE;
$teleChatID = $_SESSION['tele_chat_id'];

// Query untuk mendapatkan data pengguna yang sedang login
$queryUser = "SELECT * FROM users WHERE user_id = " . $_SESSION['user_id'];
$resultUser = mysqli_query($koneksi, $queryUser);
$userData = mysqli_fetch_assoc($resultUser);
$userProfImg = $userData['user_profile_path'];

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Periksa apakah tombol "Simpan Perubahan" ditekan
    if (isset($_POST['save_changes'])) {
        // Mengambil data dari form
        $name = $_POST['name'];
        $username = $_POST['username'];
        $user_bio = $_POST['user_bio'];

        // Memeriksa apakah ada file yang diupload
        if ($_FILES['image']['name']) {
            // Inisialisasi direktori dan nama file default
            $uploadDir = '../../storage/profile/';
            $defaultImage = 'default.png';

            // Fungsi untuk mendapatkan ekstensi file
            function getFileExtension($filename) {
                $path_parts = pathinfo($filename);
                return $path_parts['extension'];
            }

            // Mengambil informasi file yang diupload
            $fileName = $_FILES['image']['name'];
            $fileSize = $_FILES['image']['size'];
            $fileTmp = $_FILES['image']['tmp_name'];
            $fileType = $_FILES['image']['type'];
            $fileExt = getFileExtension($fileName);
            $fileExt = strtolower($fileExt);

            // Menghasilkan nama file baru
            $newFileName = $username . '.' . $fileExt;

            // Memeriksa apakah file yang diupload adalah gambar
            $allowedExtensions = array('jpg', 'jpeg', 'png');
            if (in_array($fileExt, $allowedExtensions)) {
                // Memeriksa apakah file yang diupload tidak melebihi batas ukuran
                if ($fileSize < $max_img_size) {
                    // Memeriksa apakah file yang diupload berhasil dipindahkan
                    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                        // Hapus foto profil lama jika bukan default.png
                        if ($userProfImg != 'default.png') {
                            unlink($uploadDir . $userProfImg);
                        }
                        // Assign nama file baru ke variabel
                        $userProfImg = $newFileName;
                    } else {
                        // Jika file yang diupload gagal dipindahkan
                        $errorMessage = "Gagal mengunggah file.";
                    }
                } else {
                    // Jika file yang diupload melebihi batas ukuran
                    $errorMessage = "Ukuran file terlalu besar. Maksimal 500KB.";
                }
            } else {
                // Jika file yang diupload bukan gambar
                $errorMessage = "File yang diunggah harus berupa gambar.";
            }
        }

        // Query untuk memperbarui data pengguna
        // $queryUpdateUser = "UPDATE users SET name = '$name', user_name = '$username', user_bio = '$user_bio', user_profile_path = '$user_profile_path' WHERE user_id = " . $_SESSION['user_id'];
        $queryUpdateUser = "UPDATE users SET name = '$name', user_name = '$username', user_bio = '$user_bio', user_profile_path = 'default.png' WHERE user_id = " . $_SESSION['user_id'];

        // Eksekusi query
        $resultUpdateUser = mysqli_query($koneksi, $queryUpdateUser);
        // memperbaharui session user_name
        $_SESSION['user_name'] = $username;
        // Memeriksa apakah perubahan berhasil disimpan
        if ($resultUpdateUser) {
            // Redirect ke halaman profile setelah perubahan disimpan
            header("location:../profile.php?user_name=" . $_SESSION['user_name']);
            exit();
        } else {
            // Jika terjadi kesalahan saat menyimpan perubahan
            $errorMessage = "Gagal menyimpan perubahan.";
        }
    }

    // Periksa apakah tombol "Reset Password" ditekan
    if (isset($_POST['reset_password'])) {
        // Mengambil data dari form
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Memeriksa apakah password lama sesuai dengan password yang tersimpan di database
        if ($old_password == $userData['password']) {
            // Memeriksa apakah password baru dan konfirmasi password cocok
            if ($new_password == $confirm_password) {
                // Query untuk memperbarui password pengguna
                $queryUpdatePassword = "UPDATE users SET password = '$new_password' WHERE user_id = " . $_SESSION['user_id'];

                // Eksekusi query
                $resultUpdatePassword = mysqli_query($koneksi, $queryUpdatePassword);

                // Memeriksa apakah password berhasil diperbarui
                if ($resultUpdatePassword) {
                    // API Telegram untuk mengirim pesan
                    $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$teleChatID&text=Password%20anda%20berhasil%20diubah.%0AJika%20anda%20tidak%20melakukan%20perubahan%20password,%20silahkan%20hubungi%20admin.";

                    // Kirim pesan ke Telegram
                    $ch = curl_init();
                    // Set opsi cURL
                    curl_setopt($ch, CURLOPT_URL, $telegramAPI);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    // Tutup cURL
                    curl_close($ch);

                    // Redirect ke halaman profile setelah password diperbarui
                    header("location:edit_profile.php?pesan=resetpasswordsuccess");
                    exit();
                } else {
                    // Jika terjadi kesalahan saat memperbarui password
                    header("location:edit_profile.php?pesan=resetpasswordfailed");
                    exit();
                }
            } else {
                // Jika password baru dan konfirmasi password tidak cocok
                header("location:edit_profile.php?pesan=resetpasswordmismatch");
            }
        } else {
            // Jika password lama tidak sesuai
            header("location:edit_profile.php?pesan=resetpasswordinvalid");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="../../styles/style.css">
    <link rel="stylesheet" type="text/css" href="../../styles/alert.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
</head>
<body>

<header>
                        <div class="logo">
                            <img src="../../assets/ico/HitoriGotou.ico" alt="logo" width="50">
                        </div>
                        <div class="home-search-bar">
                            <form action="search_result.php" method="GET">
                                <input type="text" name="search" id="searchInput" placeholder="Judul / #tag / username">
                                <input type="submit" value="Search">
                            </form>
                        </div>
                        <div class="nav-to">
                            <p><a href="../beranda.php">Beranda</a></p>
                        </div>
                        <div class="nav-to">
                            <p><a href="../post.php">
                                <?php
                                if ($_SESSION['level_id'] == 1) {
                                    echo 'Admin Panel';
                                } else {
                                    echo 'Posting';
                                }
                                ?>
                            </a></p>
                        </div>
                        <div class="profile-pic">
                            <a href="profile.php?user_name=<?php echo $_SESSION['user_name']; ?>">
                                <?php
                                echo '<img src="../../storage/profile/' . $_SESSION['user_profile_path'] . '" alt="' . $_SESSION['user_profile_path'] . '" width="50px"';
                            ?>
                            </a>
                        </div>
                        <div class="logout">
                            <a href="../../logout.php">LOGOUT</a>
                        </div>
</header>

<div class="container">
    <h2>Edit Profile</h2>
    <?php
    if(isset($_GET['pesan'])){
		if($_GET['pesan']=="resetpasswordsuccess"){
			echo "<div class='done'>Password berhasil diubah</div>";
		}
        if($_GET['pesan']=="resetpasswordfailed"){
            echo "<div class='alert'>Gagal mengubah password</div>";
        }
        if($_GET['pesan']=="resetpasswordmismatch"){
            echo "<div class='alert'>Password baru dan konfirmasi password tidak cocok</div>";
        }
        if($_GET['pesan']=="resetpasswordinvalid"){
            echo "<div class='alert'>Password lama tidak sesuai</div>";
        }
        if($_GET['pesan']=="invalidconfirmation"){
            echo "<div class='alert'>Konfirmasi tidak sesuai</div>";
        }
	}
    ?>
    <a href="../profile.php">Kembali</a>
    <form method="POST" enctype="multipart/form-data">
        <h3>User Info</h3>
        <!-- <label for="image" class="uploadimg">Ganti Foto</label>
        <input type="file" id="image" name="image" accept="image/*" > -->
        <!-- Image preview element -->
        <!-- <img id="image-preview" src="../../storage/profile/'<?php echo $userData['user_profile_img']; ?>'" alt="pp"> -->

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $userData['name']; ?>" required>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $userData['user_name']; ?>" required>
        <label for="user_bio">Bio:</label>
        <textarea id="user_bio" name="user_bio"><?php echo $userData['user_bio']; ?></textarea>
        <div class="buttons">
            <input type="submit" name="save_changes" value="Save Changes">
        </div>
    </form>
    <form method="POST" enctype="multipart/form-data">
        <h3>Reset Password</h3>
        <label for="old_password">Old Password:</label>
        <input type="password" id="old_password" name="old_password" required>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <div class="buttons">
            <input type="submit" name="reset_password" value="Reset Password">
        </div>
    </form>

    <!-- Bagian Form Danger Zone -->
    <form method="POST" action="delete_user.php" enctype="multipart/form-data">
        <h3>Danger Zone</h3>
        <p>Untuk menghapus akun Anda, masukkan teks berikut: <strong>hapus-akun-<?php echo $_SESSION['user_name']; ?></strong></p>
        <label for="delete_confirmation">Konfirmasi Penghapusan Akun:</label>
        <input type="text" id="delete_confirmation" name="delete_confirmation" placeholder="hapus-akun-<?php echo $_SESSION['user_name']; ?>" required>
        <div class="buttons">
            <input type="submit" name="delete_account" value="Hapus Akun">
        </div>
    </form>

</div>
    <script src="../../script/alert-time.js"></script>
    <script>
        function previewImage() {
            var preview = document.querySelector('#image-preview');
            var file = document.querySelector('input[type=file]').files[0];
            var reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.style.display = "block";
            }
            if (file){
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.style.display = 'none';
            }
        }

        document.querySelector('input[type=file]').addEventListener('change', previewImage);
    </script>
</body>
</html>
