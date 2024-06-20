<?php
session_start();
$lama = '';
include '../koneksi.php';

// Query untuk mengambil data gambar dan informasi pengguna
if (isset($_GET['post_id'])) { // jika header ada parameter post_id
    $postId = mysqli_real_escape_string($koneksi, $_GET['post_id']);
    $query = "SELECT users.user_name, users.name, users.user_profile_path, posts.* 
              FROM posts 
              JOIN users ON posts.user_id = users.user_id 
              WHERE post_id = '$postId'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $queryTime = "SELECT current_timestamp() as timenow;";
        $resultTime = mysqli_query($koneksi, $queryTime);
        $rowTime = mysqli_fetch_assoc($resultTime);

        // Waktu posting
        $postingTime = strtotime($row['create_in']);

        // Waktu saat ini
        $currentTime = strtotime($rowTime['timenow']);

        // Hitung selisih waktu dalam detik
        $timeDifference = $currentTime - $postingTime;

        // Ubah selisih waktu menjadi format yang diinginkan
        if ($timeDifference < 60) {
            // Baru saja
            $timeAgo = "Baru saja";
        } elseif ($timeDifference < 3600) {
            // Tampilkan hanya dalam menit
            $minutes = floor($timeDifference / 60);
            $timeAgo = "$minutes mnt";
        } elseif ($timeDifference < 86400) {
            // Tampilkan hanya dalam jam
            $hours = floor($timeDifference / 3600);
            $timeAgo = "$hours j";
        } elseif ($timeDifference < 2592000) {
            // Tampilkan hanya dalam hari
            $days = floor($timeDifference / 86400);
            $timeAgo = "$days hri";
        } elseif ($timeDifference < 31536000) {
            // Tampilkan hanya dalam bulan
            $months = floor($timeDifference / 2592000);
            $timeAgo = "$months bln";
        } else {
            // Tampilkan hanya dalam tahun
            $years = floor($timeDifference / 31536000);
            $timeAgo = "$years thn";
        }

        ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>View Image</title>
    <meta name="description" content="Lihat gambar yang diupload pengguna kami!" />
    <meta property="og:title" content="<?php echo $row['post_title']; ?> - <?php echo $row['user_name']; ?>" />
    <meta property="og:url" content="https://miawshare.my.id/pages/view_img.php?post_id=<?php echo $row['post_id']; ?>" />
    <meta property="og:description" content="<?php echo $row['post_description']; ?>" />
    <meta property="og:image" content="https://miawshare.my.id/storage/posting/<?php echo $row['post_img_path']; ?>" />
    <!-- <link rel="stylesheet" href="../css/style.css"> -->
    <link rel="stylesheet" href="../styles/alert.css">
    <link rel="stylesheet" href="../styles/image.css">
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="stylesheet" href="../styles/modal-view-img.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png" />
    <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="../script/script.js" defer></script> 
</head>
<body>
    <br><br><br><br>

    <div class="container">
        <div class="content-card">
            <img src="../storage/posting/<?php echo $row['post_img_path']; ?>" alt="<?php echo $row['post_title']; ?>" class="content-image">
            <div class="card-details">
                <a href="<?php echo 'profile.php?user_name=' . $row['user_name']; ?>">
                <div class="user-info">
                        <img src="../storage/profile/<?php echo $row['user_profile_path']; ?>" alt="pp" class="user-photo">
                        <span class="username"><?php echo $row['user_name']; ?></span>
                    </div>
                </a>
                <h2 class="content-title"><?php echo $row['post_title']; ?></h2>
                <p class="content-description"><?php echo $row['post_description']; ?></p>
                <div class="button-group">
                    <?php
                    if (isset($_SESSION['user_name']) && $_SESSION['user_name'] == $row['user_name']) {
                        ?>
                        <button class="delete-button" onclick="showConfirmationModal()"><i class='bx bx-trash' ></i></button>
                        <?php
                    }
                    ?>
                    <a class="download-button" href="../storage/posting/<?php echo $row['post_img_path']; ?>" download>Download Image</a>
                    <button class="copy-link-button" id="copyButton"><i class='bx bx-link-alt'></i></button>
                </div>
            </div>
            <button class="undo-button" id="undoButton"><i class='bx bxs-chevron-left'></i></button>
        </div>
    </div>

    <!-- Modal container -->
    <div id="modalContainer" class="modal-container" style="display: none;">
        <!-- Modal konfirmasi penghapusan -->
        <div id="confirmationModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeConfirmationModal()">&times;</span>
                <p>Apakah Anda yakin ingin menghapus gambar ini?</p>
                <div class="button-container">
                    <button onclick="deleteImage()">Ya</button>
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
                if (isset($_SESSION['level_id'])) {
                ?>
                <ul class="menu_item">
                    <div class="menu_title flex">
                        <span class="title">Tools</span>
                        <span class="line"></span>
                    </div>  
                    <li class="item">
                        <?php
                        if ($_SESSION['level_id'] == 1) {
                        ?>
                            <a href="admin_panel.php" class="link flex">
                                <i class="bx bx-cog"></i>
                                <span>Admin Panel</span>
                            </a>
                        <?php
                        } elseif ($_SESSION['level_id'] == 2) {
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
                    if (isset($_SESSION['level_id'])) {
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
                    } else {
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
                if (isset($_SESSION['level_id'])) {
                    echo "profile.php?user_name=", $_SESSION['user_name'];
                } else {
                    echo "#";
                }
                ?>">
                    <span class="nav_image">
                        <img src="
                        <?php
                        if (isset($_SESSION['level_id'])) {
                            echo '../storage/profile/' . $_SESSION['user_profile_path'];
                        } else {
                            echo '../storage/profile/default.png';
                        }
                        ?>" alt="logo_img" />
                    </span>
                    <div class="data_text">
                        <span class="name">
                        <?php
                        if (isset($_SESSION['level_id'])) {
                            echo $_SESSION['user_name'];
                        } else {
                            echo 'Guest';
                        }
                        ?>
                        </span>
                    </div>
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
            if (isset($_SESSION['level_id'])) {
                echo "profile.php?user_name=", $_SESSION['user_name'];
            } else {
                echo "#";
            }
            ?>">
                <img src="<?php
                if (isset($_SESSION['level_id'])) {
                    echo '../storage/profile/' . $_SESSION['user_profile_path'];
                } else {
                    echo '../storage/profile/default.png';
                }
                ?>" alt="logo_img" />
            </a>
        </span>
    </nav>
<?php
    } else {
        // tampilan jika tidak ada hasil yang ditemukan
        header("Location: error/not_found.php");
        exit();
    }
    mysqli_free_result($result);
    mysqli_close($koneksi);
} else { // jika header tidak ada parameter post_id
    header("Location: beranda.php");
    exit();
}
?>

<script>
    // Fungsi untuk menampilkan modal konfirmasi
    function showConfirmationModal() {
        // Periksa apakah variabel row sudah didefinisikan
        if (typeof <?php echo json_encode($row); ?> !== 'undefined') {
            var modalContainer = document.getElementById("modalContainer");
            modalContainer.style.display = "flex"; // Tampilkan modal container
        }
    }

    // Menutup modal saat diklik di luar modal
    window.onclick = function(event) {
        var modalContainer = document.getElementById("modalContainer");
        if (event.target == modalContainer) {
            modalContainer.style.display = "none";
        }
    }

    // Fungsi untuk menghapus gambar
    function deleteImage() {
        // Redirect ke delete_img.php dengan menyertakan parameter post_id
        window.location.href = "crud/delete_img.php?post_id=<?php echo $row['post_id']; ?>";
    }
</script>
<script>
    document.getElementById("undoButton").addEventListener("click", function() {
        window.history.back();
    });
</script>
<script src="../script/alert-time.js"></script>
<script src="../script/copy-to-clipboard.js"></script>

</body>
</html>
