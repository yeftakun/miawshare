<?php
session_start();
include '../../koneksi.php';
include '../../environment.php';

// Fungsi untuk memproses teks dan mengonversi URL menjadi tautan HTML
function convertUrlsToLinks($text) {
    // Regex untuk menemukan URL
    $pattern = '/(https?:\/\/[^\s]+)/';
    // Gantikan URL dengan tag <a>
    $replacement = '<a href="$1" target="_blank">$1</a>';
    // Proses teks dan kembalikan hasilnya
    return preg_replace($pattern, $replacement, $text);
}
function removeLinks($text) {
    // Regex untuk menemukan tag <a href=""></a>
    $pattern = '/<a href="(https?:\/\/[^\s]+)"[^>]*>(.*?)<\/a>/i';
    // Gantikan tag <a> dengan URL asli
    $replacement = '$1';
    // Proses teks dan kembalikan hasilnya
    return preg_replace($pattern, $replacement, $text);
}
function filterInput($input) {
    // Daftar tag HTML yang diizinkan (misalnya hanya <a> dan <b>)
    $allowedTags = '<a><b><i><strong><em><p><ul><li><ol><br><hr>';

    // Hapus semua tag HTML yang tidak diizinkan
    return strip_tags($input, $allowedTags);
}

// Memeriksa apakah pengguna telah login
if (!isset($_SESSION['user_id'])) {
    header("location:../../index.php?pesan=needlogin");
    exit(); // Stop further execution
}else{
    // Memeriksa apakah pengguna adalah user
    if ($_SESSION['level_id'] == 1) {
        header("location:../admin_panel.php");
        exit(); // Stop further execution
    }
}

// Mendapatkan data postingan yang akan diedit
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
    $query = "SELECT * FROM posts WHERE post_id = $post_id";
    $result = mysqli_query($koneksi, $query);
    $postData = mysqli_fetch_assoc($result);

    // Memeriksa apakah pengguna adalah pemilik postingan
    if ($postData['user_id'] != $_SESSION['user_id']) {
        header("location:../error/deniedpage.php");
        exit(); // Stop further execution
    }

    // memeriksa apakah postingan ada
    if (!$postData) {
        header("location:../error/not_found.php");
        exit(); // Stop further execution
    }

    // assign nilai ke variabel
    $post_title_old = $postData['post_title'];
    $post_description_old = $postData['post_description'];
    $post_description_old = removeLinks($post_description_old);
    $post_img_path_old = $postData['post_img_path'];
}

if (isset($_POST['submit'])) {
    $post_title = $_POST['post_title'];
    $post_description = $_POST['post_description'];

    $post_description = filterInput($post_description);
    $post_description = convertUrlsToLinks($post_description);

    $query = "UPDATE posts SET post_title = '$post_title', post_description = '$post_description' WHERE post_id = $post_id";
    $result = mysqli_query($koneksi, $query);
    if ($result) {
        header("location:../view_img.php?post_id=$post_id&pesan=updatepostsuccess");
    } else {
        header("location:../view_img.php?post_id=$post_id&pesan=updatepostfailed");
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
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 15px;
            position: relative;
            width: 100%;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .preview-container img {
            width: 100%;
            height: auto;
            max-height: 700px;
            /* display: none; */
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
    <br>
        <br>
    <!-- Main Content -->
    <main class="main_content">
        <!-- Upload Form -->
        <div class="upload_container">
            <form method="post" enctype="multipart/form-data" class="upload_form">
                <!-- Gambar (tidak perlu diganti, tampilkan saja) -->
                <!-- <div class="form_group">
                    <label for="image" class="upload_label">
                        <i class="bx bx-upload"></i>
                        <span>Select file to upload</span>
                    </label>
                    <input type="file" name="image" id="image" style="display: none;" required>
                </div> -->
                <div class="preview-container">
                    <img id="image-preview" src="../../storage/posting/<?php echo $post_img_path_old; ?>" alt="Post img">
                    <!-- <span class="preview-text">Masukan gambar</span> -->
                </div>
                <!-- Judul -->
                <div class="form_group">
                    <label for="post_title">Judul</label>
                    <input type="text" name="post_title" id="post_title" value="<?php echo $post_title_old; ?>">
                </div>
                <!-- Deskripsi/caption -->
                <div class="form_group">
                    <label for="post_description">Caption</label>
                    <textarea name="post_description" id="post_description" rows="4"><?php echo $post_description_old; ?></textarea>
                </div>
                <button type="submit" name="submit" class="upload_btn flex">
                    <i class="bx bx-cloud-upload"></i>
                    <span>Update Postingan</span>
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
        <!-- <script>
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
        </script> -->
        <!-- <script src="../../script/preview-img.js"></script> -->
        <script src="../../script/alert-time.js"></script>
    </body>
</html>
