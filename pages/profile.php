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
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <meta name="description" content="Halaman profil pengguna" />
    <meta property="og:title" content="<?php echo $name; ?>" />
    <meta property="og:url" content="https://miawshare.my.id/pages/profile.php?user_name=<?php echo $user_name; ?>" />
    <meta property="og:description" content="<?php echo $user_bio; ?>" />
    <meta property="og:image" content="https://miawshare.my.id/storage/profile/<?php echo $user_profile_path; ?>" />
    <link rel="stylesheet" href="../styles/profil.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">

    <!-- Boxicons CSS -->
    <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    </head>
    
    <body>
        <!-- Profil Pengguna -->
    <nav class="user-profile">
        <div class="profile-picture">
          <img src="../storage/profile/<?php echo $user_profile_path; ?>" class="info-img" alt="<?php echo $user_name; ?>" />
          <h1><?php echo $name; ?></h1>
          <p class="info-bio"><i><?php echo $user_bio; ?></i></p>
          <p class="info-username">@<?php echo $user_name; ?></p>
          <nav>
              <ul>
                <?php
                if (isset($_SESSION['user_id'])) {
                    if ($_SESSION['user_id'] == $user_id) {
                        ?>
                        <li><a href="crud/edit_profile.php">Edit Profil</a></li>
                        <?php
                    }
                    }
                ?>
              <li><a href="" id="copyButton">Bagikan</a></li>
            </ul>
          </nav>
          </div>
            <?php
                if ($level_id == 1) {
                    ?>
                    <div class="info">
                        <i class='bx bx-cog' ></i>
                        <p>Admin</p>
                    </div>
                    <?php
                } elseif ($level_id == 2) {
                    ?>
                    <div class="info">
                        <p>Pengguna MiawShare</p>
                    </div>
                    <?php
                }
            ?>
      </nav>
      
      <?php
        if($totalPost > 0){
            ?>
            <div class="pembuatan">   
            <p>Diupload</p>
            </div>
            <div class="gallery">
            <?php
            while($post = mysqli_fetch_assoc($getPostResult)){
                ?>
                <div class="box">
                    <a href="<?php echo 'view_img.php?post_id=' . $post['post_id']; ?>">        
                        <img src="../storage/posting/<?php echo $post['post_img_path'] ?>" alt="<?php echo $post['post_title']; ?>">
                    </a>
                </div>
                <?php
            }
        }else{
            ?>
            <div class="pembuatan">   
            <p>Belum membuat postingan</p>
            </div>
            <?php
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
    <script src="../script/script.js" defer></script>
    <script src="../script/copy-to-clipboard.js"></script>
  </body>
</html>