<?php
session_start();
$lama = '';
include '../koneksi.php';

// Memeriksa apakah pengguna sudah melaporkan postingan ini sebelumnya
$isReported = false;
if (isset($_SESSION['user_name']) && isset($_GET['post_id'])) {
    $post_id_reported = mysqli_real_escape_string($koneksi, $_GET['post_id']);
    $user_name_reporter = mysqli_real_escape_string($koneksi, $_SESSION['user_name']);
    
    $queryCheckReport = "SELECT 1 FROM reports WHERE post_id_reported = ? AND user_name_reporter = ?";
    $stmtCheck = mysqli_prepare($koneksi, $queryCheckReport);
    if ($stmtCheck) {
        mysqli_stmt_bind_param($stmtCheck, 'ss', $post_id_reported, $user_name_reporter);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_store_result($stmtCheck);
        if (mysqli_stmt_num_rows($stmtCheck) > 0) {
            $isReported = true;
        }
        mysqli_stmt_close($stmtCheck);
    }
}

// Tangani pengiriman laporan
if (isset($_POST['action']) && $_POST['action'] === 'report') {
    if ($isReported) {
        echo 'already_reported';
    } else {
        $user_name_reported = mysqli_real_escape_string($koneksi, $_POST['user_name_reported']);
        $post_id_reported = mysqli_real_escape_string($koneksi, $_POST['post_id_reported']);
        $post_reported = mysqli_real_escape_string($koneksi, $_POST['post_reported']);
        $user_name_reporter = mysqli_real_escape_string($koneksi, $_POST['user_name_reporter']);

        // Siapkan query untuk memasukkan laporan
        $query = "INSERT INTO reports (user_name_reported, post_id_reported, post_reported, user_name_reporter) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssss', $user_name_reported, $post_id_reported, $post_reported, $user_name_reporter);
            if (mysqli_stmt_execute($stmt)) {
                echo 'report_successful';
            } else {
                echo 'error: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo 'error_preparing_statement: ' . mysqli_error($koneksi);
        }
    }
    mysqli_close($koneksi);
    exit; // Hentikan eksekusi setelah menangani laporan
}

// Tangani aksi like dan dislike
if (isset($_POST['action']) && ($_POST['action'] === 'like' || $_POST['action'] === 'dislike')) {
    $post_id = mysqli_real_escape_string($koneksi, $_POST['post_id']);
    $user_name = mysqli_real_escape_string($koneksi, $_SESSION['user_name']);

    if ($_POST['action'] === 'like') {
        $query = "INSERT INTO likes (liked_post_id, liked_user_name) VALUES (?, ?)";
        $stmt = mysqli_prepare($koneksi, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'is', $post_id, $user_name);
            if (mysqli_stmt_execute($stmt)) {
                echo 'like_successful';
            } else {
                echo 'error: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo 'error_preparing_statement: ' . mysqli_error($koneksi);
        }
    } elseif ($_POST['action'] === 'dislike') {
        $query = "DELETE FROM likes WHERE liked_post_id = ? AND liked_user_name = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'is', $post_id, $user_name);
            if (mysqli_stmt_execute($stmt)) {
                echo 'dislike_successful';
            } else {
                echo 'error: ' . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo 'error_preparing_statement: ' . mysqli_error($koneksi);
        }
    }
    mysqli_close($koneksi);
    exit; // Hentikan eksekusi setelah menangani aksi like/dislike
}

