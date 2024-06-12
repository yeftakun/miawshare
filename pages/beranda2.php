<?php
session_start();
include '../koneksi.php';

// Query untuk mengambil data gambar dan informasi pengguna
$query = "SELECT users.user_name, users.name, users.user_profile_path, posts.post_id, posts.post_img_path, posts.post_title, posts.create_in 
          FROM posts 
          JOIN users ON posts.user_id = users.user_id 
          ORDER BY posts.create_in DESC 
          LIMIT 20";
$result = mysqli_query($koneksi, $query);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Beranda</title>
	<link rel="stylesheet" type="text/css" href="../styles/style.css">
	<link rel="stylesheet" type="text/css" href="../styles/alert.css">
	<link rel="icon" type="image/png" href="../assets/logo/logo.png">
</head>
<body>
    
    <header>
        <div class="logo">
            <img src="../assets/logo/logo.png" alt="logo" width="50">
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
            <?php
            if(isset($_SESSION['level_id'])) {
                if($_SESSION['level_id'] == 1) {
                ?>
                    <p><a href="admin_panel.php">Admin Panel</a></p>
                <?php
                } elseif($_SESSION['level_id'] == 2) {
                ?>
                    <p><a href="post.php">Posting</a></p>
                <?php
                }
            }
            ?>
        </div>
        <?php
        if(isset($_SESSION['level_id'])){
            ?>
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
            <?php
        }else{
            ?>
            <div class="nav-to">
                <p>
                    <a href="../index.php">LOGIN</a>
                </p>
            </div>
            <?php
        }
        ?>
    </header>
    <p>Unggahan Terbaru</p>

    <?php
    if (mysqli_num_rows($result) > 0) {
        // Loop untuk menampilkan setiap data gambar dan informasi pengguna
        while ($row = mysqli_fetch_assoc($result)) {
            ?>
            <div class="container">
                <div class="box">
                    <!-- Tampilkan gambar dari direktori "../storage/posting/" -->
                    <div class="img-preview">
                        <a href="<?php echo 'view_img.php?post_id=' . $row['post_id']; ?>">
                            <img src="../storage/posting/<?php echo $row['post_img_path']; ?>" alt="<?php echo $row['post_title']; ?>">
                            <p><?php echo $row['post_title']; ?></p>
                        </a>
                    </div>
                    <div class="posted-by">
                        <!-- Tampilkan foto profil dari direktori "../storage/profile/" -->
                        <a href="<?php echo 'profile.php?user_name=' . $row['user_name']; ?>">
                            <img src="../storage/profile/<?php echo $row['user_profile_path']; ?>" alt="pp" width="50px">
                            <p><?php echo $row['name']; ?></p>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "Tidak ada data gambar.";
    }

    // Bebaskan hasil query
    mysqli_free_result($result);

    // Tutup koneksi ke database
    mysqli_close($koneksi);
    ?>

	<br/>
	<br/>

    <script src="../script/preview-img.js"></script>
    <script src="../script/alert-time.js"></script>

</body>
</html>