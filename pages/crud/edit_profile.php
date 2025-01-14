<?php
session_start();
include '../../koneksi.php';
include '../../environment.php';

$max_profile_img_size = MAX_PROFILE_IMAGE_SIZE;
$profile_img_size_info = $max_profile_img_size / 1000;

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

        if ($_SESSION['username'] != $username){
            // Periksa apakah username sudah digunakan oleh pengguna lain
            $queryCheckUsername = "SELECT user_id FROM users WHERE user_name = '$username' AND user_id != " . $_SESSION['user_id'];
            $resultCheckUsername = mysqli_query($koneksi, $queryCheckUsername);
            if (mysqli_num_rows($resultCheckUsername) > 0) {
                header("location:edit_profile.php?pesan=username_alreadyexist");
                exit();
            }
        }

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

            // Memeriksa apakah nama file sudah ada di database, jika sudah ada, tambahkan penomoran di belakang nama file. Contoh: user.jpg, user-1.jpg, user-2.jpg, dst.
            $i = 1;
            while (file_exists($uploadDir . $newFileName)) {
                $newFileName = $username . '-' . $i . '.' . $fileExt;
                $i++;
            }

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
                    $errorMessage = "Ukuran file terlalu besar. Maksimal $profile_img_size_info KB.";
                }
            } else {
                // Jika file yang diupload bukan gambar
                $errorMessage = "File yang diunggah harus berupa gambar.";
            }
        }

        // Query untuk memperbarui data pengguna
        // $queryUpdateUser = "UPDATE users SET name = '$name', user_name = '$username', user_bio = '$user_bio', user_profile_path = '$user_profile_path' WHERE user_id = " . $_SESSION['user_id'];
        $queryUpdateUser = "UPDATE users SET name = '$name', user_name = '$username', user_bio = '$user_bio', user_profile_path = '$userProfImg' WHERE user_id = " . $_SESSION['user_id'];

        // Eksekusi query
        $resultUpdateUser = mysqli_query($koneksi, $queryUpdateUser);
        // memperbaharui session user_name
        $_SESSION['user_name'] = $username;
        // memperbaharui session user_profile_path
        $_SESSION['user_profile_path'] = $userProfImg;
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
        // memberikan escape string pada inputan password
        $old_password = mysqli_real_escape_string($koneksi, $old_password);

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
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Centered Upload Form</title>
    <link rel="stylesheet" href="../../styles/style.css" />
    <link rel="stylesheet" href="../../styles/alert.css" />
    <link rel="icon" type="image/png" href="../../assets/logo/logo.png">
    <!-- <link rel="stylesheet" href="../styles/style2.css" /> -->
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="../../script/script.js" defer></script>
    <style>
        .preview-container {
            border: 2px dashed #ccc;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            margin-top: 20px;
            position: relative;
            width: 100%;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }
        .preview-container img {
            width: 100%;
            height: auto;
            display: none;
        }
        .preview-container .preview-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #ccc;
        }
        .ganti-pp {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .image-container {
            position: relative;
            width: 100px;
            height: 100px;
        }

        #image-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #image-preview:hover {
            background-color: rgba(0, 0, 0, 0.3); /* Efek latar belakang redup */
        }

        .edit-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: rgba(255, 255, 255, 0.7);
            display: none;
            pointer-events: none;
        }

        .image-container:hover .edit-icon {
            display: block;
        }

        input[type="file"] {
            display: none;
        }

    </style>
  </head>
  <body>
    <br>
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
            if($_GET['pesan']=="username_alreadyexist"){
                echo "<div class='alert'>Username sudah digunakan oleh pengguna lain</div>";
            }
        }
        ?>
        <br>
    <main class="main_content">
        <div class="upload_container">
        <h2 align="center">Edit Profile</h2>
        <br>
        <!-- <a href="../profile.php">Kembali</a> -->
        <form method="POST" enctype="multipart/form-data" class="upload_form">
            <h3>User Info</h3>
            <div class="ganti-pp">
                <!-- Elemen pratinjau gambar -->
                <div class="image-container">
                    <img id="image-preview" src="../../storage/profile/<?php echo $userProfImg; ?>" alt="pp">
                    <i class="bx bxs-edit edit-icon"></i>
                </div>
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form_group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $userData['name']; ?>" required>
            </div>
            <div class="form_group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo $userData['user_name']; ?>" required>
            </div>
            <div class="form_group">
                <label for="user_bio">Bio:</label>
                <textarea id="user_bio" name="user_bio"><?php echo $userData['user_bio']; ?></textarea>
            </div>
            <!-- <div class="buttons">
                <input type="submit" name="save_changes" value="Save Changes" class="upload_btn flex">
            </div> -->
            <button type="submit" name="save_changes" value="Save Changes" class="upload_btn flex">
                <i class='bx bx-save'></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
        <br>
        <hr>
        <br>
        <form method="POST" enctype="multipart/form-data" class="upload_form">
            <h3>Reset Password</h3>
            <div class="form_group">
                <label for="old_password">Old Password:</label>
                <input type="password" id="old_password" name="old_password" required>
            </div>
            <div class="form_group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form_group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <!-- <div class="buttons">
                <input type="submit" name="reset_password" value="Reset Password" class="upload_btn flex">
                </div> -->
            <button type="submit" name="reset_password" value="Reset Password" class="upload_btn flex">
                <i class='bx bx-lock-alt'></i>
                <span>Ubah Password</span>
            </button>
        </form>
        <br>
        <hr>
        <br>
        <!-- Bagian Form Danger Zone -->
        <form method="POST" action="delete_user.php" enctype="multipart/form-data" class="upload_form">
            <h3 style="color: #ff0000;">Danger Zone</h3>
            <p>Untuk menghapus akun Anda, masukkan teks berikut: <strong style="color:#ff6666;">hapus-akun-<?php echo $_SESSION['user_name']; ?></strong></p>
            <br>
            <div class="form_group">
                <label for="delete_confirmation">Konfirmasi Penghapusan Akun:</label>
                <input type="text" id="delete_confirmation" name="delete_confirmation" placeholder="hapus-akun-<?php echo $_SESSION['user_name']; ?>" required>
            </div>
            <!-- <div class="buttons">
                <input type="submit" name="delete_account" value="Hapus Akun" class="upload_btn flex">
            </div> -->
            <button type="submit" name="delete_account" value="Hapus Akun" class="upload_btn flex">
                <i class='bx bx-trash'></i>
                <span>Hapus Akun</span>
            </button>
        </form>
    
    </div>
    </main>
    
    <!-- Sidebar -->
    <nav class="sidebar locked">
        <div class="logo_items flex">
            <span class="nav_image">
                <img src="../../assets/logo/logo.png" alt="logo_img" />
            </span>
            <span class="logo_name">MiawShare</span>
            <i class="bx bx-lock-alt" id="lock-icon" title="Unlock Sidebar"></i>
        </div>
        <div class="menu_container">
            <div class="menu_items">
                <ul class="menu_item">
                    <div class="menu_title flex">
                        <span class="title">Dashboard</span>
                        <span class="line"></span>
                    </div>
                    <li class="item">
                        <a href="../beranda.php" class="link flex">
                            <i class="bx bx-home-alt"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                </ul>
                <?php
                if(isset($_SESSION['level_id'])) {
                ?>
                <ul class="menu_item">
                <div class="menu_title flex">
                    <span class="title">Tools</span>
                    <span class="line"></span>
                </div>  
                <li class="item">
                    <?php
                    if($_SESSION['level_id'] == 1) {
                    ?>
                        <a href="../admin_panel.php" class="link flex">
                        <i class="bx bx-cog"></i>
                        <span>Admin Panel</span>
                        </a>
                    <?php
                    } elseif($_SESSION['level_id'] == 2) {
                    ?>
                        <a href="../post.php" class="link flex">
                        <i class='bx bx-upload'></i>
                        <span>Posting</span>
                        </a>
                    <?php
                    }
                    ?>
                </li>
                </ul>
                <?php
                }
                ?>
                <ul class="menu_item">
                <div class="menu_title flex">
                    <span class="title">Setting</span>
                    <span class="line"></span>
                </div>
                <?php
                if(isset($_SESSION['level_id'])){
                    ?>
                    <li class="item">
                        <a href="edit_profile.php" class="link flex">
                        <i class="bx bx-user"></i>
                        <span>Edit User</span>
                        </a>
                    </li>
                    <li class="item">
                        <a href="../../logout.php" class="link flex">
                        <i class="bx bx-log-out"></i>
                        <span>Log out</span>
                        </a>
                    </li>
                    <?php
                }else{
                    ?>
                    <li class="item">
                        <a href="../../index.php" class="link flex">
                        <i class="bx bx-log-in"></i>
                        <span>Login</span>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <li class="item">
                    <a href="../aboutus.php" class="link flex">
                    <i class="bx bx-flag"></i>
                    <span>About Us</span>
                    </a>
                </li>
                </ul>
            </div>

            <div class="sidebar_profile flex">
                <a href="<?php
                if(isset($_SESSION['level_id'])){
                    echo "../profile.php?user_name=", $_SESSION['user_name'];
                }else{
                    echo "#";
                }
                ?>">
                    <span class="nav_image">
                    <img src="
                    <?php
                    if(isset($_SESSION['level_id'])){
                        echo '../../storage/profile/' . $_SESSION['user_profile_path'];
                    }else{
                        echo '../../storage/profile/default.png';
                    }
                    ?>" alt="logo_img" />
                    </span>
                    <div class="data_text">
                    <span class="name">
                        <?php
                        if(isset($_SESSION['level_id'])){
                            echo $_SESSION['user_name'];
                        }else{
                            echo 'Guest';
                        }
                        ?>
                    </span>
                </a>
                </div>
            </div>
        </nav>
        <!-- Navbar -->
        <nav class="navbar flex">
            <i class="bx bx-menu" id="sidebar-open"></i>
            <form action="../search_result.php" method="GET" class="search_form">
                <input type="text" class="search_box" name="search" placeholder="Judul / #tag / username" id="searchInput"/>
                <input type="submit" value="Search" class="search_button">
            </form>
            
            <span class="nav_image">
            <a href="<?php
            if(isset($_SESSION['level_id'])){
                echo "../profile.php?user_name=", $_SESSION['user_name'];
            }else{
                echo "#";
            }
            ?>">
                <img src="<?php
                if(isset($_SESSION['level_id'])){
                    echo '../../storage/profile/' . $_SESSION['user_profile_path'];
                }else{
                    echo '../../storage/profile/default.png';
                }
                ?>" alt="logo_img" />
            </a>
            </span> 
        </nav>
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
        <script src="../../script/preview-img.js"></script>
        <script src="../../script/alert-time.js"></script>
    </body>
</html>
