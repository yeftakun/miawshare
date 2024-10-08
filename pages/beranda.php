<?php
session_start();
include '../koneksi.php';
include '../environment.php';

$limit_beranda = LIMIT_BERANDA;

// Query untuk mengambil data gambar dan informasi pengguna
$query = "SELECT users.user_name, users.name, users.user_profile_path, posts.post_id, posts.post_img_path, posts.post_title, posts.classify, posts.create_in 
          FROM posts 
          JOIN users ON posts.user_id = users.user_id 
          ORDER BY posts.create_in DESC 
          LIMIT $limit_beranda";
$result = mysqli_query($koneksi, $query);

?>

<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Beranda</title>
            <meta name="description" content="Bagikan gambarmu di MiawShare!" />
            <meta property="og:title" content="MiawShare - Bagikan Gambar" />
            <meta property="og:url" content="https://miawshare.my.id/pages/beranda.php" />
            <meta property="og:description" content="Upload dan dapatkan gambar! UwU" />
            <meta property="og:image" content="https://miawshare.my.id/assets/logo/logo.png" />
            <link rel="stylesheet" href="../styles/style.css" />
            <link rel="icon" type="image/png" href="../assets/logo/logo.png" />
            <link rel="stylesheet" href="../styles/alert.css">
            <!-- Boxicons CSS -->
            <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
            <script src="../script/script.js" defer></script>
            <style>
                .blurred {
                    filter: blur(25px); /* Membuat gambar blur */
                    pointer-events: none; /* Agar tidak bisa di klik langsung */
                }
                .hide-icon {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    color: #fff;
                    font-size: 24px;
                    background-color: rgba(0, 0, 0, 0.3);
                    padding: 10px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 2;
                }
            </style>
        </head>
    <body>
        <br>
        <br>
        <br>
        <!-- Mendapatkan pesan -->
         <?php
        if(isset($_GET['pesan'])){
            if($_GET['pesan'] == 'uploadsuccess'){
                echo "<div class='done alert-danger'>Berhasil mengupload postingan</div>";
            }elseif($_GET['pesan'] == 'nsfw'){
                echo "<div class='alert alert-danger'>Postingan anda terdeteksi NSFW - Postingan akan disembunyikan.</div>";
            }
        }
        ?>
        <div class="container-parent-again">
            <div class="container">
    
            <!-- Menampilkan semua gambar di div.box -->
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Jika gambar terdeteksi NSFW, maka gambar akan blur dan menampilkan ikon hide kecuali untuk admin
                    if (($row['classify'] == 'nsfw')) {
                        if (!isset($_SESSION['level_id']) || $_SESSION['level_id'] != 1){
                            continue;
                        }else{
                        ?>
                            <div class="box">
                                <a href="<?php echo 'view_img.php?post_id=' . $row['post_id']; ?>">
                                    <img class="blurred" src="../storage/posting/<?php echo $row['post_img_path']; ?>" alt="<?php echo $row['post_title']; ?>">
                                    <div class="overlay">
                                        <div class="overlay-content">
                                            <div class="title"><?php echo $row['post_title']; ?></div>
                                            <div class="user-info">
                                                <img src="../storage/profile/<?php echo $row['user_profile_path']; ?>" alt="Profile Picture">
                                                <span><?php echo $row['user_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hide-icon">
                                        <i class='bx bx-hide'></i>
                                    </div>
                                </a>
                            </div>
                        <?php
                        }
                    } else {
                        // Tampilan normal untuk gambar SFW atau admin
                        ?>
                            <div class="box">
                                <a href="<?php echo 'view_img.php?post_id=' . $row['post_id']; ?>">
                                    <img src="../storage/posting/<?php echo $row['post_img_path']; ?>" alt="<?php echo $row['post_title']; ?>">
                                    <div class="overlay">
                                        <div class="overlay-content">
                                            <div class="title"><?php echo $row['post_title']; ?></div>
                                            <div class="user-info">
                                                <img src="../storage/profile/<?php echo $row['user_profile_path']; ?>" alt="Profile Picture">
                                                <span><?php echo $row['user_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php
                    }
                }
            } else {
                echo "Tidak ada data gambar.";
            }            
            mysqli_free_result($result);
            mysqli_close($koneksi);
            ?>
    
            </div>
        </div>

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
                    <!-- <a href="#" class="link flex">
                    <i class="bx bx-cloud-upload"></i>
                    <span>Upload New</span>
                    </a> -->
                </li>
                </ul>
            <?php
            }?>

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
        </body>
        <script src="../script/alert-time.js"></script>
  </html>