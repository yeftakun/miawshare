<?php

session_start();
include '../koneksi.php';
include '../environment.php';
$hostAPI = HOST_API;
$hostMain = HOST_MAIN;

// Fungsi untuk mengambil data konfigurasi dari file .env untuk izin deteksi
$queryConfig = "SELECT * FROM config";
$resultConfig = mysqli_query($koneksi, $queryConfig);
// ambil nilai kolom nsfw_detect
$nsfw_detect = mysqli_fetch_assoc($resultConfig)['nsfw_detect'];

// Start detection process
function startDetectionProcess() {
    global $koneksi, $hostAPI, $hostMain;

    $query = "SELECT p.post_id, p.post_img_path, u.user_name, p.post_title, u.to_suspend 
              FROM posts p 
              INNER JOIN users u ON p.user_id = u.user_id
              WHERE p.classify IN ('pending', 'error')";
    $result = mysqli_query($koneksi, $query);

    $totalPosts = mysqli_num_rows($result);
    if ($totalPosts === 0) {
        echo "<script>alert('Semua postingan telah dideteksi!'); window.location.href = 'admin_panel.php';</script>";
        exit;
    }

    $nsfwCount = 0;
    $sfwCount = 0;
    $processedCount = 0;

    while ($post = mysqli_fetch_assoc($result)) {
        $postId = $post['post_id'];
        $postImgPath = $post['post_img_path'];
        $userName = $post['user_name'];
        $postTitle = $post['post_title'];
        $filePath = $postImgPath;
        $url = $hostAPI . '/classify';
        $data = array('image_url' => $hostMain . '/storage/posting/' . $postImgPath);

        $options = array(
            'http' => array(
                'header'  => "Content-Type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode($data),
            ),
        );

        $context  = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $responseData = json_decode($response, true);

        if ($responseData['predicted_class'] == 'nsfw') {
            // Update post classify to nsfw
            $updateQuery = "UPDATE posts SET classify = 'nsfw' WHERE post_img_path = '$filePath'";
            mysqli_query($koneksi, $updateQuery);
            
            // cek apakah sudah direport
            $queryReport = "SELECT * FROM reports WHERE post_id_reported = $postId";
            $resultReport = mysqli_query($koneksi, $queryReport);
            if (mysqli_num_rows($resultReport) > 0) {
                continue;
            }else{
                // Insert report record
                $insertReportQuery = "INSERT INTO reports (user_name_reported, post_id_reported, post_reported, reason) 
                                      VALUES ('$userName', $postId, '$postTitle', 'nsfw - auto')";
                mysqli_query($koneksi, $insertReportQuery);
            }


            // Decrement to_suspend and update user status if needed
            $to_suspend = $post['to_suspend'] - 1;
            $decrementCount = "UPDATE users SET to_suspend = $to_suspend WHERE user_name = '$userName'";
            mysqli_query($koneksi, $decrementCount);

            if ($to_suspend <= 0) {
                $updateStatus = "UPDATE users SET status = 'Suspended' WHERE user_name = '$userName'";
                mysqli_query($koneksi, $updateStatus);
            }

            $nsfwCount++;
        } else if ($responseData['predicted_class'] == 'sfw') {
            // Update post classify to sfw
            $updateQuery = "UPDATE posts SET classify = 'sfw' WHERE post_img_path = '$filePath'";
            mysqli_query($koneksi, $updateQuery);

            $sfwCount++;
        } else {
            // Update post classify to error
            $updateQuery = "UPDATE posts SET classify = 'error' WHERE post_img_path = '$filePath'";
            mysqli_query($koneksi, $updateQuery);
        }

        // echo json_encode([
        //     'progress' => $progress,
        //     'completed' => $progress >= 100,
        //     'nsfwCount' => $nsfwCount,
        //     'sfwCount' => $sfwCount,
        // ]);

        // flush();
        // ob_flush();
        // usleep(500000); // Simulate time delay for processing
    }

    header('location:admin_panel.php?pesan=detected');
    exit;
    
}

// Handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $nsfw_detect == 1) {
    startDetectionProcess();
}

// query data level
$queryLevel = "SELECT * FROM level";
$resultLevel = mysqli_query($koneksi, $queryLevel);

// query data otp
$queryOTP = "SELECT * FROM otp";
$resultOTP = mysqli_query($koneksi, $queryOTP);

// query data posts
$queryPosts = "SELECT * FROM posts";
$resultPosts = mysqli_query($koneksi, $queryPosts);

