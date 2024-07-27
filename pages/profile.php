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

    // Query untuk mendapatkan total like dari user
    $getTotalLikeQuery = "SELECT SUM(total_likes) AS total_likes_sum FROM (
                            SELECT COUNT(l.liked_post_id) AS total_likes FROM posts p
                            LEFT JOIN likes l ON p.post_id = l.liked_post_id
                            JOIN users u ON p.user_id = u.user_id
                            WHERE u.user_name = '$user_name'
                            GROUP BY p.post_id
                        ) AS likes_summary";
    $getTotalLikeResult = mysqli_query($koneksi, $getTotalLikeQuery);
    $totalLike = mysqli_fetch_assoc($getTotalLikeResult);
    // pastikan ada hasil dari query total like
    // if(mysqli_num_rows($getTotalLikeResult) > 0) {
    //     $totalLikes = $totalLike['total_likes_sum'];
    // } else {
    //     $totalLikes = "0";
    // }
    if($totalLike['total_likes_sum'] == null){
        $totalLikes = 0;
    }else{
        $totalLikes = $totalLike['total_likes_sum'];
    }

    // Query untuk total post yang diupload pengguna
    $getTotalPostQuery = "SELECT COUNT(post_id) AS total_post FROM posts WHERE user_id = '$user_id'";
    $getTotalPostResult = mysqli_query($koneksi, $getTotalPostQuery);
    $totalPost = mysqli_fetch_assoc($getTotalPostResult);
    
    // pastikan ada hasil dari query total post
    if($totalPost['total_post'] == null){
        $totalPosting = 0;
    }else{
        $totalPosting = $totalPost['total_post'];
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
    <link rel="stylesheet" href="../styles/alert.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <link rel="stylesheet" href="../styles/modal-view-img.css">

    <!-- Boxicons CSS -->
    <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    </head>
    
    <body>
        <?php
        if(isset($_GET['pesan'])){
            if($_GET['pesan']=="success_block"){
                echo "<div class='done'>Pergguna berhasil di batasi</div>";
            }
        }
        ?>
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
                    <!-- <div class="statistic-user">
                        <nav>
                            <ul>
                                <li><p><b>Post: <?php echo $totalPosting; ?></b></p></li>
                                <li><p><b>Likes: <?php echo $totalLikes; ?></p></b></li>
                            </ul>
                        </nav>
                    </div> -->
                    <div class="statistic-user">
                        <p><b>Post: <?php echo $totalPosting; ?></b></p>
                        <p><b>Likes: <?php echo $totalLikes; ?></p></b>
                    </div>
                    <?php
                    // Jika yang melihat admin, tampilkan tombol blokir
                    if ($status == 'Suspended'){
                        ?>
                        <p style="color: red;">Pengguna dibatasi</p>
                        <?php
                        if (isset($_SESSION['level_id']) && $_SESSION['level_id'] == 1) {
                            ?>
                            <!-- <a href="crud/block_user.php?user_id=<?php echo $user_id; ?>" class="block-button">Blokir Pengguna</a> -->
                            <button class="unblockButton" onclick="showConfirmationUnBlockModal()">Aktifkan</button>
                            <?php
                        }
                    }else{
                        if (isset($_SESSION['level_id']) && $_SESSION['level_id'] == 1) {
                            ?>
                            <!-- <a href="crud/block_user.php?user_id=<?php echo $user_id; ?>" class="block-button">Blokir Pengguna</a> -->
                            <button class="blockButton" onclick="showConfirmationBlockModal()">Batasi Pengguna</button>
                            <?php
                        }
                    }
                    ?>
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
        <!-- Modal container -->
          <div id="modalContainerBlock" class="modal-container" style="display: none;">
              <!-- Modal konfirmasi penghapusan -->
              <div id="confirmationModal" class="modal">
                  <div class="modal-content">
                      <!-- <span class="close" onclick="closeConfirmationModal()">&times;</span> -->
                      <p>Membatasi pengguna?</p>
                      <div class="button-container">
                          <button class="blockButton" onclick="blockUserButton()">Ya</button>
                      </div>
                  </div>
              </div>
          </div>

          <div id="modalContainerUnBlock" class="modal-container" style="display: none;">
              <!-- Modal konfirmasi penghapusan -->
              <div id="confirmationModal" class="modal">
                  <div class="modal-content">
                      <!-- <span class="close" onclick="closeConfirmationModal()">&times;</span> -->
                      <p>Mengaktifkan pengguna?</p>
                      <div class="button-container">
                          <button class="blockButton" onclick="unblockUserButton()">Ya</button>
                      </div>
                  </div>
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
                    <?php
                    // Mengunduh semua postingan yang sudah pengguna buat --> ke file crud/download.php
                    if($_SESSION['level_id'] == 2){
                        ?>
                        <li class="item">
                            <a href="another.php" class="link flex">
                            <i class="bx bxs-cog"></i>
                            <span>Lainnya</span>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
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
    <script>
        // Fungsi untuk menampilkan modal konfirmasi
        function showConfirmationBlockModal() {
            // Periksa apakah variabel row sudah didefinisikan
            if (typeof <?php echo json_encode($userData); ?> !== 'undefined') {
                var modalContainerBlock = document.getElementById("modalContainerBlock");
                modalContainerBlock.style.display = "flex"; // Tampilkan modal container
            }
        }

        function showConfirmationUnBlockModal() {
            // Periksa apakah variabel row sudah didefinisikan
            if (typeof <?php echo json_encode($userData); ?> !== 'undefined') {
                var modalContainerUnBlock = document.getElementById("modalContainerUnBlock");
                modalContainerUnBlock.style.display = "flex"; // Tampilkan modal container
            }
        }

        // Menutup modal saat diklik di luar modal
        window.onclick = function(event) {
            var modalContainerBlock = document.getElementById("modalContainerBlock");
            if (event.target == modalContainerBlock) {
                modalContainerBlock.style.display = "none";
            }
        }
        // Menutup modal saat diklik di luar modal
        window.onclick = function(event) {
            var modalContainerUnBlock = document.getElementById("modalContainerUnBlock");
            if (event.target == modalContainerUnBlock) {
                modalContainerUnBlock.style.display = "none";
            }
        }
        function blockUserButton(){
            window.location.href = "crud/block_user.php?user_name=<?php echo $user_name; ?>";
        }
        function unblockUserButton(){
            window.location.href = "crud/unblock_user.php?user_name=<?php echo $user_name; ?>";
        }
    </script>
    <script src="../script/script.js" defer></script>
    <script src="../script/copy-to-clipboard.js"></script>
    <script src="../script/alert-time.js"></script>
  </body>
</html>