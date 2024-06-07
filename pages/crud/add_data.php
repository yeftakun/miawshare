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
    <title>Document</title>
    <link rel="stylesheet" href="../../styles/alert.css">
</head>
<body>

    
    <?php


if (isset($_SESSION['level_id'])) {
    if ($_SESSION['level_id'] == 1) {
        // khusus admin
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if ($page == "level") {
                echo "<h1>Comming Soon</h1>";
            } elseif ($page == "otp") {
                // add data otp
                ?>
                <h1>Tambahkan OTP untuk registrasi</h1>
                <p><a href="../admin_panel.php">Kembali</a></p>
                <?php

                // Query untuk mendapatkan pengguna yang statusnya 'Nonaktif'
                $queryGetUnactive = "SELECT * FROM users where status = 'Nonaktif'";
                $result = mysqli_query($koneksi, $queryGetUnactive);
                $count = mysqli_num_rows($result);

                if ($count > 0) {
                    // Membuat dropdown dengan pengguna yang statusnya 'Nonaktif'
                    echo '<form method="POST" action="">';
                    echo '<select name="user_name">';
                    echo '<option value="none">None</option>';
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<option value="'.$row['user_name'].'">'.$row['user_name'].'</option>';
                    }
                    echo '</select>';
                    echo '<input type="submit" name="submit" value="Generate OTP">';
                    echo '</form>';

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

                            $telegramAPI = "https://api.telegram.org/bot$token/sendMessage?parse_mode=markdown&chat_id=$chatID&text=Halo,%20$name!.%0AKode%20OTP%20anda%20adalah:%20`$otp`";


                            // Mengirimkan OTP ke pengguna melalui Telegram
                            file_get_contents($telegramAPI);

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
                }

            } elseif ($page == "posts") {
                // add data posts
                echo "<h1>Comming Soon</h1>";
                ?>
                <h3>Add Post page</h3>
                <?php
            } elseif ($page == "users") {
                // add data users
                ?>
                <h3>Add Users page</h3> 
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

                    // cek apakah user_name sudah ada
                    $queryCheckUsername = "SELECT COUNT(user_name) AS total FROM users WHERE user_name = '$user_name'";
                    $resultCheckUsername = mysqli_query($koneksi, $queryCheckUsername);
                    $row = mysqli_fetch_assoc($resultCheckUsername);
                    $totalUsername = $row['total'];

                    if ($totalUsername > 0) {
                        echo "<div class='alert'>Username sudah ada</div>";
                    } else {
                        $insertQuery = "INSERT INTO users (user_name, name, user_bio, password, level_id, status, user_profile_path) VALUES ('$user_name', '$name', '$bio', '$password', '$level_id', '$status', '$defaultImage')";
                        $result = mysqli_query($koneksi, $insertQuery);

                        if ($result) {
                            header("location:../admin_panel.php?pesan=successadduser");
                        } else {
                            header("location:add_data.php?page=users&pesan=failedadduser");
                        }
                    }

                }
                ?>
                <form method="POST" action="">
                    <label for="user_name">Username</label>
                    <input type="text" name="user_name" required>
                    <label for="name">Name</label>
                    <input type="text" name="name" required>
                    <label for="bio">Bio</label>
                    <textarea name="bio" type="text" placeholder="Bio user"></textarea>
                    <label for="password">Password</label>
                    <input type="password" name="password" required>
                    <label for="level_id">Level</label>
                    <select name="level_id" required>
                        <option value="1">Admin</option>
                        <option value="2">User</option>
                    </select>
                    <input type="submit" value="Add User">
                <?php
        } else {
        header("location:../error/not_found.php");
    }
    } else {
    header("location:../error/deniedpage.php");
    // header("location:../../index.php?pesan=needlogin");
}
}
}
?>

</body>
</html>
