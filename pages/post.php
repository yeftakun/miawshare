<?php
session_start();
include '../koneksi.php';

// get bot token
include '../environment.php';
// init nilai
$token = TOKEN_BOT;
$max_image_size = MAX_IMAGE_SIZE;
$size_in_kb = $max_image_size / 1000;
$errorMsg = '';

// Fungsi untuk memproses teks dan mengonversi URL menjadi tautan HTML
function convertUrlsToLinks($text) {
    // Regex untuk menemukan URL
    $pattern = '/(https?:\/\/[^\s]+)/';
    // Gantikan URL dengan tag <a>
    $replacement = '<a href="$1" target="_blank">$1</a>';
    // Proses teks dan kembalikan hasilnya
    return preg_replace($pattern, $replacement, $text);
}
function filterInput($input) {
    // Daftar tag HTML yang diizinkan (misalnya hanya <a> dan <b>)
    $allowedTags = '<a><b><i><strong><em><p><ul><li><ol><br><hr>';

    // Hapus semua tag HTML yang tidak diizinkan
    return strip_tags($input, $allowedTags);
}

// Cek kembali status user dengan mengupdate session dari database
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($koneksi, $query);
    $user = mysqli_fetch_assoc($result);
    $_SESSION['status'] = $user['status'];
}