// query data users
$queryUsers = "SELECT * FROM users";
$resultUsers = mysqli_query($koneksi, $queryUsers);

// query data report
$queryReport = "SELECT * FROM reports";
$resultReport = mysqli_query($koneksi, $queryReport);

$levelRowIsEmpty = "";


if(isset($_SESSION['level_id'])) {
    if($_SESSION['level_id'] == 1) {
        // beranda admin
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" type="text/css" href="../styles/alert.css">
    <link rel="stylesheet" type="text/css" href="../styles/modal.css">
    <link rel="stylesheet" type="text/css" href="../styles/admin_panel.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <!-- Boxicons CSS -->
    <link flex href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script type="text/javascript" src="js/gridviewscroll.js"></script>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            scroll-behavior: smooth; /* Menambahkan properti ini */
        }
        .fixed-header {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #333;
            color: #fff;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .fixed-header .left-menu a,
        .fixed-header .right-menu a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
            font-weight: bold;
            margin-right: 30px;
            size: 20px; /* Ukuran teks */
        }
        .container {
            margin-top: 70px; /* Adjust based on header height */
        }
        #goUpBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            z-index: 1000;
        }
    </style>
    <style>
        .panel-search-bar {
            display: flex;
            justify-content: flex-end; /* Mengatur search bar ke kanan */
            margin-bottom: 10px; /* Jarak antara search bar dan tombol "Clear Trash" */
        }

        .panel-search-bar form {
            margin-left: auto; /* Membuat form search mengambil ruang tersisa di sebelah kanan */
            margin-right: 50px; /* Jarak antara form search dan form "Clear Trash" */
        }

        form.clear-trash-form {
            margin-right: auto; /* Membuat form "Clear Trash" mengambil ruang tersisa di sebelah kiri */
            margin-left: 50px; /* Jarak antara form "Clear Trash" dan form search */
        }

        form.clear-trash-form input[type="submit"] {
            background-color: #ff0000; /* Warna latar belakang tombol */
            color: #fff; /* Warna teks tombol */
            border: none; /* Menghilangkan border tombol */
            padding: 10px 20px; /* Padding tombol */
            border-radius: 5px; /* Membuat tombol menjadi bentuk bulat */
            cursor: pointer; /* Mengubah kursor menjadi pointer saat diarahkan ke tombol */
        }

        form.clear-trash-form input[type="submit"]:hover {
            background-color: #cc0000; /* Warna latar belakang tombol saat dihover */
        }
        #modal2 { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        #modal-content2 { background: #fff; padding: 20px; border-radius: 5px; text-align: center; position: relative; }
        .spinner { border: 4px solid rgba(0, 0, 0, 0.1); border-left: 4px solid #007bff; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        #background-btn { position: absolute; top: 10px; right: 10px; background: #dc3545; color: #fff; border: none; padding: 5px 10px; cursor: pointer; border-radius: 5px; }
        #background-btn:hover { background: #c82333; }

        /* styleKonfigurasi */
        .show-conf {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
        }

        h2 {
            font-size: 24px;
            color: #007bff;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="fixed-header">
        <div class="left-menu">
            <a href="beranda.php">Beranda</a>
            <a href="../logout.php">Logout</a>
        </div>
        <div class="right-menu">
            <a href="#levelSection">Level</a>
            <a href="#otpSection">OTP</a>
            <a href="#reportSection">Report</a>
            <a href="#postsSection">Posts</a>
            <a href="#usersSection">Users</a>
        </div>
    </div>

    <br><br><br><br><br>
    
    <div id="modal2">
        <div id="modal-content2">
            <div class="spinner"></div>
            <p>Sedang mendeteksi postingan, jangan reload halaman ini!</p>
            <!-- <button id="background-btn" onclick="cancelDetection()"><i class='bx bx-window-close'></i></button> -->
        </div>
    </div>
    

    <?php
    if(isset($_GET['pesan'])) {
        if($_GET['pesan'] == "successadduser") {
            echo "<div class='done'>Berhasil menambahkan user.</div>";
        } else if($_GET['pesan'] == "successupdateusers") {
            echo "<div class='done'>Data user berhasil diubah.</div>";
        } else if($_GET['pesan'] == "success_delete") {
            echo "<div class='done'>Data berhasil dihapus.</div>";
        } else if($_GET['pesan'] == "failed_delete") {
            echo "<div class='alert'>Data gagal dihapus.</div>";
        } else if($_GET['pesan'] == "chatidused") {
            echo "<div class='alert'>Chat ID sudah digunakan oleh pengguna lain.</div>";
        } else if($_GET['pesan'] == "trash_file_deleted") {
            echo "<div class='done'>File tidak berguna sudah dihapus.</div>";
        } else if($_GET['pesan'] == "detected") {
            echo "<div class='done'>Postingan telah selesai dideteksi.</div>";
        } else if($_GET['pesan'] == "reconfig") {
            echo "<div class='done'>Konfigurasi berhasil diperbarui.</div>";
        } else if($_GET['pesan'] == "error_detect") {
            echo "<div class='alert'>Postingan tidak bisa dideteksi.</div>";
        }
    }
    ?>

    <div class="container">
        <div class="table-container">
            <div class="panel-search-bar">
                <form class="clear-trash-form" action="crud/delete_trash_file.php" method="post">
                    <input type="submit" name="clear_trash" value="Clear Trash">
                </form>
        
                <form action="" method="get">
                    <input type="text" name="search" id="searchData" placeholder="Cari" onkeyup="searchData(this.value)">
                </form>
            </div>
            <hr>
            <?php
            $queryPostsPending = "SELECT * FROM posts WHERE classify IN ('pending', 'error')";
            $resultPostsPending = mysqli_query($koneksi, $queryPostsPending);
            $totalPostsPending = mysqli_num_rows($resultPostsPending);
            ?>
            <h2>Jumlah postingan belum dideteksi: <?php echo $totalPostsPending; ?></h2>
            <button class="btn" onclick="startDetection()">Mulai deteksi</button>
            <hr>
            <h2>Konfigurasi Website</h2>
            <?php
            // jika data token bot ada di database (tdk null), tampilkan Available
            if (TOKEN_BOT != null) {
                echo "<p class='show-conf'>Token Bot: Available</p>";
            } else {
                echo "<p class='show-conf'>Token Bot: Not Available</p>";
            } 
            echo "<p class='show-conf'>Owner Chat ID: " . OWNER_CHAT_ID . "</p>";
            echo "<p class='show-conf'>Host MiawShare: " . HOST_MAIN . "</p>";
            echo "<p class='show-conf'>Host NSWF Detection API: " . HOST_API . "</p>";
            // Jika nsfw_detect = 0, tampilkan false
            if (NSFW_DETECT == 0) {
                echo "<p class='show-conf'>NSFW Detection: False</p>";
            } else {
                echo "<p class='show-conf'>NSFW Detection: True</p>";
            }
            echo "<p class='show-conf'>Max Image Size: " . MAX_IMAGE_SIZE . "</p>";
            echo "<p class='show-conf'>Max PP Size: " . MAX_PROFILE_IMAGE_SIZE . "</p>";
            echo "<p class='show-conf'>Limit Gambar ditampilkan (beranda): " . LIMIT_BERANDA . "</p>";
            ?>
            <button onclick="showUpdateModal()">Update</button>

            <!-- Modal for updating configuration -->
            <div id="updateModal" class="modal">
                <div class="modal-content">
                    <!-- <span class="close" onclick="closeUpdateModal()">&times;</span> -->
                    <button onclick="closeUpdateModal()">Batal</button>
                    <h2>Update Configuration</h2>
                    <form id="updateForm" action="crud/update_config.php" method="post">
                        <label for="tokenBot">Token Bot:</label>
                        <input type="text" id="tokenBot" name="tokenBot" value="<?php echo TOKEN_BOT; ?>"><br>

                        <label for="ownerChatId">Owner Chat ID:</label>
                        <input type="text" id="ownerChatId" name="ownerChatId" value="<?php echo OWNER_CHAT_ID; ?>"><br>

                        <label for="hostMain">Host MiawShare:</label>
                        <input type="text" id="hostMain" name="hostMain" value="<?php echo HOST_MAIN; ?>"><br>

                        <label for="hostAPI">Host NSWF Detection API:</label>
                        <input type="text" id="hostAPI" name="hostAPI" value="<?php echo HOST_API; ?>"><br>

                        <label for="nsfwDetect">NSFW Detection:</label>
                        <select id="nsfwDetect" name="nsfwDetect">
                            <option value="1" <?php echo (NSFW_DETECT == 1) ? 'selected' : ''; ?>>True</option>
                            <option value="0" <?php echo (NSFW_DETECT == 0) ? 'selected' : ''; ?>>False</option>
                        </select><br>

                        <label for="maxImageSize">Max Image Size:</label>
                        <input type="text" id="maxImageSize" name="maxImageSize" value="<?php echo MAX_IMAGE_SIZE; ?>"><br>

                        <label for="maxProfileImageSize">Max PP Size:</label>
                        <input type="text" id="maxProfileImageSize" name="maxProfileImageSize" value="<?php echo MAX_PROFILE_IMAGE_SIZE; ?>"><br>

                        <label for="limitBeranda">Limit Gambar ditampilkan (beranda):</label>
                        <input type="text" id="limitBeranda" name="limitBeranda" value="<?php echo LIMIT_BERANDA; ?>"><br>


                        <input type="submit" value="Update">
                    </form>
                </div>
            </div>

            <script>
                function showUpdateModal() {
                    document.getElementById('updateModal').style.display = 'block';
                }

                function closeUpdateModal() {
                    document.getElementById('updateModal').style.display = 'none';
                }

                window.onclick = function(event) {
                    var modal = document.getElementById('updateModal');
                    if (event.target == modal) {
                        modal.style.display = 'none';
                    }
                }
            </script>
        </div>
    </div>
    <div class="container">
        <div id="levelSection" class="table-container">
            <h2>Level Table</h2>
            <table id="levelTable" style="width:100%;border-collapse:collapse;">
                <?php
                if (mysqli_num_rows($resultLevel) > 0) {
                    echo "<tr class='GridViewScrollHeader'>";
                    echo "<th>ID</th><th>Name</th></tr>";
                    while($rowLevel = mysqli_fetch_assoc($resultLevel)) {
                        echo "<tr class='GridViewScrollItem'>";
                        echo "<td>".$rowLevel['level_id']."</td>";
                        echo "<td>".$rowLevel['level_name']."</td>";
                    }
                } else {
                    echo "<tr><td colspan='2'>Data tidak ditemukan</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <div class="container">
        <div id="otpSection" class="table-container">
            <h2>OTP Table</h2>
            <a href="crud/add_data.php?page=otp" class="btn">Add Data</a>
            <table id="otpTable" style="width:100%;border-collapse:collapse;">
                <?php
                if (mysqli_num_rows($resultOTP) > 0) {
                    echo "<tr class='GridViewScrollHeader'>";
                    echo "<th>User Name</th><th>OTP Code</th><th>For</th><th>Control</th></tr>";
                    while($rowOtp = mysqli_fetch_assoc($resultOTP)) {
                        echo "<tr class='GridViewScrollItem'>";
                        echo "<td>".$rowOtp['user_name']."</td>";
                        echo "<td>".$rowOtp['otp_code']."</td>";
                        echo "<td>".$rowOtp['to_use']."</td>";
                        echo "<td><a class='deleteBtn' data-id='".$rowOtp['id']."' data-page='otp'>Delete</a></td>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Data tidak ditemukan</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <div class="container">
        <div id="reportSection" class="table-container">
            <h2>Report Post Table</h2>
            <table id="reportTable" style="width:100%;border-collapse:collapse;">
                <?php
                if (mysqli_num_rows($resultReport) > 0) {
                    echo "<tr class='GridViewScrollHeader'>";
                    echo "<th>User Name</th><th>Post Reported</th><th>Reporter</th><th>Reason</th><th>Control</th></tr>";
                    while($rowReport = mysqli_fetch_assoc($resultReport)) {
                        echo "<tr class='GridViewScrollItem'>";
                        ?>
                        <td>
                            <a href="<?php echo "profile.php?user_name=", $rowReport['user_name_reported']; ?>">
                                <?php echo $rowReport['user_name_reported']; ?>
                            </a>
                        </td>
                        <?php
                        // echo "<td><a>".$rowReport['user_name_reported']."</a></td>";
                        // echo "<td>".$rowReport['post_id_reported']."</td>";
                        ?>
                        <td>
                            <a href="<?php echo "view_img.php?post_id=", $rowReport['post_id_reported']; ?>">
                                <?php echo $rowReport['post_reported'] . " (" . $rowReport['post_id_reported'] . ")"; ?>
                            </a>
                        <?php
                        // echo "<td>".$rowReport['post_reported']."</td>";
                        // echo "<td>".$rowReport['user_name_reporter']."</td>";
                        ?>
                        <td>
                            <a href="<?php echo "profile.php?user_name=", $rowReport['user_name_reporter']; ?>">
                                <?php echo $rowReport['user_name_reporter']; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo $rowReport['reason']; ?>
                        </td>
                        <?php
                        echo "<td><a class='deleteBtn' data-id='".$rowReport['report_id']."' data-page='report'>Biarin</a> | <a class='deleteBtn' data-id='".$rowReport['post_id_reported']."' data-page='posts'>Hapus Post</a></td>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Data tidak ditemukan</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <div class="container">
        <div id="postsSection" class="table-container">
            <h2>Posts Table</h2>
            <table id="postsTable" style="width:100%;border-collapse:collapse;">
                <?php
                if (mysqli_num_rows($resultPosts) > 0) {
                    echo "<tr class='GridViewScrollHeader'>";
                    echo "<th>ID</th><th>User ID</th><th>Post Title</th><th>Post Description</th><th>Post Link</th><th>Post Img Path</th><th>Post Date</th><th>Control</th></tr>";
                    while($rowPost = mysqli_fetch_assoc($resultPosts)) {
                        echo "<tr class='GridViewScrollItem'>";
                        echo "<td>".$rowPost['post_id']."</td>";
                        echo "<td>".$rowPost['user_id']."</td>";
                        echo "<td>".$rowPost['post_title']."</td>";
                        echo "<td>".$rowPost['post_description']."</td>";
                        echo "<td>".$rowPost['post_link']."</td>";
                        echo "<td>".$rowPost['post_img_path']."</td>";
                        echo "<td>".$rowPost['create_in']."</td>";
                        echo "<td><a href='view_img.php?post_id=".$rowPost['post_id']."' class='btn'>Lihat</a> | <a class='deleteBtn' data-id='".$rowPost['post_id']."' data-page='posts'>Delete</a></td>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Data tidak ditemukan</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>
    <div class="container">
        <div id="usersSection" class="table-container">
            <h2>Users Table</h2>
            <a href="crud/add_data.php?page=users" class="btn">Add Data</a>
            <a href="crud/add_data.php?page=notification" class="btn">Kirim Pesan</a>
            <table id="usersTable" style="width:100%;border-collapse:collapse;">
                <?php
                if (mysqli_num_rows($resultUsers) > 0) {
                    echo "<tr class='GridViewScrollHeader'>";
                    echo "<th>ID</th><th>User Name</th><th>Name</th><th>User Profile Path</th><th>User Bio</th><th>Level ID</th><th>Password</th><th>Status</th><th>To Suspend</th><th>Tele Chat ID</th><th>Control</th></tr>";
                    while($rowUser = mysqli_fetch_assoc($resultUsers)) {
                        echo "<tr class='GridViewScrollItem'>";
                        echo "<td>".$rowUser['user_id']."</td>";
                        // echo "<td><a href='profile?username=.$row['user_name'].'>".$rowUser['user_name']."</a></td>";
                        ?>
                        <td>
                            <a href="<?php echo "profile.php?user_name=", $rowUser['user_name']; ?>">
                                <?php echo $rowUser['user_name']; ?>
                            </a>
                        <?php
                        echo "<td>".$rowUser['name']."</td>";
                        echo "<td>".$rowUser['user_profile_path']."</td>";
                        echo "<td>".$rowUser['user_bio']."</td>";
                        echo "<td>".$rowUser['level_id']."</td>";
                        echo "<td>".$rowUser['password']."</td>";
                        echo "<td>".$rowUser['status']."</td>";
                        echo "<td>".$rowUser['to_suspend']."</td>";
                        echo "<td>".$rowUser['tele_chat_id']."</td>";
                        echo "<td><a href='crud/edit_data.php?page=users&id=".$rowUser['user_id']."' class='btn'>Edit</a> | <a class='deleteBtn' data-id='".$rowUser['user_id']."' data-page='users'>Delete</a></td>";
                    }
                } else {
                    echo "<tr><td colspan='10'>Data tidak ditemukan</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <!-- Modal konfirmasi penghapusan -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Apakah Anda yakin ingin menghapus data ini?</p>
            <button class="modalDelBtn" id="confirmDelete">Delete</button>
        </div>
    </div>

    <button id="goUpBtn"><i class='bx bx-up-arrow-alt'></i></button>

    <script src="../script/alert-time.js"></script>
    <script>
        // Fungsi untuk menampilkan modal konfirmasi penghapusan
        function showModal(id, page) {
            var modal = document.getElementById("deleteModal");
            var confirmBtn = document.getElementById("confirmDelete");
            modal.style.display = "block";

            // Ketika tombol delete pada modal diklik, kirim permintaan penghapusan
            confirmBtn.onclick = function() {
                // Kirim permintaan penghapusan ke server melalui AJAX
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4) {
                        // Tampilkan pesan hasil penghapusan atau perbarui tampilan tabel
                        // Misalnya, muat ulang halaman setelah penghapusan selesai
                        if (xhr.status == 200) {
                            window.location.reload();
                        } else {
                            alert("Gagal menghapus data: " + xhr.responseText);
                        }
                    }
                };
                xhr.open("GET", "crud/delete_data.php?page=" + page + "&id=" + id, true);
                xhr.send();
            }
        }

        // Tambahkan event listener untuk tombol delete pada setiap baris tabel
        var deleteBtns = document.querySelectorAll(".deleteBtn");
        deleteBtns.forEach(function(btn) {
            btn.addEventListener("click", function() {
                var id = this.getAttribute("data-id");
                var page = this.getAttribute("data-page");
                showModal(id, page);
            });
        });


        // Fungsi untuk menutup modal saat tombol close diklik
        var closeBtn = document.querySelector(".close");
        closeBtn.onclick = function() {
            var modal = document.getElementById("deleteModal");
            modal.style.display = "none";
        }

        // Fungsi untuk menutup modal saat area di luar modal diklik
        window.onclick = function(event) {
            var modal = document.getElementById("deleteModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        </script>
    <script>
            // Fungsi untuk melakukan pencarian dan menampilkan hanya baris yang sesuai
            function searchData(input) {
                // Konversi input menjadi huruf kecil untuk pencocokan yang tidak case-sensitive
                var searchText = input.toLowerCase();
                // Temukan semua tabel
                var tables = document.querySelectorAll("table");
                tables.forEach(function(table) {
                    // Temukan semua baris dalam tabel
                    var rows = table.getElementsByTagName("tr");
                    // Loop melalui semua baris, mulai dari 1 untuk melewati baris header
                    for (var i = 1; i < rows.length; i++) {
                        var row = rows[i];
                        // Dapatkan semua sel dalam baris
                        var cells = row.getElementsByTagName("td");
                        var found = false;
                        // Loop melalui semua sel dalam baris
                        for (var j = 0; j < cells.length; j++) {
                            var cell = cells[j];
                            // Periksa apakah teks dalam sel cocok dengan input pencarian
                            if (cell.textContent.toLowerCase().indexOf(searchText) > -1) {
                                found = true;
                                break; // Pergi ke baris berikutnya jika pencocokan ditemukan
                            }
                        }
                        // Tampilkan atau sembunyikan baris sesuai dengan hasil pencarian
                        if (found) {
                            row.style.display = ""; // Tampilkan baris
                        } else {
                            row.style.display = "none"; // Sembunyikan baris
                        }
                    }
                });
            }

            // Tambahkan event listener untuk memanggil fungsi pencarian saat nilai input berubah
            var searchInput = document.getElementById("searchData");
            searchInput.addEventListener("input", function() {
                searchData(this.value);
            });
        </script>
        <script>

        // Show/hide "Go Up" button
        window.onscroll = function() {
            let goUpBtn = document.getElementById('goUpBtn');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                goUpBtn.style.display = 'block';
            } else {
                goUpBtn.style.display = 'none';
            }
        };

        // Scroll to top when "Go Up" button is clicked
        document.getElementById('goUpBtn').onclick = function() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        };
    </script>
    <script>
        // Smooth scrolling untuk link yang mengarah ke bagian tertentu
        document.querySelectorAll('.right-menu a').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Show/hide "Go Up" button
        window.onscroll = function() {
            let goUpBtn = document.getElementById('goUpBtn');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                goUpBtn.style.display = 'block';
            } else {
                goUpBtn.style.display = 'none';
            }
        };

        // Scroll to top when "Go Up" button is clicked
        document.getElementById('goUpBtn').onclick = function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        };

    </script>
    <script>
        function startDetection() {
            document.getElementById('modal2').style.display = 'flex';
            fetch('admin_panel.php', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.completed) {
                        document.getElementById('modal2').style.display = 'none';
                        // alert('Postingan telah selesai dideteksi!');
                    } else {
                        // Handle progress update if needed
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modal2').style.display = 'none';
                });
        }

        function cancelDetection() {
            document.getElementById('modal2').style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === document.getElementById('modal2')) {
                event.preventDefault();
            }
        };
    </script>
</body>
</html>

<?php
} else {
    header('Location:error/deniedpage.php');
}
}else{
    header('Location:../index.php?pesan=needlogin');
}
?>
