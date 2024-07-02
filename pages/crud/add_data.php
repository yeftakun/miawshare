<?php
session_start();
include '../../koneksi.php';
include '../../environment.php';

$token = TOKEN_BOT;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Notification</title>
    <link rel="stylesheet" href="../../styles/alert.css">
    <link rel="stylesheet" href="../../styles/style.css">
    <link rel="stylesheet" type="text/css" href="../styles/admin_panel.css">

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
<?php

if (isset($_SESSION['level_id'])){
    if($_SESSION['level_id'] == 1){
        if (isset($_GET['page'])){
            $page = $_GET['page']; // mengambil nilai parameter page
            if ($page == "otp") {
                // bagian untuk otp
                ?>
                <?php

// Query untuk mendapatkan pengguna yang statusnya 'Nonaktif'
$queryGetUnactive = "SELECT * FROM users where status = 'Nonaktif'";
$result = mysqli_query($koneksi, $queryGetUnactive);
$count = mysqli_num_rows($result);

if ($count > 0) {
    // Membuat dropdown dengan pengguna yang statusnya 'Nonaktif'
    ?>
                    <main class="main_content">
                        <div class="upload_container">
                            <h2>Update OTP untuk registrasi</h2>
                            <br>
                            <form action="" method="POST" enctype="multipart/form-data" class="upload_form">
                                <div class="form_group">
                                    <label for="user_name">Username</label>
                                    <select name="user_name" id="user_name">
                                        <option value="none">None</option>
                                        <?php
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo '<option value="'.$row['user_name'].'">'.$row['user_name'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <button type="submit" name="submit" class="upload_btn flex" value="Generate OTP">
                                    <span>Generate OTP</span>
                                </button>
                            </form>
                        </div>
                    </main>
                    <?php
                    // echo "<main class='main_content'>";
                    // echo '<form method="POST" action="">';
                    // echo '<select name="user_name">';
                    // echo '<option value="none">None</option>';
                    // while ($row = mysqli_fetch_array($result)) {
                    //     echo '<option value="'.$row['user_name'].'">'.$row['user_name'].'</option>';
                    // }
                    // echo '</select>';
                    // echo '<input type="submit" name="submit" value="Generate OTP">';
                    // echo '</form>';

                    // Memproses formulir saat tombol submit ditekan
                    if (isset($_POST['submit'])) {
                        $user_name = $_POST['user_name'];
                        if ($user_name != 'none') {
                            // Mengambil data pengguna yang dipilih
                            $queryDeleteOldOtp = "DELETE FROM otp WHERE user_name = '$user_name'";
                            $resultDeleteOldOtp = mysqli_query($koneksi, $queryDeleteOldOtp);
                            $queryGetUser = "SELECT * FROM users WHERE user_name = '$user_name'";
                            $resultGetUser = mysqli_query($koneksi, $queryGetUser);
                            $userData = mysqli_fetch_assoc($resultGetUser);
                            $chatID = $userData['tele_chat_id'];
                            $name = $userData['name'];

                            // Membuat OTP 6 digit acak
                            $otp = mt_rand(100000, 999999);

                            $telegramAPIOTP = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=Halo,%20$name!.%0AKode%20OTP%20anda%20adalah:%20`$otp`";
                            // Mengirimkan OTP ke pengguna melalui Telegram
                            $ch = curl_init();
                            // Set opsi cURL
                            curl_setopt($ch, CURLOPT_URL, $telegramAPIOTP);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_exec($ch);
                            // Tutup cURL
                            curl_close($ch);

                            // Memasukkan user_name dan otp ke dalam tabel otp
                            $queryInsertOtp = "INSERT INTO otp (user_name, otp_code, to_use) VALUES ('$user_name', '$otp', 'register')";
                            $resultInsertOtp = mysqli_query($koneksi, $queryInsertOtp);

                            // Memeriksa apakah data berhasil dimasukkan
                            if ($resultInsertOtp) {
                                echo "<div class='done'>OTP terkirim di Telegram $user_name</div>";
                            } else {
                                echo "Failed to insert OTP.";
                            }
                        } else {
                            echo "Please select a user.";
                        }
                    }
                } else {
                    echo 'Tidak ada user yang baru mendaftar.';
                    ?>
                    <main class="main_content">
                        <div class="upload_container">
                            <h2>Update OTP untuk registrasi</h2>
                            <br>
                            <p>Tidak ada user baru yang mendaftar</p>
                        </div>
                    </main>
                    <?php
                }
            } else if ($page == "users") {
                // bagian untuk user
                ?>
                <h3>Add Users page</h3> 
                <?php
                if (isset($_GET['pesan'])) {
                    if ($_GET['pesan'] == 'chatidused') {
                        echo "<div class='alert'>ChatID sudah digunakan</div>";
                    }
                }
                ?>
                <p><a href="../admin_panel.php">Kembali</a></p>
                <?php

                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $user_name = $_POST['user_name'];
                    $name = $_POST['name'];
                    $bio = $_POST['bio'];
                    $password = $_POST['password'];
                    $level_id = $_POST['level_id'];
                    $status = 'Aktif'; // Akun yang ditambahkan admin dapat langsung dipakai user.
                    $defaultImage = 'default.png';
                    $chatID = $_POST['chatID'];
                    
                    // Cek apakah ada chatID yang diinputkan
                    if (empty($_POST['chatID'])) {
                        $chatID = "0";
                    } else {
                        $chatID = $_POST['chatID'];
                    }
                    
                    // Query untuk mendapatkan data dan cek apakah ChatID sudah digunakan oleh pengguna lain
                    $queryCheckChatID = "SELECT tele_chat_id FROM users WHERE tele_chat_id = $chatID";
                    $resultCheckChatID = mysqli_query($koneksi, $queryCheckChatID);
                    $countChatID = mysqli_num_rows($resultCheckChatID);
                    
                    if ($countChatID > 0) {
                        if ($chatID != 0) {
                            header("location:add_data.php?page=$page&pesan=chatidused"); // ChatID sudah digunakan maka kembali ke halaman admin_panel
                        exit();
                        }
                    }

                    // cek apakah user_name sudah ada
                    $queryCheckUsername = "SELECT COUNT(user_name) AS total FROM users WHERE user_name = '$user_name'";
                    $resultCheckUsername = mysqli_query($koneksi, $queryCheckUsername);
                    $row = mysqli_fetch_assoc($resultCheckUsername);
                    $totalUsername = $row['total'];

                    if ($totalUsername > 0) {
                        echo "<div class='alert'>Username sudah ada</div>";
                    } else {
                        $insertQuery = "INSERT INTO users (user_name, name, user_bio, password, level_id, status, user_profile_path, tele_chat_id) VALUES ('$user_name', '$name', '$bio', '$password', '$level_id', '$status', '$defaultImage', '$chatID')";
                        $result = mysqli_query($koneksi, $insertQuery);

                        if ($chatID != 0) {
                            // kirim notifikasi ke telegram
                            $queryTelegramID = "SELECT tele_chat_id FROM users WHERE user_name = '$user_name'";
                            $resultTelegramID = mysqli_query($koneksi, $queryTelegramID);
                            $rowTelegramID = mysqli_fetch_assoc($resultTelegramID);
                            $teleChatID = $rowTelegramID['tele_chat_id'];
                            
                            // format name
                            $nameFormated = str_replace(' ', '%20', $name);
                            $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=**Selamat%20datang%20di%20aplikasi%20kami,%20$nameFormated!**%0AAkun%20anda%20telah%20dibuat%20oleh%20admin%20dengan%20level%20$level_id.%0A%0AAnda%20dapat%20login%20dengan%0AUsername:%20`$user_name`%0APassword:%20`$password`.%0AAnda%20menerima%20pesan%20ini%20karena%20ChatID%20anda%20($chatID)%20terdaftar%20di%20sistem%20kami.";

                            $ch = curl_init();
                            // Set opsi cURL
                            curl_setopt($ch, CURLOPT_URL, $telegramAPI);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_exec($ch);
                            // Tutup cURL
                            curl_close($ch);

                            header("location:../admin_panel.php?pesan=successadduser");
                        } else {
                            header("location:../admin_panel.php?pesan=successadduser");
                        }
                    }
                }
                ?>
                <!-- <form method="POST" action="">
                    <label for="user_name">Username</label>
                    <input type="text" name="user_name" required>
                    <label for="name">Name</label>
                    <input type="text" name="name" required>
                    <label for="bio">Bio</label>
                    <textarea name="bio" type="text" placeholder="Bio user" require></textarea>
                    <label for="password">Password</label>
                    <input type="password" name="password" required>
                    <label for="level_id">Level</label>
                    <select name="level_id" required>
                        <option value="1">Admin</option>
                        <option value="2">User</option>
                    </select>
                    <label for="chatID">ChatID</label>
                    <input type="number" name="chatID" value=0>
                    <input type="submit" value="Add User"> -->
                    <main class="main_content">
                        <div class="upload_container">
                            <h2>Tambahkan Data User</h2>
                            <br>
                            <form action="" method="post" class="upload_form">
                                <div class="form_group">
                                    <label for="user_name">Username</label>
                                    <input type="text" name="user_name" required>
                                </div>
                                <div class="form_group">
                                    <label for="name">Name</label>
                                    <input type="text" name="name" required>
                                </div>
                                <div class="form_group">
                                    <label for="bio">Bio</label>
                                    <textarea name="bio" type="text" placeholder="Bio user" value="-" require></textarea>
                                </div>
                                <div class="form_group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" required>
                                </div>
                                <div class="form_group">
                                    <label for="level_id">Level</label>
                                    <select name="level_id" required>
                                        <option value="1">Admin</option>
                                        <option value="2">User</option>
                                    </select>
                                </div>
                                <div class="form_group">
                                    <label for="chatID">ChatID</label>
                                    <input type="number" name="chatID" value=0>
                                </div>
                                <button type="submit" value="Add User" class="upload_btn flex">
                                    <i class='bx bx-user-plus'></i>
                                    <span>Tambah Pengguna</span>
                                </button>
                            </form>
                        </div>
                    </main>
                <?php
            } else if ($page == "posts") {
                // bagian untuk post
                echo "<h1>Mendatang</h1>";
            } else if ($page == "notification") {
                // Mengirim notifikasi dengan target chat id user yang dipilih melalui dropdown
                $queryGetUsers = "SELECT * FROM users where tele_chat_id != '0'";
                $resultGetUsers = mysqli_query($koneksi, $queryGetUsers);
                ?>
                <main class="main_content">
                    <div class="upload_container">
                        <h2>Kirim Notifikasi</h2>
                        <br>
                        <form action="" method="POST" enctype="multipart/form-data" class="upload_form">
                            <div class="form_group">
                                <label for="user_name">Username</label>
                                <select name="user_name" id="user_name">
                                    <option value="none">None</option>
                                    <option value="all">To All</option>
                                    <?php
                                    while ($row = mysqli_fetch_array($resultGetUsers)) {
                                        echo '<option value="'.$row['user_name'].'">'.$row['user_name'].'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form_group">
                                <label for="message">Message</label>
                                <textarea name="message" type="text" placeholder="Message" require></textarea>
                            </div>
                            <button type="submit" name="submit" class="upload_btn flex" value="Send Notification">
                                <span>Kirim Notifikasi</span>
                            </button>
                        </form>
                    </div>
                </main>
                <?php

                // Memproses formulir saat tombol submit ditekan

                if (isset($_POST['submit'])) {
                    $user_name = $_POST['user_name'];
                    $message = $_POST['message'];
                    // Agar karakter khusus dapat dikirimkan melalui URL, karena parse_mode=markdown maka karakter pda format markdown tidak akan dianggap sebagai karakter khusus
                    $message = str_replace(" ", "%20", $message);
                    $message = str_replace("\\n", "%0A", $message); // untuk ini tidak perlu karena dibutuhkan pada pengetikan
                    $message = str_replace("*", "%2A", $message);
                    $message = str_replace("_", "%5F", $message);
                    $message = str_replace("[", "%5B", $message);
                    $message = str_replace("]", "%5D", $message);
                    $message = str_replace("(", "%28", $message);
                    $message = str_replace(")", "%29", $message);
                    $message = str_replace("~", "%7E", $message);
                    $message = str_replace("\\", "%5C", $message);

                    if ($user_name == 'all') {
                        // Mengambil semua chatID pengguna
                        $queryGetChatID = "SELECT tele_chat_id FROM users where tele_chat_id != '0'";
                        $resultGetChatID = mysqli_query($koneksi, $queryGetChatID);
                        while ($row = mysqli_fetch_array($resultGetChatID)) {
                            $chatID = $row['tele_chat_id'];
                            $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=$message";
                            // Mengirimkan notifikasi ke semua pengguna
                            $ch = curl_init();
                            // Set opsi cURL
                            curl_setopt($ch, CURLOPT_URL, $telegramAPI);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_exec($ch);
                            // Tutup cURL
                            curl_close($ch);
                        }
                        echo "<div class='done'>Notifikasi terkirim ke semua pengguna</div>";
                    } else {
                        // Mengambil chatID pengguna yang dipilih
                        $queryGetChatID = "SELECT tele_chat_id FROM users WHERE user_name = '$user_name'";
                        $resultGetChatID = mysqli_query($koneksi, $queryGetChatID);
                        $row = mysqli_fetch_assoc($resultGetChatID);
                        $chatID = $row['tele_chat_id'];
                        $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=$message";
                        // Mengirimkan notifikasi ke pengguna yang dipilih
                        $ch = curl_init();
                        // Set opsi cURL
                        curl_setopt($ch, CURLOPT_URL, $telegramAPI);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_exec($ch);
                        // Tutup cURL
                        curl_close($ch);
                        echo "<div class='done'>Notifikasi terkirim ke $user_name</div>";
                    }
                }
            } else {
                header("location:../error/not_found.php"); // jika parameter page tidak sesuai
                exit();
            }
        } else {
            header("location:../error/not_found.php"); // jika tidak ada parameter tambahan di header
            exit();
        }
    } else { // jika bukan admin
        header("location:../error/deniedpage.php");
        exit();
    }
} else {
    header("location:../error/not_found.php"); // jika level_id tidak ada di session
    exit();
}

?>
<script src="../../script/alert-time.js"></script>
</body>
</html>
