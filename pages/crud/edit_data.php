<?php
session_start();
include '../../koneksi.php';
include '../../environment.php';

$token = TOKEN_BOT;

if (isset($_SESSION['level_id'])) {
    if ($_SESSION['level_id'] == 1) {
        // khusus admin
        if (isset($_GET['page']) && isset($_GET['id'])) {
            $page = $_GET['page'];
            $id = $_GET['id'];
            if ($page == "users") {
                // Mendapatkan data pengguna berdasarkan ID
                $queryGetUser = "SELECT * FROM users WHERE user_id = $id";
                $resultGetUser = mysqli_query($koneksi, $queryGetUser);
                $userData = mysqli_fetch_assoc($resultGetUser);
                ?>
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Edit User</title>
                    <link rel="stylesheet" href="../../styles/alert.css">
                    <!-- <link rel="stylesheet" href="../../styles/admin_panel.css"> -->
                    <link rel="stylesheet" href="../../styles/style.css">
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
                    </style>
                </head>
                <body>
                    <div class="fixed-header">
                        <div class="left-menu">
                            <a href="../beranda.php">Beranda</a>
                            <a href="../../logout.php">Logout</a>
                        </div>
                        <div class="right-menu">
                            <a href="../admin_panel.php">Admin Panel</a>
                        </div>
                    </div>
                    <!-- <form method="POST" action="">
                        <input type="hidden" name="user_id" value="<?php echo $userData['user_id']; ?>">
                        <label for="user_name">Username</label>
                        <input type="text" name="user_name" value="<?php echo $userData['user_name']; ?>" required>
                        <label for="name">Name</label>
                        <input type="text" name="name" value="<?php echo $userData['name']; ?>" required>
                        <label for="bio">Bio</label>
                        <textarea name="bio" type="text"><?php echo $userData['user_bio']; ?></textarea>
                        <label for="password">Password</label>
                        <input type="text" name="password" value="<?php echo $userData['password']; ?>" required>
                        <label for="level_id">Level</label>
                        <select name="level_id" required>
                            <option value="1" <?php if ($userData['level_id'] == 1) echo 'selected'; ?>>Admin</option>
                            <option value="2" <?php if ($userData['level_id'] == 2) echo 'selected'; ?>>User</option>
                        </select>
                        <label for="chatID">Chat ID</label>
                        <input type="number" name="chatID" value="<?php echo $userData['tele_chat_id']; ?>">

                        <input type="submit" name="submit" value="Save Changes">
                    </form> -->
                    <main class="main_content">
                        <div class="upload_container">
                            <h2>Edit Data User</h2>
                            <br>
                            <form action="" method="POST" class="upload_form">
                                <div class="form_group">
                                    <input type="hidden" name="user_id" value="<?php echo $userData['user_id']; ?>">
                                </div>
                                <div class="form_group">
                                    <label for="user_name">Username</label>
                                    <input type="text" name="user_name" value="<?php echo $userData['user_name']; ?>" required>
                                </div>
                                <div class="form_group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" value="<?php echo $userData['name']; ?>" required>
                                </div>
                                <div class="form_group">
                                    <label for="bio">Bio</label>
                                    <textarea name="bio" type="text"><?php echo $userData['user_bio']; ?></textarea>
                                </div>
                                <div class="form_group">
                                    <label for="password">Password</label>
                                    <input type="text" name="password" value="<?php echo $userData['password']; ?>" required>
                                </div>
                                <div class="form_group">
                                <label for="level_id">Level</label>
                                    <select name="level_id" required>
                                        <option value="1" <?php if ($userData['level_id'] == 1) echo 'selected'; ?>>Admin</option>
                                        <option value="2" <?php if ($userData['level_id'] == 2) echo 'selected'; ?>>User</option>
                                    </select>
                                </div>
                                <div class="form_group">
                                    <label for="chatID">Chat ID</label>
                                    <input type="number" name="chatID" value="<?php echo $userData['tele_chat_id']; ?>">
                                </div>
                                <button type="submit" name="submit" value="Save Changes" class="upload_btn flex">
                                    <i class='bx bx-save' ></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </form>
                        </div>
                    </main>
                </body>
                </html>
                <?php
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
                    $user_id = $_POST['user_id'];
                    $user_name = $_POST['user_name'];
                    $name = $_POST['name'];
                    $bio = $_POST['bio'];
                    $password = $_POST['password'];
                    $level_id = $_POST['level_id'];
                    

                    // Ketika chatID kosong, maka set "0"
                    if (empty($_POST['chatID'])) {
                        $chatID = "0";
                    }else{
                        $chatID = $_POST['chatID'];
                    }

                    // Query dapatkan data dan cek apakah ChatID sudah digunakan oleh pengguna lain
                    $queryCheckChatID = "SELECT tele_chat_id FROM users WHERE tele_chat_id = $chatID AND user_id != $user_id";
                    $resultCheckChatID = mysqli_query($koneksi, $queryCheckChatID);

                    // jika chatID yang dicari adalah 0, maka ubah $countChatID menjadi 0
                    if ($chatID == 0) {
                        $countChatID = 0;
                    } else {
                        $countChatID = mysqli_num_rows($resultCheckChatID);
                    }
                    
                    if ($countChatID > 0) {
                        header("location:../admin_panel.php?pesan=chatidused"); // ChatID sudah digunakan maka kembali ke halaman admin_panel
                        exit();
                    }

                    // Update data pengguna
                    $queryUpdateUser = "UPDATE users SET user_name = '$user_name', name = '$name', user_bio = '$bio', password = '$password', level_id = '$level_id', tele_chat_id = $chatID WHERE user_id = $user_id";
                    $resultUpdateUser = mysqli_query($koneksi, $queryUpdateUser);

                    // ambil data terbaru dari penguna tersebut
                    $queryGetNewData = "SELECT * FROM users WHERE user_id = $user_id";
                    $resultGetNewData = mysqli_query($koneksi, $queryGetNewData);
                    $newData = mysqli_fetch_assoc($resultGetNewData);

                    $new_user_name = $newData['user_name'];
                    $new_name = str_replace(' ', '%20', $newData['name']);
                    $new_bio = $newData['user_bio'];
                    $new_level = $newData['level_id']; 
                    if ($chatID == 0) {
                        $new_chatID = "0";
                    } else {
                        $new_chatID = $newData['tele_chat_id'];
                    }
                    $new_chatID = $newData['tele_chat_id'];
                    $new_password = $newData['password'];

                    $message = "*Data%20anda%20telah%20diperbaharui%20oleh%20Admin!*%0A%0ANama:%20$new_name%0AUsername:%20$new_user_name%0ABio:%20$new_bio%0ALevel:%20$new_level%0APassword:%20$new_password%0AChat%20ID:%20$new_chatID.";

                    // $message = "*Data%20anda%20telah%20diperbaharui%20oleh%20Admin!*%0A%0A" .
                    //             "Nama:%20" . $new_name . "%0A" .
                    //             "Username:%20" . $new_user_name . "%0A" .
                    //             "Bio:%20" . $new_bio . "%0A" .
                    //             "Level:%20" . $new_level . "%0A" .
                    //             "Password:%20" . $new_password . "%0A" .
                    //             "Chat%20ID:%20" . $new_chatID . ".";
                    
                    // $_SESSION['level_id'] = $level_id;
                    
                    
                    // cek apablila user memiliki chat_id, maka lakukan pengiriman pesan
                    if ($chatID != 0) {
                        // init tele APi
                        $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=Markdown&chat_id=$chatID&text=$message";
                        $ch = curl_init();
                        // Set opsi cURL
                        curl_setopt($ch, CURLOPT_URL, $telegramAPI);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        // Tutup cURL
                        curl_close($ch);
                    }


                    if ($resultUpdateUser) {
                        
                        
                        // jika diri sendiri mengubah level_id, maka destroy session dan logout. Tetapi jika diri sendiri tidak mengubah level_id, maka session tetap ada
                        if ($_SESSION['user_id'] == $user_id) {
                            if ($_SESSION['level_id'] != $new_level) {
                                session_destroy();
                                header("location:../beranda.php?pesan=selflevelchanged");
                            }
                        }
                        header("location:../admin_panel.php?pesan=successupdateusers");
                    } else {
                        echo "<div class='alert'>Failed to update user data.</div>";
                    }
                }
            } else {
                header("location:../error/not_found.php");
            }
        } else {
            header("location:../error/not_found.php");
        }
    } else {
        header("location:../error/deniedpage.php");
    }
} else {
    header("location:../../index.php?pesan=needlogin");
}
?>
