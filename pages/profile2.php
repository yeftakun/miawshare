<?php
// Include koneksi ke database
include '../koneksi.php';
session_start();

// Ambil nama pengguna dari URL
if(isset($_GET['user_name'])) {
    $user_name = $_GET['user_name'];
    
    // Query untuk mengambil informasi pengguna berdasarkan user_name
    $getUserQuery = "SELECT * FROM users WHERE user_name = '$user_name'";
    $getUserResult = mysqli_query($koneksi, $getUserQuery);

    // Pastikan ada hasil yang ditemukan
    if(mysqli_num_rows($getUserResult) > 0) {
        $userData = mysqli_fetch_assoc($getUserResult);
        
        // Assign data pengguna ke variabel
        $user_id = $userData['user_id'];
        $user_name = $userData['user_name'];
        $name = $userData['name'];
        $user_profile_path = $userData['user_profile_path'];
        $user_bio = $userData['user_bio'];
        $level_id = $userData['level_id'];
        $password = $userData['password'];
        $status = $userData['status'];
        $tele_chat_id = $userData['tele_chat_id'];

        // Query untuk mengambil data postingan pengguna
        $getPostQuery = "SELECT * FROM posts WHERE user_id = '$user_id' ORDER BY create_in DESC";
        $getPostResult = mysqli_query($koneksi, $getPostQuery);
        $totalPost = mysqli_num_rows($getPostResult);

    } else {
        // Redirect ke halaman lain jika pengguna tidak ditemukan
        header("Location: error/not_found.php");
        exit();
    }
} else {
    // Redirect ke halaman lain jika user_name tidak disediakan
    // header("Location: error/not_found.php");
    header("Location: profile.php?user_name=$_SESSION[user_name]");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
</head>
<body>
    <?php
    // Periksa apakah pengguna sudah login
    if(isset($_SESSION['user_id'])) {
        if(isset($_SESSION['user_name']) && $_SESSION['user_name'] !== $user_name)
 {
            ?>
            <!-- tampilan pengguna lain -->
            <p><a href="beranda.php">Beranda</a> | <a href="#" id="copyButton">Share</a></p>
            <p>Informasi Pengguna:</p>
            <img src="../storage/profile/<?php echo $user_profile_path; ?>" alt="<?php echo $user_profile_path; ?>" max-width="300px">
            <ul>
                <li>User ID : <?php echo $user_id; ?></li>
                <li>User Name : <?php echo $user_name; ?></li>
                <li>Nama : <?php echo $name; ?></li>
                <li>Bio : <?php echo $user_bio; ?></li>
                <li>Level ID : <?php echo $level_id; ?></li>
                <li>Password : <?php echo $password; ?></li>
                <li>Status : <?php echo $status; ?></li>
                <li>Chat ID : <?php echo $tele_chat_id; ?></li>
            </ul>

            <h3>Dibuat oleh <?php echo $name ?>:</h3>
            <?php
            
            // Tampilkan postingan pengguna
            if($totalPost > 0) {
                while($post = mysqli_fetch_assoc($getPostResult)) {
                    ?>
                    <div class="container">
                        <div class="post">
                            <a href="<?php echo 'view_img.php?post_id=' . $post['post_id']; ?>">
                                <img src="../storage/posting/<?php echo $post['post_img_path']; ?>" alt="<?php echo $post['post_title']; ?>" max-width="300px">
                                <p><?php echo $post['post_title']; ?></p>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p><?php echo $name ?> ini belum membuat postingan</p>
                <?php
            }
            ?>
            <?php
        } else {
            ?>
            <!-- tampilan untuk pengguna itu sendiri -->
            <p><a href="beranda.php">Beranda</a> | <a href="#" id="copyButton">Share</a> | <a href="crud/edit_profile.php">Edit Profil</a></p>
            <p>Informasi Saya:</p>
            <img src="../storage/profile/<?php echo $user_profile_path; ?>" alt="<?php echo $user_profile_path; ?>" max-width="300px">
            <ul>
                <li>User ID : <?php echo $user_id; ?></li>
                <li>User Name : <?php echo $user_name; ?></li>
                <li>Nama : <?php echo $name; ?></li>
                <li>Bio : <?php echo $user_bio; ?></li>
                <li>Level ID : <?php echo $_SESSION['level_id']; ?></li>
                <li>Password : <?php echo $password; ?></li>
                <li>Status : <?php echo $status; ?></li>
                <li>Chat ID : <?php echo $tele_chat_id; ?></li>
            </ul>

            <h3>Dibuat oleh Anda</h3>
            <?php
            
            // Tampilkan postingan pengguna
            if($totalPost > 0) {
                while($post = mysqli_fetch_assoc($getPostResult)) {
                    ?>
                    <div class="container">
                        <div class="post">
                            <a href="<?php echo 'view_img.php?post_id=' . $post['post_id']; ?>">
                                <img src="../storage/posting/<?php echo $post['post_img_path']; ?>" alt="<?php echo $post['post_title']; ?>" max-width="300px">
                                <p><?php echo $post['post_title']; ?></p>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>Anda belum membuat postingan</p>
                <?php
            }
            ?>
        <?php
        }

    } else {
        // tampilan pengguna lain (tapi belum login)
        // Pengguna belum login, tampilkan link login
        ?>
        <p><a href="beranda.php">Beranda</a> | <a href="#" id="copyButton">Share</a> | <a href="../index.php">Login</a></p>
        <p>Informasi Pengguna:</p>
        <img src="../storage/profile/<?php echo $user_profile_path; ?>" alt="<?php echo $user_profile_path; ?>" max-width="300px">
        <ul>
            <li>User ID : <?php echo $user_id; ?></li>
            <li>User Name : <?php echo $user_name; ?></li>
            <li>Nama : <?php echo $name; ?></li>
            <li>Bio : <?php echo $user_bio; ?></li>
            <li>Level ID : <?php echo $level_id; ?></li>
            <li>Password : <?php echo $password; ?></li>
            <li>Status : <?php echo $status; ?></li>
            <li>Chat ID : <?php echo $tele_chat_id; ?></li>
        </ul>

        <h3>Dibuat oleh <?php echo $name ?>:</h3>
            <?php
            
            // Tampilkan postingan pengguna
            if($totalPost > 0) {
                while($post = mysqli_fetch_assoc($getPostResult)) {
                    ?>
                    <div class="container">
                        <div class="post">
                            <a href="<?php echo 'view_img.php?post_id=' . $post['post_id']; ?>">
                                <img src="../storage/posting/<?php echo $post['post_img_path']; ?>" alt="<?php echo $post['post_title']; ?>" max-width="300px">
                                <p><?php echo $post['post_title']; ?></p>
                            </a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p><?php echo $name ?> ini belum membuat postingan</p>
                <?php
            }
            ?>
        <?php
    }
    ?>

    <script src="../script/alert-time.js"></script>
    <script src="../script/copy-to-clipboard.js"></script>
</body>
</html>