// Ketika status user "Suspended" atau "Banned", redirect ke halaman error
if(isset($_SESSION['status'])){
    if($_SESSION['status'] == 'Suspended'){
        header("location:error/deniedpage.php");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // init dan ambil data dari form
    $post_title = $_POST['post_title'];
    $post_description = $_POST['post_description'];
    $post_link = $_POST['post_link'];

    // Filter deskripsi untuk membatasi tag HTML
    $post_description = filterInput($post_description);
    // Proses deskripsi untuk mengonversi URL menjadi tautan
    $post_description = convertUrlsToLinks($post_description);

    // ambil data dari session
    $user_id = $_SESSION['user_id'];
    $tele_chat_id = $_SESSION['tele_chat_id']; // untuk kirim notifikasi ke telegram pengupload
    
    // Init direktori
    $uploadDir = '../storage/posting/';
    
    
    
    // dapatkan ekstensi file
    function getFileExtension($filename) {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }
    
    // mendapatkan nama file tanpa ekstensi
    function getFileNameWithoutExtension($filename) {
        return pathinfo($filename, PATHINFO_FILENAME);
    }
    
    // Validasi 1: ukuran file
    if ($_FILES['image']['size'] > $max_image_size) {
        header("location:post.php?pesan=oversize");
        exit();
    } else  {
        // Validasi 2: ekstensi file
        if ($_FILES['image']['size'] > 0) {
            $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');
            $imageExtension = getFileExtension($_FILES['image']['name']);
            if (!in_array($imageExtension, $allowedExtensions)) {
                header("location:post.php?pesan=unsupportedfile");
                exit();
            }
        }
        
        // Proses upload gambar (gambar wajib diupload)
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
        // Untuk ke database, set $filePath hanya dengan nama file
        $filePath = basename($filePath);
        
        
        //  Proses input data ke database
        $insertQuery = "INSERT INTO posts (user_id, post_img_path, post_title, post_description, post_link) VALUES ('$user_id', '$filePath', '$post_title', '$post_description', '$post_link')";
        // eksekusi query
        mysqli_query($koneksi, $insertQuery);

        // NOTIFIKASI KE TELEGRAM DI DISABLE DULU
        // $getPostID = "SELECT post_id FROM posts WHERE post_img_path = '$filePath'";
        // $sharedLink = "https://hostIP/pinterest-kw/pages/view_post.php?post_id=$getPostID";
        // Init tele API dan kirim
        // $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$tele_chat_id&text=Gambar%20telah%20berhasil%20diupload!%0A%0A*Judul*:%20$post_title%0A*Deskripsi*:%20$post_description%0A*Go%20to%20Post*:%20$sharedLink";
        
        
        // Kirim pesan ke Telegram
        // file_get_contents($telegramAPI);
        
        // redirect ke beranda jika PostIDnya ditemukan
        header("location:beranda.php?pesan=uploadsuccess");
        
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
    <link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="../styles/alert.css" />
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <!-- <link rel="stylesheet" href="../styles/style2.css" /> -->
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="../script/script.js" defer></script>
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
    </style>
  </head>
  <body>
    <?php
    if(isset($_SESSION['level_id'])){
        if($_SESSION['level_id'] == 2){
            if(isset($_GET['pesan'])){
                if($_GET['pesan'] == 'oversize'){
                    echo "<div class='alert alert-danger'>Ukuran file terlalu besar. Maksimal $size_in_kb KB.</div>";
                }elseif($_GET['pesan'] == 'unsupportedfile'){
                    echo "<div class='alert alert-danger'>Format file tidak didukung. Hanya menerima file JPG, JPEG, PNG, dan GIF.</div>";
                }
            }
            // halaman posting khusus user
            ?>
            <!-- Main Content -->
            <main class="main_content">
              <!-- Upload Form -->
              <div class="upload_container">
                <form method="post" enctype="multipart/form-data" class="upload_form">
                  <div class="form_group">
                    <label for="post_title">Title</label>
                    <input type="text" name="post_title" id="post_title">
                  </div>
                  <div class="form_group">
                    <label for="post_description">Description</label>
                    <textarea name="post_description" id="post_description" rows="4" placeholder="Deskripsi gambar #tag1 #tag2 #tag3"></textarea>
                  </div>
                  <div class="form_group">
                    <label for="image" class="upload_label">
                      <i class="bx bx-upload"></i>
                      <span>Select file to upload</span>
                    </label>
                    <input type="file" name="image" id="image" style="display: none;" required>
                  </div>
                  <div class="preview-container">
                    <img id="image-preview" src="" alt="Image Preview">
                    <span class="preview-text">Masukan gambar</span>
                  </div>
                  <button type="submit" name="submit" class="upload_btn flex">
                    <i class="bx bx-cloud-upload"></i>
                    <span>Upload File</span>
                  </button>
                </form>
              </div>
            </main>
            <?php
        }elseif($_SESSION['level_id'] == 1){
            // jika level id bukan 2, redirect ke panel admin
            header("location:admin_panel.php");
        }

    }else{
        // jika belum login, redirect ke halaman login
        header("location:../index.php?pesan=needlogin");
    }
    ?>
    <!-- Sidebar -->
    <nav class="sidebar locked">
        <div class="logo_items flex">
            <span class="nav_image">
                <img src="../assets/logo/logo.png" alt="logo_img" />
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
                        <a href="beranda.php" class="link flex">
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
                        <a href="admin_panel.php" class="link flex">
                        <i class="bx bx-cog"></i>
                        <span>Admin Panel</span>
                        </a>
                    <?php
                    } elseif($_SESSION['level_id'] == 2) {
                    ?>
                        <a href="post.php" class="link flex">
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
                        <a href="crud/edit_profile.php" class="link flex">
                        <i class="bx bx-user"></i>
                        <span>Edit User</span>
                        </a>
                    </li>
                    <li class="item">
                        <a href="../logout.php" class="link flex">
                        <i class="bx bx-log-out"></i>
                        <span>Log out</span>
                        </a>
                    </li>
                    <?php
                }else{
                    ?>
                    <li class="item">
                        <a href="../index.php" class="link flex">
                        <i class="bx bx-log-in"></i>
                        <span>Login</span>
                        </a>
                    </li>
                    <?php
                }
                ?>
                <li class="item">
                    <a href="aboutus.php" class="link flex">
                    <i class="bx bx-flag"></i>
                    <span>About Us</span>
                    </a>
                </li>
                </ul>
            </div>

            <div class="sidebar_profile flex">
                <a href="<?php
                if(isset($_SESSION['level_id'])){
                    echo "profile.php?user_name=", $_SESSION['user_name'];
                }else{
                    echo "#";
                }
                ?>">
                    <span class="nav_image">
                    <img src="
                    <?php
                    if(isset($_SESSION['level_id'])){
                        echo '../storage/profile/' . $_SESSION['user_profile_path'];
                    }else{
                        echo '../storage/profile/default.png';
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
            <form action="search_result.php" method="GET" class="search_form">
                <input type="text" class="search_box" name="search" placeholder="Judul / #tag / username" id="searchInput"/>
                <input type="submit" value="Search" class="search_button">
            </form>
            
            <span class="nav_image">
            <a href="<?php
            if(isset($_SESSION['level_id'])){
                echo "profile.php?user_name=", $_SESSION['user_name'];
            }else{
                echo "#";
            }
            ?>">
                <img src="<?php
                if(isset($_SESSION['level_id'])){
                    echo '../storage/profile/' . $_SESSION['user_profile_path'];
                }else{
                    echo '../storage/profile/default.png';
                }
                ?>" alt="logo_img" />
            </a>
            </span> 
        </nav>
        <script>
        // Function to preview the selected image
        function previewImage() {
            var preview = document.querySelector('#image-preview');
            var file = document.querySelector('input[type=file]').files[0];
            var reader = new FileReader();

            reader.onloadend = function () {
                preview.src = reader.result;
                preview.style.display = 'block'; // Show the preview image
                document.querySelector('.preview-text').style.display = 'none'; // Hide the preview text
            }

            if (file) {
                reader.readAsDataURL(file); // Read the file as a data URL
            } else {
                preview.src = ''; // Clear the preview if no file is selected
                preview.style.display = 'none'; // Hide the preview image
                document.querySelector('.preview-text').style.display = 'block'; // Show the preview text
            }
        }

        // Event listener for file input change
        document.querySelector('input[type=file]').addEventListener('change', previewImage);
        </script>
        <script src="../script/preview-img.js"></script>
        <script src="../script/alert-time.js"></script>
    </body>
</html>
