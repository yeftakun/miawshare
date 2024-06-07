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
                </head>
                <body>
                    <h3>Edit User</h3>
                    <a href="../admin_panel.php">Kembali</a>
                    <form method="POST" action="">
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
                    </form>
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
                    $chatID = $_POST['chatID'];

                    // Update data pengguna
                    $queryUpdateUser = "UPDATE users SET user_name = '$user_name', name = '$name', user_bio = '$bio', password = '$password', level_id = '$level_id', tele_chat_id = $chatID WHERE user_id = $user_id";
                    $resultUpdateUser = mysqli_query($koneksi, $queryUpdateUser);

                    // ambil data terbaru dari penguna tersebut
                    $queryGetNewData = "SELECT * FROM users WHERE user_id = $user_id";
                    $resultGetNewData = mysqli_query($koneksi, $queryGetNewData);
                    $newData = mysqli_fetch_assoc($resultGetNewData);

                    $new_name = $newData['name'];
                    $new_bio = $newData['user_bio'];
                    $new_level = $newData['level_id']; 
                    $new_chatID = $newData['tele_chat_id'];
                    $new_password = $newData['password'];

                    $message = "Data%20anda%20telah%20diperbaharui%20oleh%20Admin.%0A%0ANama:%20$new_name%0ABio:%20$new_bio%0ALevel:%20$new_level%0APassword:%20$new_password%0AChat%20ID:%20$new_chatID.";

                    // init tele APi
                    $url = "https://api.telegram.org/bot$token/sendMessage?chat_id=$chatID&text=$message";

                    $_SESSION['level_id'] = $level_id;

                    // kirim API
                    file_get_contents($url);

                    if ($resultUpdateUser) {
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