// Query untuk mengambil data gambar dan informasi pengguna
if (isset($_GET['post_id'])) {
    $postId = mysqli_real_escape_string($koneksi, $_GET['post_id']);
    $query = "SELECT users.user_name, users.name, users.user_profile_path, posts.* 
              FROM posts 
              JOIN users ON posts.user_id = users.user_id 
              WHERE post_id = '$postId'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Hitung jumlah like
        $queryLikes = "SELECT COUNT(*) as like_count FROM likes WHERE liked_post_id = '$postId'";
        $resultLikes = mysqli_query($koneksi, $queryLikes);
        $likeCount = 0;
        if ($resultLikes) {
            $rowLikes = mysqli_fetch_assoc($resultLikes);
            $likeCount = $rowLikes['like_count'];
        }

        // Cek apakah pengguna sudah menyukai gambar ini
        $isLiked = false;
        if (isset($_SESSION['user_name'])) {
            $user_name = $_SESSION['user_name'];
            $queryCheckLike = "SELECT 1 FROM likes WHERE liked_post_id = '$postId' AND liked_user_name = '$user_name'";
            $resultCheckLike = mysqli_query($koneksi, $queryCheckLike);
            if ($resultCheckLike && mysqli_num_rows($resultCheckLike) > 0) {
                $isLiked = true;
            }
        }

        $queryTime = "SELECT current_timestamp() as timenow;";
        $resultTime = mysqli_query($koneksi, $queryTime);
        $rowTime = mysqli_fetch_assoc($resultTime);

        $postingTime = strtotime($row['create_in']);
        $currentTime = strtotime($rowTime['timenow']);
        $timeDifference = $currentTime - $postingTime;

        if ($timeDifference < 60) {
            $timeAgo = "Baru saja";
        } elseif ($timeDifference < 3600) {
            $minutes = floor($timeDifference / 60);
            $timeAgo = "$minutes mnt";
        } elseif ($timeDifference < 86400) {
            $hours = floor($timeDifference / 3600);
            $timeAgo = "$hours j";
        } elseif ($timeDifference < 2592000) {
            $days = floor($timeDifference / 86400);
            $timeAgo = "$days hri";
        } elseif ($timeDifference < 31536000) {
            $months = floor($timeDifference / 2592000);
            $timeAgo = "$months bln";
        } else {
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
    <script>
        // Fungsi untuk menangani pengiriman laporan
        function reportImage() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var user_name_reported = '<?php echo $row['user_name']; ?>';
            var post_id_reported = '<?php echo $row['post_id']; ?>';
            var post_reported = '<?php echo $row['post_title']; ?>';
            var user_name_reporter = '<?php echo $_SESSION['user_name']; ?>';

            xhr.send('action=report&user_name_reported=' + encodeURIComponent(user_name_reported) +
                      '&post_id_reported=' + encodeURIComponent(post_id_reported) +
                      '&post_reported=' + encodeURIComponent(post_reported) +
                      '&user_name_reporter=' + encodeURIComponent(user_name_reporter));

            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'report_successful') {
                        alert('Laporan berhasil dikirim');
                    } else if (xhr.responseText === 'already_reported') {
                        alert('Anda sudah membuat laporan untuk data postingan ini.');
                    } else {
                        alert('Terjadi kesalahan. Coba lagi nanti.');
                    }
                } else {
                    alert('Terjadi kesalahan. Coba lagi nanti.');
                }
            };
        }

        // Fungsi untuk menangani aksi like dan dislike
        function toggleLike() {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var post_id = '<?php echo $row['post_id']; ?>';
            var action = '<?php echo $isLiked ? 'dislike' : 'like'; ?>';

            xhr.send('action=' + action + '&post_id=' + encodeURIComponent(post_id));

            xhr.onload = function () {
                if (xhr.status === 200) {
                    if (xhr.responseText === 'like_successful' || xhr.responseText === 'dislike_successful') {
                        location.reload();
                    } else {
                        alert('Terjadi kesalahan. Coba lagi nanti.');
                    }
                } else {
                    alert('Terjadi kesalahan. Coba lagi nanti.');
                }
            };
        }
    </script>
    <style>
        /* LIKE BUTTON */
        .like-container {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .like-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            margin-right: 5px;
            font-size: 24px;
            display: flex;
            align-items: center;
        }

        .like-button i {
            transition: color 0.3s ease;
        }

        .like-button i.bx.bxs-heart {
            color: red;
        }

        .like-button i.bx.bx-heart {
            color: black;
        }

        .like-container span {
            font-size: 16px;
            color: #333;
            line-height: 24px; /* Sesuaikan dengan font-size tombol untuk memastikan teks berada di tengah */
        }

        /* POPUP MENU */
        /* .menu-btn {
            display: inline-block;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            margin: 20px;
        } */

        .menu-button {
            /* Style dasar tombol */
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px;
            font-size: 1.5em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.1s ease-in-out; /* Transisi halus untuk transformasi */
            font-size: 24px;
        }

        .menu-button:active {
            transform: scale(0.9); /* Skala tombol sedikit lebih kecil saat ditekan */
        }


        .popup {
            display: none;
            position: absolute;
            top: 50px;
            right: 20px;
            width: 200px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        
        .popup ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .popup li {
            /* Menghapus garis batas */
            border-bottom: none;
        }
        
        .popup a,
        .popup button {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            font-size: 14px; /* Ukuran font yang lebih kecil */
            transition: background-color 0.3s, box-shadow 0.3s; /* Transisi halus untuk efek hover */
            border: none; /* Menghapus border dari button */
            background: none; /* Menghapus latar belakang default dari button */
            text-align: left; /* Memastikan teks pada button di-align kiri */
            width: 100%; /* Memastikan button memenuhi lebar li */
            font-family: "Segoe UI", Arial, sans-serif;
            font-weight: bold; /* Make the font bold */
        }

        .popup a:hover,
        .popup button:hover {
            background-color: rgba(0, 0, 0, 0.1); /* Efek latar belakang pada hover */
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2); /* Bayangan pada hover */
            border-radius: 5px; /* Border-radius pada hover */
        }

        .popup.show {
            display: block;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
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
                <?php
                if (isset($_SESSION['user_name'])){
                    ?>
                    <div class="like-container">
                        <button class="like-button" onclick="toggleLike()">
                            <i class='<?php echo $isLiked ? 'bx bxs-heart' : 'bx bx-heart'; ?>' style='color:<?php echo $isLiked ? 'red' : 'black'; ?>'></i>
                        </button>
                        <span><?php echo $likeCount; ?></span>
                    </div>
                    <?php
                }else{
                    ?>
                    <div class="like-container">
                        <button class="like-button" onclick="needlogin()">
                            <i class='<?php echo $isLiked ? 'bx bxs-heart' : 'bx bx-heart'; ?>' style='color:<?php echo $isLiked ? 'red' : 'black'; ?>'></i>
                        </button>
                        <span><?php echo $likeCount; ?></span>
                    </div>
                    <?php
                }
                ?>
                <div class="button-group">
                    <!-- <button class="menu-button"><i class='bx bx-menu' ></i></button> -->
                    <button class="menu-button" id="menuBtn"><i class='bx bx-menu' ></i></button>
                    <div class="popup" id="popupMenu">
                        <ul>
                            <li><a href="../storage/posting/<?php echo $row['post_img_path']; ?>" download><i class='bx bxs-download'></i> Download gambar</i></a></li>
                            <li><a href="" id="copyButton"><i class='bx bx-copy' ></i> Copy Link</a></li>
                            <li><a href="" id="copyAddressButton"><i class='bx bx-copy' ></i> Copy Image Address</a></li>
                            <?php
                            if (isset($_SESSION['user_name'])){
                                ?>
                                <li>
                                    <a href="" id="reportButton" onclick="reportImage()"><i class='bx bx-flag' ></i> Laporkan gambar</a>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                            if (isset($_SESSION['user_name']) && $_SESSION['user_name'] == $row['user_name']) {
                                ?>
                                <i>
                                    <!-- <li><a href="" onclick="showConfirmationModal()">Hapus gambar</a></li> -->
                                    <button class="del-btn" onclick="showConfirmationModal()"><i class='bx bx-trash-alt' ></i> Hapus gambar</button>
                                </i>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="overlay" id="overlay"></div>

                    <script>
                        document.getElementById('menuBtn').addEventListener('click', function(event) {
                            event.stopPropagation();
                            var popup = document.getElementById('popupMenu');
                            var overlay = document.getElementById('overlay');
                            popup.classList.toggle('show');
                            overlay.classList.toggle('show');
                        });

                        document.getElementById('overlay').addEventListener('click', function() {
                            document.getElementById('popupMenu').classList.remove('show');
                            this.classList.remove('show');
                        });

                        document.addEventListener('click', function(event) {
                            var popup = document.getElementById('popupMenu');
                            var menuBtn = document.getElementById('menuBtn');
                            if (!popup.contains(event.target) && !menuBtn.contains(event.target)) {
                                popup.classList.remove('show');
                                document.getElementById('overlay').classList.remove('show');
                            }
                        });
                    </script>

                    <!-- TOMBOL PINDAH KE MENU -->
                    <!-- <?php
                    if (isset($_SESSION['user_name']) && $_SESSION['user_name'] == $row['user_name']) {
                        ?>
                        <button class="delete-button" onclick="showConfirmationModal()"><i class='bx bx-trash' ></i></button>
                        <?php
                    }
                    ?>
                    <a class="download-button" href="../storage/posting/<?php echo $row['post_img_path']; ?>" download>Download</i></a>
                    <button class="copy-link-button" id="copyButton"><i class='bx bx-link-alt'></i></button>
                    <?php
                    if (isset($_SESSION['user_name'])){
                        ?>
                        <button class="report-button" id="reportButton" onclick="reportImage()">
                            <i class='bx bxs-flag-alt'></i>
                        </button>
                        <?php
                    }
                    ?> -->
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
                <!-- <span class="close" onclick="closeConfirmationModal()">&times;</span> -->
                <p>Apakah Anda yakin ingin menghapus gambar ini?</p>
                <div class="button-container">
                    <button class="confirmButton" onclick="deleteImage()">Ya</button>
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
<script>
    function needlogin() {
        window.location.href = "../index.php?pesan=needlogin";
    }
</script>

<!-- COPY ALAMAT GAMBAR -->

<!-- Elemen input tersembunyi untuk menyimpan URL gambar -->
<input type="text" id="imageURL" style="position: absolute; left: -9999px;">

<script>
document.getElementById("copyAddressButton").addEventListener("click", function() {
    // Mendapatkan URL halaman
    var url = window.location.href;
    
    // Mendapatkan path gambar dari PHP
    var imgPath = "<?php echo '../storage/posting/' . $row['post_img_path']; ?>";
    
    // Menghapus 'pages/' dari URL
    var baseURL = url.replace(/\/pages\//, "/");
    
    // Menggabungkan URL yang diubah dengan path gambar
    var copyText = baseURL.substring(0, baseURL.lastIndexOf("/") + 1) + imgPath.replace("../", "");

    // Mengganti spasi dengan %20
    copyText = copyText.replace(/ /g, "%20");
    
    // Membuat elemen textarea yang tidak terlihat
    var textarea = document.createElement("textarea");
    textarea.value = copyText;
    document.body.appendChild(textarea);
    
    // Memilih teks dalam textarea
    textarea.select();
    textarea.setSelectionRange(0, 99999); // Untuk perangkat mobile
    
    // Menyalin teks yang dipilih ke clipboard
    document.execCommand("copy");
    
    // Menghapus elemen textarea yang dibuat
    document.body.removeChild(textarea);
    
    // Memberi tahu pengguna bahwa URL telah disalin
    alert("Link gambar berhasil disalin ke clipboard");
});
</script>



<script src="../script/alert-time.js"></script>
<script src="../script/copy-to-clipboard.js"></script>

</body>
</html>
