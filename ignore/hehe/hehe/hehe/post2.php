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

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // init dan ambil data dari form
    $post_title = $_POST['post_title'];
    $post_description = $_POST['post_description'];
    $post_link = $_POST['post_link'];

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
<html>
    <head>
        <title>Beranda</title>
        <link rel="stylesheet" type="text/css" href="../styles/style.css">
        <link rel="stylesheet" type="text/css" href="../styles/alert.css">
        <link rel="icon" type="image/png" href="../assets/ico/HitoriGotou.ico">
    </head>
    <body>
        
        <?php
if(isset($_SESSION['level_id'])) {
    if($_SESSION['level_id'] == 2) {
        // halaman posting user
        ?>
        <!-- header -->
        <header>
            <div class="logo">
                <img src="../assets/ico/HitoriGotou.ico" alt="logo" width="50">
            </div>
            <div class="home-search-bar">
                <form action="search_result.php" method="GET">
                    <input type="text" name="search" id="searchInput" placeholder="Judul / #tag / username">
                    <input type="submit" value="Search">
                </form>
            </div>
            <div class="nav-to">
                <p><a href="beranda.php">Beranda</a></p>
            </div>
            <div class="nav-to">
                <p><a href="post.php">Posting</a></p>
            </div>
            <div class="profile-pic">
                <a href="profile.php?user_name=<?php echo $_SESSION['user_name']; ?>">
                    <?php
                    echo '<img src="../storage/profile/' . $_SESSION['user_profile_path'] . '" alt="' . $_SESSION['user_profile_path'] . '" width="50px"';
                    ?>
                </a>
            </div>
            <div class="logout">
                <a href="../logout.php">LOGOUT</a>
            </div>
        </header>
        
        <!-- content -->
        <h1>Upload</h1>
        <?php
        if(isset($_GET['pesan'])){
            if($_GET['pesan']=="oversize"){
                echo "<div class='alert'>Ukuran gambar melebihi $size_in_kb KB</div>";
            }
        }
        ?>
        <div class="form">
            <form method="POST" enctype="multipart/form-data" class="form">
                <!-- Tampilkan pesan kesalahan jika ada -->
                <?php if (!empty($errorMsg)) { ?>
                    <div class="alert"><?php echo $errorMsg; ?></div>
                    <?php 
                } ?>

                <label for="image" class="uploadimg">Upload Gambar</label>
                <input type="file" id="image" name="image" accept="image/*">
                <img id="image-preview" src="#" alt="image-preview" style="display: none;"> 
                <label for="post_title">Judul</label>
                <input type="text" id="post_title" name="post_title">
            
                <label for="post_description">Deskripsi</label>
                <textarea id="post_description" name="post_description" placeholder="Deskripsi gambar #tag1 #tag2 #tag3" rows="5"></textarea>
            
                <label for="post_link">Link</label>
                <input type="text" id="post_link" name="post_link">
            
                <button class="button" type="submit">Upload</button>
        </form>
    </div>

<?php
    } elseif($_SESSION['level_id'] == 1) {
        header("location:admin_panel.php");
    }
} else {
    header("location:error/index.php?pesan=needlogin");
}
?>
    <br/>
    <script src="../script/preview-img.js"></script>
    <script src="../script/alert-time.js"></script>
</body>
</html>